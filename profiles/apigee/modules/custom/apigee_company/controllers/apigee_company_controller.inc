<?php
/**
 * @file
 * The controller class for the Apigee Company Entity.
 */

use Apigee\ManagementAPI\Company;
use Apigee\Exceptions\ResponseException;

/**
 * Controller class for the apigee_company entity.
 */
class ApigeeCompanyController implements DrupalEntityControllerInterface, EntityAPIControllerInterface {

  const CACHE_LIFETIME_IN_MINUTES = 60;

  /**
   * The last exception thrown, if any.
   *
   * @var Apigee\Exceptions\ResponseException
   */
  protected static $lastException;


  /**
   * Gets the configuration for the requested org.
   *
   * @return \Apigee\Util\OrgConfig
   *   Configuration for the default org.
   */
  protected static function getConfig() {
    return devconnect_default_org_config();
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($entity_type) {
  }

  /**
   * {@inheritdoc}
   */
  public function create(array $values = array()) {
    // Add is_new property if it is not set.
    $values += array('is_new' => TRUE);
    $entity = new ApigeeCompanyEntity($values);
    $entity_type = 'apigee_company';
    // Allow other modules to alter the created entity.
    drupal_alter('apigee_company_entity_create', $entity_type, $entity);

    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function save($entity) {
    $config = self::getConfig();
    $company = new Company($config);

    // If the company createdAt field is not null, this is an update.
    $is_update = !empty($entity->createdAt);

    $attributes = $entity->attributes;
    if (!empty($attributes)) {
      foreach ($entity->attributes as $key => $value) {
        $company->setAttribute($key, $value);
      }
    }
    $company->setDisplayName($entity->displayName);
    $company->setName($entity->name);
    $company->setStatus($entity->status);

    try {
      $company->save($is_update);
      $this->addToCache($company);
    }
    catch (ResponseException $e) {
      self::$lastException = $e;
      return FALSE;
    }

    return ($is_update ? SAVED_UPDATED : SAVED_NEW);
  }

  /**
   * {@inheritdoc}
   */
  public function load($ids = array(), $conditions = array()) {
    $entities = array();

    if (!empty($ids)) {
      $config = self::getConfig();
      $company = new Company($config);
      foreach ($ids as $id) {
        try {
          $array = cache_get('apigee_company_' . $id);
          if (is_array($array)) {
            $entity = new ApigeeCompanyEntity($array);
          }
          else {
            $company->load($id);
            $entity = $this->addToCache($company);
          }

          $entities[$id] = $entity;
        }
        catch (ResponseException $e) {
          self::$lastException = $e;
          // Exception has been logged; take no further action.
        }
      }
    }
    else {
      // Load all companies for this org.
      $config = self::getConfig();
      $company = new Company($config);
      try {
        $companies = $company->listCompaniesDetail();
        foreach ($companies as $company) {
          $entities[$company->getName()] = $this->addToCache($company);
        }
      }
      catch (ResponseException $e) {
        self::$lastException = $e;
        // Exception has been logged; take no further action.
      }
    }
    return $entities;
  }

  /**
   * Updates a developer in a company.
   *
   * @param string $company_name
   *   The company machine name.
   * @param string $developer_email
   *   The developers email.
   * @param string $developer_role
   *   The developer role.
   *
   * @return bool
   *   TRUE if the developer was updated successfull else FALSE.
   */
  public function updateDeveloper($company_name, $developer_email, $developer_role) {

    $config = self::getConfig();
    $company = new Company($config);

    try {
      $company->load($company_name);
      $company->updateDeveloper($developer_email, $developer_role, $company_name);
      $this->addToCache($company);
    }
    catch (ResponseException $e) {
      self::$lastException = $e;
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Remove a developer from a company.
   *
   * @param string $developer_email
   *   The email id of the developer to be removed from the company.
   * @param string $company_name
   *   The machine name of the company from which to remove the developer.
   *
   * @return bool
   *   TRUE if the developer was removed successfully, else FALSE.
   */
  public function removeDeveloper($developer_email, $company_name = NULL) {
    $config = self::getConfig();
    $company = new Company($config);

    try {
      $company->load($company_name);
      $company->removeDeveloper($developer_email, $company_name);
      $this->addToCache($company);
    }
    catch (ResponseException $e) {
      self::$lastException = $e;
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Fetch the roles of a developer in a company.
   *
   * @param string $developer_email
   *   The email id of the developer whose roles are to be retrieved.
   * @param string $company_name
   *   The machine name of the company from which to fetch the developer roles.
   *
   * @return array|bool
   *   An array of developer roles, returns false in case of failure.
   */
  public function getDeveloperRoles($developer_email, $company_name) {
    $config = self::getConfig();
    $company = new Company($config);
    try {
      $developer_roles = $company->getDeveloperRoles($developer_email, $company_name);
    }
    catch (ResponseException $e) {
      self::$lastException = $e;
      return FALSE;
    }
    return $developer_roles;
  }

  /**
   * {@inheritdoc}
   */
  public function resetCache(array $ids = NULL) {
    if (is_array($ids)) {
      foreach ($ids as $id) {
        cache_clear_all('apigee_company_' . $id);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function delete($ids) {
    // @TODO Implement the logic to delete a company.
  }

  /**
   * {@inheritdoc}
   */
  public function invoke($hook, $entity) {

  }

  /**
   * {@inheritdoc}
   */
  public function export($entity, $prefix = '') {

  }

  /**
   * {@inheritdoc}
   */
  public function import($export) {

  }

  /**
   * {@inheritdoc}
   */
  public function buildContent($entity, $view_mode = 'full', $langcode = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function view($entities, $view_mode = 'full', $langcode = NULL, $page = NULL) {

  }

  /**
   * Returns the last exception returned from Edge.
   *
   * @return Apigee\Exceptions\ResponseException|null
   *   The last exception that occured.
   */
  public static function getLastException() {
    return self::$lastException;
  }

  /**
   * Rebuilds the cache record for a Company.
   *
   * @param Company $company
   *   The company whose cache entry is to be refreshed.
   *
   * @return ApigeeCompanyEntity
   *   The corresponding entity that was written to cache.
   */
  private function addToCache(Company $company) {
    $entity = new ApigeeCompanyEntity($company->toArray());
    $entity->developers = $company->listDevelopers();
    cache_set('apigee_company_' . $entity->name, $entity->toArray(), time() + (self::CACHE_LIFETIME_IN_MINUTES * 60));
    return $entity;
  }
}
