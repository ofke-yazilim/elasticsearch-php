<?php
require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

//Json data alır aşağıdaki şekilde gönderli.
$data = '{
    "doc" : {
        "name-4" : "new_name"
    }
}';

//İstenilirse array data gönderilerek daha sonrasında json yapılabilir.
$data = array("doc"=>array("name-5"=>"name5","name-3"=>"name3"));
$data = json_encode($data);

//Aşağıda yeni2 create_index.php ile oluşturulan index değerini temsil eder.
//Aşağıdaki urunler3 mset.php ile oluşturulan _type değerini type değerini temsil eder.
//Aşağıdaki 14 mset.php ile oluşturulan _id değerini temsil eder ve o değer üzerinde güncellem yapar.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://localhost:9200/yeni2/urunler3/14/_update");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$respond = curl_exec($ch);
curl_close($ch);
