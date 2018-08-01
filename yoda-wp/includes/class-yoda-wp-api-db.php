<?php

/**
 * Abstractions for any necessary custom DB queries
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 */

/**
 * This class defines all the custom DB query functions.
 *
 * @since      1.0.0
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 * @author     Brian Herold <bmherold@gmail.com>
 */
class Yoda_WP_API_DB {


	/*
		PUBLIC METHODS -----------------------------------------------
	*/
	public function __construct() {
		$this->load_dependencies();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-yoda-wp-admin.php';
	}

	public function get_posts() {
		return $this->queryPosts(['post_type' => ['wizard', 'post', 'page', 'announcement']], true);
	}

	public function get_guides($route, $permissions, $user_id, $locale, $use_dummy_data = false) {
		if ($use_dummy_data == 'guides') {
			return $this->getDummyGuideData();
		}

		$query = [
			'post_type' => ['announcement', 'wizard'],
			'post_status' => 'publish',
			'orderby' => 'ID',
			'order' => 'ASC',
		];

		if ($route) {
			error_log('[Filtering Guides by Route]: ' . $route);
			$query = array_merge($query, [
				'meta_query' => [
					'relation' => 'OR',
					[
						'key'     => 'announcement-url',
						'value'   => $route,
						'compare' => 'LIKE'
					],
					[
						'key'     => 'wizard-url',
						'value'   => $route,
						'compare' => 'LIKE'
					]
				]
			]);
		}

		// Get the guides!
		$guides = $this->queryPosts($query, true);

		if ($user_id) {
			error_log('[Filtering Completed Guides]: for user_id: ' . $user_id);
			$guides = $this->filterCompleteGuides($guides, $user_id);
		}

		foreach($guides as $guide) {
			$translations = self::getGuideAvailableTranslations($guide, (bool)$use_dummy_data);
			$availableTranslations = $translations ? array_keys($translations) : false;
			error_log('Guide "'.$guide->post_title.'" has these available translations: ' . print_r($availableTranslations, true));
		}

		$DEFAULT_LOCALE = 'en-us';
		if ($locale != $DEFAULT_LOCALE) {
			$guides = $this->translateGuides($guides, $locale, (bool)$use_dummy_data);
		}

		return $this->mapPostsToOutputSchema($guides);
	}

	public function getGuide($guide_id) {
		error_log('[finding guide ' . $guide_id.']');
		$guide = get_post($guide_id);
		error_log('-------------------------');
		error_log(print_r($guide, true));

		if ($guide) {
			return $guide;
		} else {
			return false;
		}
	}

	public function markGuideComplete($guide_id, $user_id) {
		error_log('user_id ' . $user_id);
		global $wpdb;

		$table_guides_completed = $wpdb->prefix . Yoda_WP_Admin::TABLE_GUIDES_COMPLETED;

		$record = $wpdb->get_row( "SELECT * FROM $table_guides_completed WHERE guide_id = $guide_id and user_id = '$user_id'", ARRAY_A );

		error_log(print_r($record, true));

		if ($record) {
			error_log('GUIDE ALREADY COMPLETE');
			return $record;
		} else {
			error_log("INSERTING GUIDE with $user_id");
			$wpdb->insert(
				$table_guides_completed,
				[
					'guide_id' => $guide_id,
					'user_id' => $user_id,
					'completed_on' => current_time( 'mysql' )
				],
				[	'%d',	'%s', '%s' ]
			);

			return $wpdb->get_row( "SELECT * FROM $table_guides_completed WHERE id = {$wpdb->insert_id}", ARRAY_A );
		}

	}


	/*
		PRIVATE METHODS -----------------------------------------------
	*/

	private function translateGuides($guides, $locale, $use_dummy_data = false) {
		error_log("[Translating Guides]: to '$locale'");
		if ($use_dummy_data) { error_log("-------------- WITH DUMMY TRANSLATIONS DATA --------------"); }
		$locale = strtolower($locale);
		$translatedGuides = array();

		foreach($guides as $guide) {
			error_log("[Translating Guide]: " . $guide->post_title);

			$translations = self::getGuideAvailableTranslations($guide, $use_dummy_data);
			$localeData = ($translations && isset($translations[$locale])) ? $translations[$locale] : false;
			if (!$localeData) {
				error_log("- skipping translation, missing desired locale");
				$translatedGuides[] = $guide;
				continue; // break if we don't have the requested translations data
			}

			error_log("- translated");
			$translatedGuides[] = $this->translateGuide($guide, $localeData);
		}

		return $translatedGuides;
	}

	private function translateGuide($guide, $localeData) {
		error_log(print_r($localeData, true));

		switch ($guide->post_type) {
			case 'announcement':
				$guide->post_title = $localeData['TITLE']; // will be same as $localeData['STEPS']['1']['CONTENT'] for announcements
				$guide->post_content = $localeData['CONTENT'];
				break;

				case 'wizard':
					$steps = unserialize(current($guide->meta['wizard-steps-repeater']));

					$translatedSteps = array_map(function($step, $i) use ($localeData) {
						$stepIdx = $i;

						return array_merge($step, [
							'step-title' => isset($localeData['STEPS']["$stepIdx"]["TITLE"]) ?
								$localeData['STEPS']["$stepIdx"]["TITLE"] : (isset($step['step-title']) ? $step['step-title'] : ''),
							'stepContent' => isset($localeData['STEPS']["$stepIdx"]["CONTENT"]) ?
								$localeData['STEPS']["$stepIdx"]["CONTENT"] : $step['stepContent'],
						]);
					}, $steps, array_keys($steps));

					$guide->post_title = $localeData['TITLE'];
					$guide->meta['wizard-steps-repeater'] = [maybe_serialize($translatedSteps)]; // wrap element in array for
					break;
		}

		return $guide;
	}

	private function filterCompleteGuides($guides, $user_id) {
		global $wpdb;
		$table_guides_completed = $wpdb->prefix . Yoda_WP_Admin::TABLE_GUIDES_COMPLETED;

		$completed_guide_ids = $wpdb->get_col( $wpdb->prepare(
			"
				SELECT      guide_id
				FROM        $table_guides_completed
				WHERE       user_id = %s
			",
			$user_id
		) );

		error_log('COMPLETED GUIDE IDS: ' . print_r($completed_guide_ids, true));

		$filtered_guides = array_filter($guides, function($guide) use ($completed_guide_ids) {
			$is_show_once = $this->getGuideShowOnceValue($guide);
			if (!$is_show_once) {
				return true;
			} else {
				return !in_array($guide->ID, $completed_guide_ids);
			}
		});

		$clean_array = array();
		foreach($filtered_guides as $x) {
			$clean_array[] = $x;
		}

		return $clean_array;
	}

	private function getGuideShowOnceValue($guide) {
		$guide = $guide->to_array();
		switch ($guide['post_type']) {
			case 'announcement':
				if (isset($guide['meta']['announcement-show-once'])) {
					return current($guide['meta']['announcement-show-once']);
				}
				return false;
				break;

			case 'wizard':
				if (isset($guide['meta']['wizard-show-once'])) {
					return current($guide['meta']['wizard-show-once']);
				}
				return false;
				break;

			default:
				return false;
				break;
		}
	}

	private function mapPostsToOutputSchema($posts) {
		// error_log(print_r($posts,true));

		return array_map(function($x) {
			$x = $x->to_array();

			switch ($x['post_type']) {
				case 'announcement':
					return [
						'id' => $x['ID'],
						'title' => $x['post_title'],
						'url' => isset($x['meta']['announcement-url']) ? current($x['meta']['announcement-url']) : '',
						'steps' => [[
							'title' => $x['post_title'],
							'selector' => isset($x['meta']['announcement-selector']) ? current($x['meta']['announcement-selector']) : '',
							'content' => isset($x['post_content']) ? $x['post_content'] : '',
							]],
							'type' => $x['post_type'],
							'displayType' => isset($x['meta']['announcement-type']) ? current($x['meta']['announcement-type']) : '',
							'meta' => [
								'featureToggles' => isset($x['meta']['announcement-feature-toggles']) ? current($x['meta']['announcement-feature-toggles']) : '',
								'regions' => $this->getRegionFromMeta($x['meta'], 'announcement'),
								'permissions' => $this->getPermissionsFromMeta($x['meta'], 'announcement')
							],
							'created' => $x['post_date'],
						'updated' => $x['post_modified'],
					];

					break;

					case 'wizard':
						$steps = unserialize(current($x['meta']['wizard-steps-repeater']));
						return [
							'id' => $x['ID'],
							'title' => $x['post_title'],
							'url' => isset($x['meta']['wizard-url']) ? current($x['meta']['wizard-url']) : '',
							'steps' => array_map(function($s) {
								return [
									'title' => isset($s['step-title']) ? $s['step-title'] : '',
									'selector' => $s['step-selector'],
									'content' => isset($s['stepContent']) ? $s['stepContent'] : '',
								];
							}, $steps),
							'type' => $x['post_type'],
							'created' => $x['post_date'],
							'updated' => $x['post_modified'],
						];

						break;

				default:
					return $x;
					break;
			}
		}, $posts);
	}

	private function getRegionFromMeta($meta, $type) {
		$regionMeta = isset($meta["{$type}-region"]) ? unserialize(current($meta["{$type}-region"])) : false;
		$regions = $regionMeta ? unserialize($regionMeta) : false;
		return $regions ? array_keys($regions) : [];
	}

	private function getPermissionsFromMeta($meta, $type) {
		$permissionsString = isset($meta["{$type}-permissions"]) ? current($meta["{$type}-permissions"]) : [];
		return strlen($permissionsString) ? preg_split("/[\s,]+/", $permissionsString) : [];
	}

	private function queryPosts($options = [], $with_meta = false) {
    // $defaults = array(
		// 	'numberposts' => 5,
		// 	'category' => 0,
		//  'orderby' => 'date',
		// 	'order' => 'DESC',
		//  'include' => array(),
		// 	'exclude' => array(),
		//  'meta_key' => '',
		// 	'meta_value' =>'',
		//  'post_type' => 'post',
		// 	'suppress_filters' => true
		// );
		$post_results = get_posts($options);

		if ($with_meta) {
			foreach ($post_results as &$post) {
				$post->meta = get_post_meta($post->ID);
			}
		}

		return $post_results;
	}

	private function getDummyGuideData() {
		return [
			[
				"title" => "titsle",
				"steps" => [
					[
						"selector" => '.section-header',
						"content"  => '<div> WIZARDS</div>'
					],
					[
						"selector" => '.breadcrumbs',
						"content"  => '<div> WIZARDS EVERYWHERE </div>'
					]
				]
			]
		];
	}


	/*
		STATIC METHODS -----------------------------------------------
	*/

	public static function getGuideAvailableTranslations($guide, $use_dummy_data = false) {
		if (!$guide->meta && !$use_dummy_data) {
			$guide->meta = get_post_meta($guide->ID); // saturate with meta if it's not already there
		}

		$translationsMeta = $use_dummy_data ?
		self::getDummyTranslationsMeta() : (isset($guide->meta['translations']) ? $guide->meta['translations'] : false);

		if (!$translationsMeta) {
			return false;
		}

		return unserialize(current($translationsMeta));
	}

	private static function getDummyTranslationsMeta() {
		$translations = [
			"es" => [
				"TITLE" => "My SWEET Title (spanish)",
				"STEPS" => [
					"1" => [
						"TITLE" => "Step 1 title (spanish)",
						"CONTENT" => "Step 1 content (spanish)"
					],
					"2" => [
						"TITLE" => "Step 2 title (spanish)",
						"CONTENT" => "Step 2 content (spanish)"
					],
					"3" => [
						"TITLE" => "Step 3 title (spanish)",
						"CONTENT" => "Step 3 content (spanish)"
					]
				]
			]
		];

		return [maybe_serialize($translations)]; // make it look like wordpress serialized metadata
	}

}
