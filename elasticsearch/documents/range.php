<?php
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

//id değeri 25ten büyükleri getirir
$params['body']['query']['range']['id']['gt'] = '25';//Büyük

//id değerleri 45ten küçükleri getirir
$params['body']['query']['range']['id']['lte'] = '45';//Küçük

//id değeri 25ten büyük 52den küçük değerleri alır $params['body']['query'] = $aralik şekilnde kullanılır
//$params['body']['query']  = $aralik = array("range"=>array("id"=>array("gte"=>25,"lte"=>52)));

$params['size'] = 1;//listelememiz gereken eleman sayısını temsil eder.

$results = $client->search($params);
print_r($results);
