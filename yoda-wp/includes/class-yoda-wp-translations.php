<?php
  require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
  use GitElephant\Repository;

/**
 * Used to handle all Git/Bitbucket translations operations
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 */

/**
 * This class defines all code necessary to handle the wizard/announcement translations management.
 *
 * @since      1.0.0
 * @package    Yoda_WP
 * @subpackage Yoda_WP/includes
 * @author     Brian Herold <bmherold@gmail.com>>
 */
class Yoda_WP_Translations {

  const TEMP_FOLDER = 'tmp/yoda-translations-repo';
  const LOCALES_DEFAULT_DIR = 'locales';

  private $repository;
  private $localesDir;

	public function __construct($repositoryUrl, $localesDir = false) {
    $this->localesDir = $localesDir ? $localesDir : self::LOCALES_DEFAULT_DIR;
    $this->clone_repository($repositoryUrl);

	}

  public function clone_repository($repositoryUrl) {

    $tempFolder = plugin_dir_path( dirname( __FILE__ ) ) . self::TEMP_FOLDER;

    if (!file_exists($tempFolder)) {
      mkdir($tempFolder, 0755, true);
    }

    $this->repository = new Repository($tempFolder);

    $repoExists = false;
    try {
      $this->repository->getStatus();
      $repoExists = true;
    } catch (Exception $e) {
      $repoExists = false;
    }

    try{
      $this->repository->pull(); // make sure we're up to date!
    } catch (Exception $e) {
      error_log("BROKE ON REPO PULL, {$e->getMessage()}");
      throw new Exception("BROKE ON REPO PULL, {$e->getMessage()}");
    }

    if (!$repoExists) {
      $this->repository->cloneFrom($repositoryUrl, $tempFolder);
    }
  }

  public function update_repository($post_id, $post_title, $post_content) {
    $post = get_post($post_id);
    $localesFolder = plugin_dir_path( dirname( __FILE__ ) ) . self::TEMP_FOLDER . '/' . $this->localesDir;
    $englishJsonFilePath = $localesFolder . '/en.json';

    $englishJsonString = file_get_contents($englishJsonFilePath);
    try {
      $englishJson = json_decode($englishJsonString, true);
    } catch (Exception $e) {
      throw new Exception("There were issues decoding the en.json locale. Fix the Yoda git translations repository.");
      // TODO - email/notify someone who cares to fix the en.json formatting
    }

    $englishJson[$post_id] = [
      'TITLE' => $post_title,
      'CONTENT' => $post_content
    ];

    try {
      $englishJsonString = json_encode($englishJson);
    } catch (Exception $e) {
      throw new Exception("There were issues encoding the en.json locale. There may be a problem with your title or content.");
    }

    file_put_contents($englishJsonFilePath, $englishJsonString);

    try {
      $didCommitChanges = $this->commit_repo_changes($post->post_type, $post_id);
    } catch (Exception $e) {
      throw new Exception("There was a problem with staging/committing/pushing to the Yoda git translations repository. {$e->getMessage}");
    }

    return $didCommitChanges;
  }

  public function commit_repo_changes($post_type, $post_id) {
    $status = $this->repository->getStatus();

    if ($status->modified()->count() == 0) {
      return false;
    }

    $commit_message = "English language updated via Wordpress for {$post_type} {$post_id}.";
    $this->repository->stage();
    $commit = $this->repository->commit($commit_message);
    $this->repository->push();

    return $this->repository->getStatus();
  }

  public function sync_post_translations() {
    $localesFolder = plugin_dir_path( dirname( __FILE__ ) ) . self::TEMP_FOLDER . '/' . $this->localesDir;

    $combinedJson = [];

		foreach (glob("{$localesFolder}/*.json") as $file) {
      $file_parts = pathinfo($file);
      $lang = $file_parts['filename'];
      $jsonString = file_get_contents($file);

      if ($lang == 'en') {
        continue; // skip sync english translations - they will be natively in the wordpress
      }

      try {
        $langJson = json_decode($jsonString, true);
      } catch (Exception $e) {
        throw new Exception("There were issues decoding the {$lang} locale. Fix the Yoda git translations repository.");
        // TODO - email/notify someone who cares to fix the en.json formatting
      }

      foreach($langJson as $post_id => $post){
        if (!isset($combinedJson[$post_id])) {
          $combinedJson[$post_id] = [];
        }

        $combinedJson[$post_id][$lang] = $post;
      }

    }

    foreach ($combinedJson as $post_id => $translations) {
      update_post_meta( $post_id, 'translations', $translations );
    }

    return true;
  }
}