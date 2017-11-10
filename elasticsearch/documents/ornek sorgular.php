<?php
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

$params['index'] = $index;//create_index.php ile oluşturulan index adı
$params['type']  = $type;//mset.php ile oluşturulan type değeri _type
        
//$params['body']['query']['bool']['must']['query_string'] = array("fields"=>array("code","name-1"),"query"=>"%001 OR %Bayan%","use_dis_max"=>true);
//$params['body']['query']['bool']['filter']['bool']['must']['range'] = array("id"=>array("gte"=>11,"lte"=>52));
//$params['body']['query']['bool']['filter']['bool']['must']['terms'] = array("id"=>array(2,51));
//$params['body']['query']['bool']['filter']['bool']['filter']['bool']['must']['terms'] = array("id"=>array(2,51));

//Aşağıdaki sordu code ve name-1 sürunlarında 001 ile biten ya da içerisende Bayan geçen kayıtları arar. $params['body']['query'] = $like şekilnde kullanılır
//$params['body']['query']  =  $like  = array("query_string"=>array("fields"=>array("code","name-1"),"query"=>"%001 OR %Bayan%","use_dis_max"=>true));

//id değeri 25ten büyük 52den küçük değerleri alır $params['body']['query'] = $aralik şekilnde kullanılır
//$params['body']['query']  = $aralik = array("range"=>array("id"=>array("gte"=>25,"lte"=>52)));

//tarih değeri değeri "01/01/2012"ten büyük "2013"ten küçük değerleri alır. $params['body']['query'] = $tariharalik şekilnde kullanılır
//$params['body']['query']  = $tariharalik = array("range"=>array("tarih"=>array("gte"=>"01/01/2012","lte"=>"2013","format"=>"dd/MM/yyyy||yyyy")));

//id değeri 11 olanı getirir
//$params['body']['query']['match']['id'] = 11;//kesin değer

//id değeri 25ten büyükleri getirir
//$params['body']['query']['range']['id']['gt'] = '25';//Büyük

//id değerleri 45ten küçükleri getirir
//$params['body']['query']['range']['id']['lte'] = '45';//Küçük

//Belirtilen değerleri arar getirir
//$params['body']['query']['bool']['must']['terms']['id'] = array(11,2);//iki tan kesin değer

//$params['size'] = 1;//Maksimum eleman sayısı

$results = $client->search($params);