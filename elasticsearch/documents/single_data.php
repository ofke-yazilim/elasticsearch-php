<?php
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

$params['index'] = $index;//create_index.php ile oluşturduğumuz index ismi.
$params['type']  = $type;//mset.php ile oluşturduğumuz type ismi

//id değeri 11 olan verileri listeler.
$params['body']['query']['match']['id'] = 11;//kesin değer

$params['size'] = 1;//listelememiz gereken eleman sayısını temsil eder.

$results = $client->search($params);
print_r($results);
