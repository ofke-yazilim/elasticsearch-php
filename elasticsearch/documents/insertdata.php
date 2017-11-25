<?php

require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

//Json data alır aşağıdaki şekilde gönderli.
$data = '{
    "id" : "12",
    "name" : "new_name",
    "title" : "new_title"
}';

//İstenilirse array data gönderilerek daha sonrasında json yapılabilir.
$data = array("doc"=>array("id"=>"1","name"=>"new_name","title"=>"new_title"));
$data = json_encode($data);

//Aşağıda yeni2 create_index.php ile oluşturulan index değerini temsil eder.
//Aşağıdaki urunler3 mset.php ile oluşturulan _type değerini type değerini temsil eder.
//Aşağıdaki 14 değeri yeni bir _id değerini temsil eder mset.php ile oluşturulan değerden farklı olmalıdır
//Aşağıda yeni2 index üzerinde bulunan urunler3 type değerine sahip json verimiz üzerine 14 _id değerine sahip yeni bir data ekliyoruz.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:9200/yeni2/urunler3/14/_create");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: '. strlen($_data)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$respond = curl_exec($ch);
curl_close($ch);
echo $respond;
