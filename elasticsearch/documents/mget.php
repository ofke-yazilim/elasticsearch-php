<?php
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

//Aşağıda daha önce oluşturulmuş json yapısına 3 adet veri depo ediliyor bu verilerden arama yapabiliriz.
$idler = array("1","2","3");//Mset işlemi sırasında tanılanan _index değerlerinitemsil eder bunları kullanrak verileri get ediyoruz.

for($i=0;$i<3;$i++) {
    $params['body']['docs'][] =  [
        '_index' => "index_ismi",//Burada kullanılacak olan index create_index.php ile oluşturulanla aynı adı taşır
        '_type' =>  "type_ismi",//mset.php ile oluşturduğumuz type ismidir bakınız mset.php.
        '_id' => $idler[$i]
    ];
}

return $this->client->mget($params);
