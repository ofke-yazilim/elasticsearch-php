# elasticsearch-php
Elasticsearch yapısını gerçekleyen fonksiyonlar php kullanılarak yazılmıştır.

<h2>1 - WİNDOWS KURULUMU</h2>
<h4>Öncelikle çalıştırmış olduğumuz servisleri projemize tanımlayabimek için composer kuruyoruz projemize.</h4>
<li> Composer nasıl kurulur : https://getcomposer.org/Composer-Setup.exe adresinden indirilen exe next, next ile kurulur.</li>
<li> Ardından command ekranı kullanılarak composer eklenmek istenilen projeye gidilir örneğin ben wamp üzerinde proje yapıyorum</li>
   "cd C:\wamp64\www\elasticsearh" bu projeye gidebilirim.</li>
<li> Proje yolunu command ekranında tanıttıktan sonra "composer init" yazarak composer.json dosyamı oluturuyorum.
   (composer.json oluştururken bir kaç bilgi isteyecek bizden.girerek enter yapalım.)</li>

<h4>Artık composer kuruldu sıra elasticsearch servisini aktif etmekte bunun için </h4>
<li> https://www.elastic.co/downloads/elasticsearch adresinden elasticsearch dosyaları zip olarak indirilir.</li>
<li> İndirilen zip dosyası c:/ içerisinde açılır ve elasticsearch.bat dosyası çalıştırılır ve sürekli çalışır vaziyette bırakılır 
   bu sayede elasticsearch servisi çalışır.</li>
<li> command ekranına "composer require elasticsearch/elasticsearch" yazarak projeme elasticsearch servisini aktif ediyorum</li>

<h4>index.php dosyamın içine aşağıdaki kodları yazarak çalışıp çalışmadığını test edebilirim.</h4>
require 'vendor/autoload.php';<br>
$client = Elasticsearch\ClientBuilder::create()->build();<br>
 
if ($client) {<br>
    echo 'connected';<br>
}


<h2>2- elasticsearch/elasticsearch.php class fonksiyonları ve kullanımı</h2>

<h4>elasticsearch fonksiyonlarını içeren class yükleniyor</h4>
//include 'elasticsearch.php';

<h4>Yüklenen class tanıtılıyor</h4>
//$elasticsearch = new elasticsearch();

<h4>elasticsearch portuna bağlantı sağlandımı test ediliyor</h4>
//$con = $elasticsearch->connectTest();

<h4>elasticsearch portuna bağlantı sağlandi ise devam ediliyor.</h4>
//if($con=="connected"){
    <h4>Verilerin bulunduğu json alınıyor</h4>
    $json = file_get_contents("data.json");
    $products = json_decode($json);

   <h4>Sonuçları alacağımız array tanımlanıyor.</h4>
    $responses = array();
    
    <h4>Arama işlemlerini gerçekleştireceğimiz yeni bir json oluşturuyoruz.</h4>
    <h6>Eğer daha önce aynı index ismi ile json oluşturduysanız hata veririr.</h6>
    $elasticsearch->createIndex("yeni2",$rows=array("id","hit","date","title-1","name-1"));
    
    <h4>Adı gönderilen index json değerini siler</h4>
    $elasticsearch->deleteIndex("yeni2");exit;
    
    <h4>Elasticsearch servisimiz üzerine adı index değeri demo2, tipi urunler2 olan ve içerisinde $product arrayını barındıracak json tanımlandı.</h4>
    //$responses = $elasticsearch->dataSet("yeni2","urunler3",$products);

    <h3>Arama yapabilmek için yukarıda tanımlamış olduğumuz json yapısındaki verileri alıyoruz.</h3>
    <h4>Verileri aldığımız fonksiyon iki şekilde çalışır ilkinde sadece _id indexleri 0 ve 1 olanlar listelenip alınırken ikincisnde bütün veriler alınır.</h4>
    //$responses = $elasticsearch->dataGet("yeni2","urunler3",array(11,51,12,13,27,97));
    //$responses = $elasticsearch->dataGet("yeni2","urunler3",array("full", count($products)));

    <h4>Aşağıda sql sorgularında like olarak bilinen işlemin elasticsearch ile yapan fonsiyonu çalıştırır.</h4>
    <h6>Aşağıdaki sorgu id,code,name-1 sütunlarında içerisinde Bayan geçen ya da sonu 001 ile biten verileri id değerine göre büyükten küçüğe listeler.</h6>
    //$responses = $elasticsearch->searchLike("*001 OR *Bayan*","yeni2","urunler3",array("id","hit","code","name-1"),array('hit' => array( 'order' => 'desc'),'name-1' => array( 'order' => 'desc')),5,1);

    <h4>Beliritlen sütun üzerinde istenilen tek bir değeri arayan fonksiyon yani id değeri 11 olan datayı getirir</h4>
    //$responses = $elasticsearch->searchSingleRow(16,"yeni2","urunler3","id",1);
    
    <h4>id değeri 5 ile 10 arasındaki olan değerleri id değerine göre büyükten küçüğe doğru getirir 0 dan başlayarak 10 adet getirir.</h4>
    //$responses = $elasticsearch->searchRange(10,5,"yeni2","urunler3","id",array('hit' => array( 'order' => 'desc')),40,1);

    <h4>id değeri 14 olan verinin name-5 ve name-3 kısmını istenilen değerler ile günceller</h4>
    //$elasticsearch->dataUpdate("yeni2","urunler3",14,array("doc"=>array("name-5"=>"name5","name-3"=>"name3")),"http:localhost:9200");

    <h4>Komplex sorgular için çalıştırılan bir fonsiyondur.</h4>
    //$responses = $elasticsearch->searchComplex("yeni2" ##"index değeri"
        //,"urunler3" ##"type değeri"   
        //,null ##"like sorgusu null değeri alır
        //,array("id","hit","code","name-1")  ##"Like sorgusunu yapılacağı sütunlar"
        //,"id" ## "Range işlemi yapılacak sütun" 
        //,5 ## "Tanımlı değerden büyükleri getirir    null değeri alabilir                    
        //,100 ## "Tanımlı değerden küçükleri getirir   null değeri alabilir  
        //,array("id"=>array("1","2","3","16")) ## "Terms değerini temsil eder" örneğin : status değerleri 1,2,ya da 3 olanları getir =>array("status"=>array("1","2","3")) array() şeklinde boş gönderilebilir.
        //,array('hit' => array( 'order' => 'desc')) ## "örenğin hit değerine göre sıralama istersek" => array('hit' => array( 'order' => 'desc') array() şeklinde boş gönderilebilir.
        //,200 ## Kaç adet veri getirilecek null değeri alır
        //,null)## Sayfa değeri 1 1. sayfayı temsil eder, 2 ikinci sayfayı
    ;
    
    <h4>Veriler ekrana yazılıyor.</h4>
    //echo count($responses);exit;
    //print_r($responses);exit;
    //var_dump($responses);exit;
//} else{
    //die("Elasticsearch portuna bağlantı sağlanamadı");
//}
