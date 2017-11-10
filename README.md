# elasticsearch-php
Elasticsearch yapısını gerçekleyen fonksiyonlar php kullanılarak yazılmıştır.

<h2>WİNDOWS KURULUMU</h2>
<h4>Öncelikle çalıştırmış olduğumuz servisleri projemize tanımlayabimek için composer kuruyoruz projemize.</h4>
<li>1- Composer nasıl kurulur : https://getcomposer.org/Composer-Setup.exe adresinden indirilen exe next, next ile kurulur.</li>
<li>2- Ardından command ekranı kullanılarak composer eklenmek istenilen projeye gidilir örneğin ben wamp üzerinde proje yapıyorum</li>
   "cd C:\wamp64\www\elasticsearh" bu projeye gidebilirim.</li>
<li>3- Proje yolunu command ekranında tanıttıktan sonra "composer init" yazarak composer.json dosyamı oluturuyorum.
   (composer.json oluştururken bir kaç bilgi isteyecek bizden.girerek enter yapalım.)</li>

<h4>Artık composer kuruldu sıra elasticsearch servisini aktif etmekte bunun için </h4>
<li>1- https://www.elastic.co/downloads/elasticsearch adresinden elasticsearch dosyaları zip olarak indirilir.</li>
<li>2- İndirilen zip dosyası c:/ içerisinde açılır ve elasticsearch.bat dosyası çalıştırılır ve sürekli çalışır vaziyette bırakılır 
   bu sayede elasticsearch servisi çalışır.</li>
<li>3- command ekranına "composer require elasticsearch/elasticsearch" yazarak projeme elasticsearch servisini aktif ediyorum</li>
<br><br>
<h4>index.php dosyamın içine aşağıdaki kodları yazarak çalışıp çalışmadığını test edebilirim.</h4>

require 'vendor/autoload.php';<br>
$client = Elasticsearch\ClientBuilder::create()->build();<br>
 
if ($client) {<br>
    echo 'connected';<br>
}<br>
<br><br>
