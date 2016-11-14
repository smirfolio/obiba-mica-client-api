<?php
/**
 * Created by PhpStorm.
 * User: samir
 * Date: 16-11-13
 * Time: 18:54
 */

namespace ObibaMicaClient;


trait Network {


  public Function getNetworksResources($parameters){
    $language = MicaConfig::getCurrentLang();
    $from = empty($parameters['from']) ? '0' : $parameters['from'];
    $limit = empty($parameters['limit']) ? '5' : $parameters['limit'];
    $order = empty($parameters['order']) ? '' : ($parameters['order'] == 'desc' ? '-' : '');
    $sort = empty($parameters['sort']) ? '' : $parameters['sort'];
    $sort_rql_bucket = empty($sort) ? "" : ",sort($order$sort)";
    $query = empty($parameters['query']) ? '' : $parameters['query'];
    $queryParameter = empty($query) ? NULL : ",match($query,(Mica_network.name,Mica_network.acronym,Mica_network.description))";
    $studies_query = empty($parameters['study_id']) ? '' : ",in(Mica_network.studyIds," . rawurlencode($parameters['study_id']) . ")";
    if (!empty($queryParameter || $studies_query)) {
      $params = "network(limit($from,$limit)$queryParameter$studies_query$sort_rql_bucket)";
    }
    else {
      $params = "network(exists(Mica_network.id),limit($from,$limit)$sort_rql_bucket)";
    }
    $params .= ",locale($language)";
    return '/networks/_rql?query=' . $params;
  }
  public function getNetworks($micaClient, $resourceQuery, $ajax = FALSE) {
    $data = $micaClient->obibaGet($resourceQuery, 'HEADER_JSON', $ajax);
    $resultData = json_decode($data);
    $resultResourceQuery = new NetworkJoinResponseWrapper($resultData);
    $hasSummary = $resultResourceQuery->hasSummaries();
    if (!empty($hasSummary)) {
      return $resultResourceQuery;
    }
    return FALSE;
  }
}
