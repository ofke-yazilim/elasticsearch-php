<?php
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

$params['index'] = $index;//create_index.php ile oluşturduğumuz index ismi.
$params['type']  = $type;//mset.php ile oluşturduğumuz type ismi

//Aşağıdaki sordu code ve name-1 sürunlarında 001 ile biten ya da içerisende Bayan geçen kayıtları arar. $params['body']['query'] = $like şekilnde kullanılır
$params['body']['query']  =  $like  = array("query_string"=>array("fields"=>array("code","name-1"),"query"=>"%001 OR %Bayan%","use_dis_max"=>true));

//tarih değeri değeri "01/01/2012"ten büyük "2013"ten küçük değerleri alır. $params['body']['query'] = $tariharalik şekilnde kullanılır
//$params['body']['query']  = $tariharalik = array("range"=>array("tarih"=>array("gte"=>"01/01/2012","lte"=>"2013","format"=>"dd/MM/yyyy||yyyy")));

$results = $client->search($params);
print_r($results);
