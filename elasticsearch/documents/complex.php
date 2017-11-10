<?php
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

//1-Aşağıdaki query code ve name-1 sütünlarında Bayan geçen ve 001 ile biten değerlerin,
//2-İd değeri 11 ile 52 arasında olanlarının,
//3-Status değeri 2 ya da 51 olanları listeler.
/*1-*/$params['body']['query']['bool']['must']['query_string'] = array("fields"=>array("code","name-1"),"query"=>"%001 OR %Bayan%","use_dis_max"=>true);
/*2-*/$params['body']['query']['bool']['filter']['bool']['must']['range'] = array("id"=>array("gte"=>11,"lte"=>52));
/*3-*/$params['body']['query']['bool']['filter']['bool']['filter']['bool']['must']['terms'] = array("status"=>array(2,51));

/*
 * Komplex sorgularda bool ve must kullanılır,
 * eğer sadece bir sorgu yapılacaksa 1. sırada olduğu gibi bool ve must yeterldir hatta kullanılmayabilir.
 * eğer birden fazla sorgulama çeşiti kullanılacak ise ozaman bool must kullanılır ayrıca 1. sorgudan sonrakilerde filter yapısıda dahil olur.
 * sorguların aşağıya doğru kaydığını görürsek her eklene yeni filterda son sırada kalan must kısmına denk gelen değer fildter olur ve 
 * filter değerinden sonra bool ardından must gelir ve bu dizaynda devam eder 
 * örneğin yukarıda 3 adımda yaptığımız sorguya bir sorgu daha eklemek istersek : 
 * diyelim show değeri 1 olanları getirsin ozaman 4. sıraya şu sorgu denk gelir.
 * $params['body']['query']['bool']['filter']['bool']['filter']['bool']['filter']['bool']['must']['match'] = array("show"=>1);
 */

$results = $client->search($params);