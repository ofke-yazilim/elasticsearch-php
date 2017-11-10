<?php
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

//Aşağıda daha önce oluşturulmuş json yapısına 3 adet veri depo ediliyor bu verilerden arama yapabiliriz.
$isimler = array("omer","faruk","kesmez");
$idler = array("1","2","3");
for($i=0;$i<3;$i++) {
    $params['body'][] = [
        'index' => [
            '_index' => "index_ismi",//Burada kullanılacak olan index create_index.php ile oluşturulanla aynı adı taşır
            '_type' =>  "type_ismi",//Yeni oluşturuyoruz verilerimizi bir tabloya set ediyoruz gibi düşünün typ o tablonun isminitemsil eder.
            '_id' =>1
        ]
    ];

    $params['body'][] = [
        'id' => "$idler[$i]",
        'name' => "$isimler[$i]"
    ];
}

//Çoklu verileri index yapımıza tanımlarken bul kullanıyoruz.
$results = $client->bulk($params);
