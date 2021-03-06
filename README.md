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

<h3>Önemli : </h3>
<h5>Windows Server Üzerine Kurulum Hakkında</h5><hr>
Eğer elasticsearch windows server üzerine kurulmak isteniyorsa öncelikle windows üzerinde java kurulu olması gerekmektedir. Hem JDK hem JRE kurulu olmalıdır. İnternette "jre server" şeklinde aratarak indirebilirsiniz. Örneğin ben şu adresten uygun sürümü indirdim : http://www.oracle.com/technetwork/java/javase/downloads/server-jre8-downloads-2133154.html indirilen zip dosyası içeriği c:/Program Files/java.. yolu takip edilrek çıkarılır. Klasörün içerisinde bulunan jre klasörüne ait yol, örneğin : "C:\Program Files (x86)\Java\jdk1.8.0_151\jre" denetim masası üzerinden -> Sistem sekmesin içerisiden -> gelişmiş sistem ayarları takip edilerek açılan pencereden ortam değişkenleri tıklanır açılan pencerede sistem değişkenleri kısmına yeni denerek  değişken adı JAVA_HOME ve değişken adresi "C:\Program Files (x86)\Java\jdk1.8.0_151\jre" olacak şekilde tanımlanır. Bu sayede elastichsearch windows server üzerinde kullanılabilir.

<h2>Ayrıntılı Dökümana Aşağıdaki adresten ulaşabilrsiniz.</h2>
https://github.com/ofke-yazilim/elasticsearch-php/tree/master/elasticsearch/documents

<h2>2- elasticsearch/elasticsearch.php class fonksiyonları ve kullanımı</h2>

<?php
<h4>elasticsearch fonksiyonlarını içeren class yükleniyor</h4><br>
<span>include 'elasticsearch.php';</span>

<h4>Yüklenen class tanıtılıyor</h4>
<span>$elasticsearch = new elasticsearch();</span>

<h4>elasticsearch portuna bağlantı sağlandımı test ediliyor</h4>
<span>$con = $elasticsearch->connectTest();</span>

<h4>elasticsearch portuna bağlantı sağlandi ise devam ediliyor.</h4>
<span>if($con=="connected"){</span>
    <h4>Verilerin bulunduğu json alınıyor</h4>
    <span>$json = file_get_contents("data.json");</span>
    <span>$products = json_decode($json);</span>

   <h4>Sonuçları alacağımız array tanımlanıyor.</h4>
    <span>$responses = array();</span>
    
    <h4>Arama işlemlerini gerçekleştireceğimiz yeni bir json oluşturuyoruz.<br>
    Eğer daha önce aynı index ismi ile json oluşturduysanız hata veririr.</h4>
    <span>$elasticsearch->createIndex("yeni2",$rows=array("id","hit","date","title-1","name-1"));</span>
    
    <h4>Adı gönderilen index json değerini siler</h4>
    <span>$elasticsearch->deleteIndex("yeni2");</span>
    
    <h4>Elasticsearch servis üzerinde eğer gönderilen indexe sahip bir oluşum varsa özelliklerini döndürür</h4>
    $indexAbout = $elasticsearch->getIndex("yeni2","http://localhost:9200");
    
    <h4>Alınan index bilgileri ekrana basılıyor</h4>
    print_r($indexAbout);
    
    <h5>Yukarıda getIndex ile çağrılan index elastic serviste mevcut değilse 404 hatası verir</h5>
    if($indexAbout->status==404){
        <h5>İndex olmadığı için oluşturuyoruz.</h5>
        $elasticsearch->createIndex("yeni2",$rows=array("id","code","stock","name-1"));<br>
    }
    
    <h4>Elastic servis üzerinde tanımlı tüm indexler ekrana basılıyor</h4>
    echo $elasticsearch->getIndexs("http://localhost:9200");
    
    <h4>Elasticsearch servisimiz üzerine adı index değeri demo2, tipi urunler2 olan ve içerisinde $product arrayını barındıracak json tanımlandı.</h4>
    <span>$responses = $elasticsearch->dataSet("yeni2","urunler3",$products);</span>

    <h4>Arama yapabilmek için yukarıda tanımlamış olduğumuz json yapısındaki verileri alıyoruz.<br>
    Verileri aldığımız fonksiyon iki şekilde çalışır ilkinde sadece _id indexleri 0 ve 1 olanlar listelenip alınırken ikincisnde bütün veriler alınır.</h4>
    <span>$responses = $elasticsearch->dataGet("yeni2","urunler3",array(11,51,12,13,27,97));</span>
    <span>$responses = $elasticsearch->dataGet("yeni2","urunler3",array("full", count($products)));</span>
    
    <h4>Eğer array olarak 1 adet veri gönderiyorsak aşağıdaki şekilde ekliyoruz.</h4>
    Örneğin $data = array(0=>array("id"=>210,"name-1"=>"Kesmez"))<br>
    id değerimizi gönderirken aşağıda olduğu gibi bir fazlası gönderilmeli 210 için 211 gönderiilmeli<br>
    Ayrıca 211 sayısının sol tarafında 1 olarak gönderilmeli <br>
    $insert = $elasticsearch->dataSetAdd("yeni2","urunler3",1,211,"http://localhost:9200",array(0=>array("id"=>210,"name-1"=>"Kesmez"),1=>array("id"=>211,"name-1"=>"Faruk")));

    //<h4>Elastc serviste bulunan array boyutunu verir.</h4>
    echo $elasticsearch->getCount("yeni2","urunler3","http://localhost:9200");

    <h4>Aşağıda sql sorgularında like olarak bilinen işlemin elasticsearch ile yapan fonsiyonu çalıştırır.<br>
    Aşağıdaki sorgu id,code,name-1 sütunlarında içerisinde Bayan geçen ya da sonu 001 ile biten verileri id değerine göre büyükten küçüğe listeler.</h4>
    <span>$responses = $elasticsearch->searchLike("*001 OR *Bayan*","yeni2","urunler3",array("id","hit","code","name-1"),array('hit' => array( 'order' => 'desc'),'name-1' => array( 'order' => 'desc')),5,1);</span>

    <h4>Beliritlen sütun üzerinde istenilen tek bir değeri arayan fonksiyon yani id değeri 11 olan datayı getirir</h4>
    <span>$responses = $elasticsearch->searchSingleRow(16,"yeni2","urunler3","id",1);</span>
    
    <h4>id değeri 5 ile 10 arasındaki olan değerleri id değerine göre büyükten küçüğe doğru getirir 0 dan başlayarak 10 adet getirir.</h4>
    <span>$responses = $elasticsearch->searchRange(10,5,"yeni2","urunler3","id",array('hit' => array( 'order' => 'desc')),40,1);</span>

    <h4>id değeri 14 olan verinin name-5 ve name-3 kısmını istenilen değerler ile günceller</h4>
    <span>$elasticsearch->dataUpdate("yeni2","urunler3",14,array("doc"=>array("name-5"=>"name5","name-3"=>"name3")),"http:localhost:9200");</span>

    <h4>Komplex sorgular için çalıştırılan bir fonsiyondur.</h4>
    <span>  <em>$responses = $elasticsearch->searchComplex("yeni2" 
        ,"urunler3" 
        ,null
        ,array("id","hit","code","name-1")
        ,"id" 
        ,5                  
        ,100
        ,array("id"=>array("1","2","3","16"))
        ,array('hit' => array( 'order' => 'desc'))
        ,200 
        ,null)</em>
    ;</span>
    
    <h4>Veriler ekrana yazılıyor.</h4>
    <span>echo count($responses);exit;</span><br>
    <span>print_r($responses);exit;</span><br>
    <span>var_dump($responses);exit;</span><br>
<span>} else{</span><br>
    <span>die("Elasticsearch portuna bağlantı sağlanamadı");</span><br>
<span>}</span><br>
