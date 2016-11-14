<?php
/**
 * Created by PhpStorm.
 * User: samir
 * Date: 11/12/16
 * Time: 12:00 AM
 */

namespace ObibaMicaClient;
/**
 * ObibaMicaDocuments class.
 */

class ObibaMicaDocuments extends ObibaMica {
  use Study, Network, Dataset;

  protected $resourceQuery = NULL;
  function __construct() {
    parent::__construct(
      new MicaConfig(),
      new MicaWatchDog(),
      new MicaCache()
    );
    return $this;
  }

  public function buildResourceQuery($method, $parameters){
    $resourceMethod = $method . 'Resources';
    $this->resourceQuery = $this->{$resourceMethod}($parameters);
    return $this;
  }
  /**
   * Get a collection of entities studies/datasets/networks/projects/variables.
   *
   * The $method parameters is a string method named in the trait entity class.
   *
   * @param string $method
   *   The method nome in the entity trait.
   * @param bool $ajax
   *   Is $ajax or not.
   *
   * @return string
   *   List entities in Json format.
   */
  public function getCollections($method, $ajax = FALSE) {
    $cachedData = $this->micaCache->clientGetCache($method . '_' . $this->resourceQuery);
    if (!empty($cachedData)) {
      return $cachedData;
    }
    else {
      $collections = $this->{$method}($this, $this->resourceQuery, $ajax);
      $this->micaCache->clientSetCache($method . '_' . $this->resourceQuery, $collections);
      return $collections;
    }
  }

  /**
   * Get a specific entity document study/dataset/network/project/variable.
   *
   * @param string $method
   *   The method nome in the entity trait.
   * @param string $idDocument
   *   The id of the entity to retrieve.
   *
   * @return Object
   *   Entity document.
   */
  public function getDocument($method) {
    return $this->{$method}($this,  $this->resourceQuery);
  }
}