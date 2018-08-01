<?php
  require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

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

  const REPO_DIR = 'yoda-translations-repo';
  const LOCALES_DEFAULT_DIR = 'locales';
  const GIT_NAME = 'YODA.WP';
  const GIT_EMAIL = 'yoda-wp@noreply.genesys.com';

  private $repository;
  private $localesDir;

	public function __construct($repositoryUrl, $localesDir = false) {
    $this->localesDir = $localesDir ? $localesDir : self::LOCALES_DEFAULT_DIR;
    $this->clone_repository($repositoryUrl);
	}

  public function clone_repository($repositoryUrl) {

    $repoDirPath = $this->get_repo_dir();

    if (!file_exists($repoDirPath)) {
      mkdir($repoDirPath, 0755, true);
    }

    $this->repository = new GitElephant\Repository($repoDirPath);
    $this->repository->addGlobalConfig('user.name', self::GIT_NAME);
    $this->repository->addGlobalConfig('user.email', self::GIT_EMAIL);

    $repoExists = false;
    try {
      $this->repository->getStatus();
      $repoExists = true;
    } catch (Exception $e) {
      $repoExists = false;
    }

    if (!$repoExists) {
      $this->repository->cloneFrom($repositoryUrl, $repoDirPath);
    }

    try{
      $this->repository->pull(); // make sure we're up to date!
    } catch (Exception $e) {
      throw new Exception("YODA.WP had a problem pulling changes from the translations repository: {$e->getMessage()}");
    }
  }

  private function get_repo_dir() {
    $upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    return $upload_dir . '/' . self::REPO_DIR;
  }

  private function get_repo_locales_dir() {
    return $this->get_repo_dir() . '/' . $this->localesDir;
  }

  public function update_repository($post_id, $post_title, $post_content) {
    $post = get_post($post_id);
    $localesFolder = $this->get_repo_locales_dir();
    $englishJsonFilePath = $localesFolder . '/en.json';
    $englishJsonString = file_get_contents($englishJsonFilePath);

    try {
      $englishJson = json_decode($englishJsonString, true);
    } catch (Exception $e) {
      $message = "There were issues decoding the en.json locale. Fix the YODA.WP git translations repository.";
      throw new Exception($message);
      $this->sendEmail('Translations file issue', $message);
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
    $didCommitChanges = $this->commit_repo_changes($post->post_type, $post_id);

    return $didCommitChanges;
  }

  public function commit_repo_changes($post_type, $post_id) {
    $status = $this->repository->getStatus();

    if ($status->modified()->count() == 0) {
      return false;
    }

    $commit_message = "English language updated via Wordpress for {$post_type} {$post_id}.";
    $this->repository->stage();
    $this->repository->commit($commit_message);
    $this->repository->push();

    return $this->repository->getStatus();
  }

  private function sendEmail($subject, $body) {
    $translations_contact_email = getenv('TRANSLATIONS_ISSUE_CONTACT_EMAIL');
    $to = $translations_contact_email;
    $subject = '[YODA.WP] ' . $subject;
    $body = $body;
    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail( $to, $subject, $body, $headers );
  }

  public function sync_post_translations() {
    $localesFolder = $this->get_repo_locales_dir();

    $combinedJson = [];

		foreach (glob("{$localesFolder}/*.json") as $file) {
      $file_parts = pathinfo($file);
      $lang = $file_parts['filename'];
      $jsonString = file_get_contents($file);

      if ($lang == 'en') {
        continue; // skip sync english translations - they will be natively in the wordpress post
      }

      try {
        $langJson = json_decode($jsonString, true);
      } catch (Exception $e) {
        $message = "There were issues decoding the {$lang} locale. Fix the YODA.WP git translations repository.";
        throw new Exception($message);
        $this->sendEmail('Translations file issue', $message);
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
      update_post_meta( $post_id, 'translations-last-sync', gmdate(DateTime::ISO8601, time()) );
    }

    return true;
  }
}