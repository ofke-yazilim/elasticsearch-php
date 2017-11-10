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
}<br>
<br><br>
<h2>elasticsearch/elasticsearch.php class fonksiyonları ve kullanımı</h2>

<h4>elasticsearch fonksiyonlarını içeren class yükleniyor</h4>
include 'elasticsearch.php';

<h4>Yüklenen class tanıtılıyor</h4>
$elasticsearch = new elasticsearch();

<h4>elasticsearch portuna bağlantı sağlandımı test ediliyor</h4>
$con = $elasticsearch->connectTest();

<h4>2- elasticsearch portuna bağlantı sağlandi ise devam ediliyor.</h4>
  <h4>Arama işlemlerini gerçekleştireceğimiz yeni bir json oluşturuyoruz.</h4>
    <h6>Eğer daha önce aynı index ismi ile json oluşturduysanız hata veririr.</h6>
    $elasticsearch->createIndex("yeni2",$rows=array("id","hit","date","title-1","name-1"));
    
  

