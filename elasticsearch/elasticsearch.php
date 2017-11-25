<?php

/*
 * Ömer Faruk KESMEZ tarafından yazılmıştır.
 * Umuma açıktır.
 * İstediğiniz gibi kullanabilrisniz.
 * @ofke
 * info@ofkeyazilim.com
 */

/**
 * Description of elasticsearch
 *
 * @author ofke
 */
class elasticsearch {
    public $client = null;
    public $query = array();
    
    //Kurucu fonksiyon ile elasticsearch bağlantısı sağlanıyor
    public function __construct() {
        require 'vendor/autoload.php';
        $this->client = Elasticsearch\ClientBuilder::create()->build();
    }
    
    //Yeni bir index oluşturuyoruz sql veritabanında yeni bir veri tabanı olşturuyoruz gibi düşün.
    /*
     * $index =>Yeni oluşturacağımız elasticsearch index adını yani json verisinin adını temsil eder.
     * $rows  =>İlerki arama aşamalarında özel işlemler için kullanacağımız sütun isimlerini temsil eder.
     * ########################### ÖRNEK KULLANIM ################################################
     * # "eticaret"($index) adında, "id","date","title-1","name-1"($rows) sütunları özel tanımlı bir arama jsonu oluşturmak için :
     * # createIndex("eticaret",$rows=array("id","date","title-1","name-1")); şeklinde kullanılır.
     * Bakınız =>documents/create_index.php
     */
    public function createIndex($index,$rows=array()){
        //Mapings işlemi yeni oluşturulacak olan index için 
        //Type kısımlarında taşınacak olan ve kullanılacak sütun isimlerinin tanıtılması kısmıdır.
        //Yukarıda Type dediğimiz şey aslında sql veritabanında bulunan tablolara karşılık gelir.
        //Örneğin verilerimizi id değerine göre sıralamak mı istiyoruz ozaman id değeri mampping içerisinde tanımlanmalıdır,
        //Ayrıca tanımlanan bu id değerine fielddata değeri true olarak atanmalıdır.
        foreach ($rows as $value) {
            if($value=="id"){
                $_type = "integer";
                $_fielddata = false;
                $data = array("type"=>"integer");
            } else{
                $_type = "text";
                $_fielddata = true;
                $data = array("type"=>"text","fielddata"=>true);
            }
//            $data = [
//                'type' => "$_type",
//                'fielddata'=> $_fielddata
//            ];
            $properties[$value] = $data;
        }
        
        $params = ['index' => "$index",
            'body' =>  [ 
                 'mappings' => [
                    '_default_' => [    
                        'properties' => $properties
                    ]
                    ]
                ]
            ];

        //Yukarıda oluşturduğumuz array yapısını ayrıntılı görebilmek için aşağıdaki kodu enable yapın.
        //var_dump($params);
        
        $this->client->indices()->create($params);
    }

    //Elasticsearch servisine bağlantı sağlandımı kontrol ediliyor
    public function connectTest(){
        if ($this->client) {
            return 'connected';
        } else{
            return 'not connected';
        }
    }

    //Verilerin saklanması için elasticsearch içerisine set ediliyor 
    /*
     * 3 değer alır ve array türünde değer döndürür:
     * $index => string türünde saklayacağımız verinin hangi isimde indexleneceğini belirtir. json olarak saklanır yani database gibi düşün
     * $type  => string türünde sakladığımız veriliern saklanma yeri yani veritabanındaki tablo gibi düşün
     * $data  => array türünde saklayacağımız veriyi içerir. ($data verisi aşağıdaki şekilde gönderilmelidir.)
     * $data  = array(0=>array('name'=>'omer','surname'=>'kesmez'),1=>array('name'=>'faruk','surname'=>'kesmez'),..)
     * Bakınız => documents/mset.php
     */
    public function dataSet($index,$type,$data=array()){
        $boyut = count($data);
        for($i=0;$i<$boyut;$i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => "$index",
                    '_type' =>  "$type",
                    '_id' =>$i
//                    '_id' =>$data['id']
                ]
            ];

        // Aşağıdaki şekilde de kullanılabilir ama daha zahmetli olur
        //    $params['body'][] = [
        //        'id' => $products[$i]['id'],
        //        'name' => $products[$i]['name-1']
        //    ];

            $params['body'][] = $data[$i];
        }
        
        return $this->client->bulk($params);
    }
    
    //Daha önce set edilmiş veriler üzerine yeni array olarak ekleniyor veriler ekleniyor.
    /*
     * 6 değer alır ve json türünde değer döndürür:
     * $index => string türünde saklayacağımız verinin hangi isimde indexleneceğini belirtir. json olarak saklanır yani database gibi düşün
     * $type  => string türünde sakladığımız veriliern saklanma yeri yani veritabanındaki tablo gibi düşün
     * $data  => array türünde saklayacağımız veriyi içerir. ($data verisi aşağıdaki şekilde gönderilmelidir.)
     * $data  = array(0=>array('name'=>'omer','surname'=>'kesmez'),1=>array('name'=>'faruk','surname'=>'kesmez'),..)
     * $page  => kaçıncı sayfa olduğunu belirtir.
     * $limit => Her sayfada kaç adet data olduğunu saklar.
     * $elasticSerchUrl  => Elasticsearch portunun çalıştığı adres linki "http://localhost:9200" genelde 9200 üzerinde çalışır.
     * Bakınız => documents/insertdata.php
     */
    public function dataSetAdd($index,$type,$limit,$page,$elasticSerchUrl,$data=array()){
        $boyut = count($data);
        for($i=0;$i<$boyut;$i++) {
            $_data = json_encode($data[$i]);
            $key = $i+(($page-1)*$limit);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$elasticSerchUrl/$index/$type/$key/_create");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $_data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: '. strlen($_data)));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $respond = curl_exec($ch);
            curl_close($ch);
            echo $respond;
        }
    }
    
    
    //Daha önce set edilmiş veriler üzerine array olarak olarak gönderilen veriler id değerine uygun olarak güncelleniyor.
    /*
     * 3 değer alır ve array türünde değer döndürür:
     * $index => string türünde saklayacağımız verinin hangi isimde indexleneceğini belirtir. json olarak saklanır yani database gibi düşün
     * $type  => string türünde sakladığımız veriliern saklanma yeri yani veritabanındaki tablo gibi düşün
     * $data  => array türünde saklayacağımız veriyi içerir. ($data verisi aşağıdaki şekilde gönderilmelidir.)
     * $data  = array(0=>array('name'=>'omer','surname'=>'kesmez'),1=>array('name'=>'faruk','surname'=>'kesmez'),..)
     * Bakınız => documents/updatedata.php
     */
    public function dataUpdateArray($index,$type,$data=array()){
        $boyut = count($data);
        for($i=0;$i<$boyut;$i++) {
            
            $params['body'][] = array(
        	'update' => array(
                    '_index' => "$index",
                    '_type' =>  "$type",
                    '_id' => $i
        	)
            );

            $params['body'][] = array(
        	'doc_as_upsert' => 'true',
        	'doc' => $data[$i]
            );
            
        }
      return $this->client->bulk($params);
    }
    
    //Daha önce indexlanmiş ya da bulklanmış veriden istediğimiz id numralarına sahip olanları alıyoruz.
    /*
     * 3 değer alır ve array türünde değer döndürür:
     * $index => string türünde saklayacağımız verinin hangi isimde indexleneceğini belirtir. json olarak saklanır yani database gibi düşün
     * $type  => string türünde sakladığımız veriliern saklanma yeri yani veritabanındaki tablo gibi düşün
     * $data  => array türünde saklayacağımız veriyi içerir. ($data verisi aşağıdaki şekilde gönderilmelidir.)
     * $data  = array(1,2,3,4,....)
     * Önemli  : Eğer indexlenmiş tüm veriler alınacak ise $data arrayı 2 boyutlu gönderilir 1. eleman bütün verilerin alınacağını gösterirken 
     * 2. eleman toplam eleman sayısını belirtir. Yani örneğin $data = array("full",100); şeklinde gönderilir.
     * Bakınız => documents/mget.php
    */
    public function dataGet($index,$type,$data=array()){  
        $boyut = count($data);
        //Eğer indexlenmiş tüm veriler alınacak ise $data arrayı 2 boyutlu gönderilir 1. eleman bütün verilerin alınacağını gösterirken 
        //2. eleman toplam eleman sayısını belirtir. 
        if($boyut==2 && $data[0]=="full"){
            $boyut = $data[1];
        }
        for($i=0;$i<$boyut;$i++) {
            $params['body']['docs'][] =  [
                '_index' => "$index",
                '_type' => "$type",
                '_id' => ($data[0]=="full")?$i:$data[$i]
            ];
        }

        return $this->client->mget($params);
    }
    
    //Sql sorgularımızda kullandığımız like sorgusu gibi düşünebiliriz.
    /*
     * $rows  => Arama yapılacak olan sütun isimlerini taşıyan array değerini temsil eder
     * ################## $rows Örnek Kullanımı ########################
     * # Örneğin name ve title sütunlarında arama yapmak istiyorsak kullanımı => array('name','title');
     * #################################################################
     * $like  => Arama yapılacak olan sütun sayısını temsil etmektedir.
     * ################## $like Örnek Kullanımları ######################
     * # Örneğin omer ile başlayan veya içerisinde faruk geçen değerler için kullanımı => "%omer OR %faruk%"
     * # Yukarıdaki $like ve $rows örnek değerleri kullanıldığında name ve title sütunları içerisinde,
     * # omer ile başlayan veya içerisinde faruk geçen değerler bir array içerisinde alınır.
     * ###################################################################
     * $size => Alıncak veri sayısını temsil eder örneğin 10 tane.
     * $index => daha önce tanımladığımız verileri taşıyan json ismi sqlde veritabanı gibi düşün.
     * $sort  =>Array olarak değer alır ve örneğin id değerine göre büyükten küçüğe sırala şeklinde çalışır
     * $type => Sql içerisinde kullandığımız tablo gibi düşün.
     * $page => sayfa değerini temsil eder.
     * Bakınız => documents/like_query.php
     */
    public function searchLike($like, $index, $type, $rows = array(), $sort=array() ,$size = null,$page=null){
        $params['index'] = $index;
        $params['type']  = $type;
        $params['body']['query']  =  $like  = array("query_string"=>array("fields"=>$rows,"query"=>"$like","use_dis_max"=>true));
        
        //Tanımlı bir size değeri gönderildi isetanımlanıyor.
        if($size){
            $params['size'] = $size;
        } 
        
        if($page==0 || !$page){
            $page = 1;
        }
        
        if($size && $page && $page>0){
            $page = $page-1;
            $params['from'] = $size*$page;
        }

        if(count($sort)>0){
            $params['body']['sort'] = $sort;
        }
        
        //Arama Sonuçları alınıyor.
        $results = $this->client->search($params);

        //Arama sonuçları düzneli bir array haline getiriliyor.
        $data = array();$i=0;
        foreach ($results['hits']['hits'] as $key => $item) {
            foreach ($item as $value) {
                if(is_array($value) && $value['id']){
                    $data[] = $value;  
                }
            }
        }
        
        //Arama sonuçları döndürülüyor.
        return $data;
    }
    
    //Sadece kesin bir veri arıyorsak örenğin id değeri 1 olan kayıtları getir.
    /*
     * $value => Aranan değeri temsil eder.
     * $row   => Arama yapılacak sütunu taşır
     * $size => Alıncak veri sayısını temsil eder örneğin 10 tane.
     * $index => daha önce tanımladığımız verileri taşıyan json ismi sqlde veritabanı gibi düşün.
     * $type => Sql içerisinde kullandığımız tablo gibi düşün.
     * ######################### ÖRNEK KULLANIM #######################
     * # id($row) sütunundaki değeri 2($value) olan demo2($index) indexli urunler2($type) tipindeki json içerisinde 1($size) kayıt getir.
     * # sorgusunu şu şekilde yaparız searchSingleRow(2,"demo2","urunler2","id",1);
     * Bakınız => documents/single_data.php
     */
    function searchSingleRow($value, $index, $type, $row, $size = null){
        $params['index'] = $index;
        $params['type']  = $type;
        
        //Elasticsearch üzerinde arama yapmak için gerekli sorgu arrayı oluşturuluyor
        $params['body']['query']['match'][$row] = $value;
        
        //Tanımlı bir size değeri gönderildi isetanımlanıyor.
        if($size){
            $params['size'] = $size;
        }  
        
        //Arama Sonuçları alınıyor.
        $results = $this->client->search($params);

        //Arama sonuçları düzneli bir array haline getiriliyor.
        $data = array();$i=0;
        foreach ($results['hits']['hits'] as $key => $item) {
            foreach ($item as $value) {
                if(is_array($value)){
                    $data[] = $value;  
                }
            }
        }
        
        //Arama sonuçları döndürülüyor.
        return $data;
    }
    
    //Belitilen sütun değeri için belirtilen aralıklardaki verileri alır.
    //Örneğin : id değerleri 10dan büyükleri getir.
    //Örneğin : fiyatı 100 ile 1000 arasında olanları getir.
    /*
     * $small => Bu değerden küçük olanları listeler.
     * $big => Bu değerden büyükleri listeler.
     * $row   => Arama yapılacak sütunu taşır
     * $size => Alıncak veri sayısını temsil eder örneğin 10 tane.
     * $index => daha önce tanımladığımız verileri taşıyan json ismi sqlde veritabanı gibi düşün.
     * $page  =>Lİstelenecek olan sayfa sırasını belirtir
     * $sort  =>Array olarak değer alır ve örneğin id değerine göre büyükten küçüğe sırala şeklinde çalışır
     * $type => Sql içerisinde kullandığımız tablo gibi düşün.
     ######################### ÖRNEK KULLANIM #######################
     * # id değeri 5 ten büyük 10dan küçük olan verileri id değerine göre büyükten küçüğe listeleyen kulanım aşağıdadır. 
     * # searchRange(10,5,"demo2","urunler2","id",array('id' => array( 'order' => 'desc')),null,null);
     * Bakınız => documents/range.php
     */
    function searchRange($small=null,$big=null, $index, $type, $row, $sort=array() , $size = null,$page = null){
        $params['index'] = $index;
        $params['type']  = $type;
        
        //Elasticsearch üzerinde arama yapmak için gerekli sorgu arrayı oluşturuluyor
        if($big && !$small){
            //$row değeri $big değerinden büyükleri getirir
            $params['body']['query']['range'][$row]['gt'] = $big;//Büyük
        } elseif($small && !$big){
            //$row değeri $small değerinden küçükleri getirir
            $params['body']['query']['range'][$row]['lte'] = $small;//Küçük
        } else{
            //$row değeri $big ile $small arasındaki verileri getirir.
            $params['body']['query']['range'][$row]['gt'] = $big;//Büyük
            $params['body']['query']['range'][$row]['lte'] = $small;//Küçük
        }

        //Tanımlı bir size değeri gönderildi isetanımlanıyor.
        if($size){
            $params['size'] = $size;
        } 
        
        if($page==0 || !$page){
            $page = 1;
        }
        
        if($size && $page && $page>0){
            $page = $page-1;
            $params['from'] = $size*$page;
        }

        if(count($sort)>0){
            $params['body']['sort'] = $sort;
        }
  
        //Arama Sonuçları alınıyor.
        $results = $this->client->search($params);

        //Arama sonuçları düzneli bir array haline getiriliyor.
        $data = array();$i=0;
        foreach ($results['hits']['hits'] as $key => $item) {
            foreach ($item as $value) {
                if(is_array($value) && $value['id']){
                    $data[] = $value;  
//                    echo $value['id']."<br>";
                }
            }
        }
//        print_r($results);exit;
//        var_dump($data);
//        exit;
        
        //Arama sonuçları döndürülüyor.
        return $data;
    }
    
    //Sql sorgularında arama işlemlerinde kullandığımız bazı complex sorguları getiren sorgular gibi düşün.
    //Örnek verecek olursak "code","name" sütunlarında "ara" değeri geçen ve fiyatı 100 tlden büyük id değeri 12,11,10,.. olan ürünleri listele.
    /*
     * $index => daha önce tanımladığımız verileri taşıyan json ismi sqlde veritabanı gibi düşün.
     * $type => Sql içerisinde kullandığımız tablo gibi düşün.
     * $like  => Arama yapılacak olan sütun sayısını temsil etmektedir.
     * ################## $like Örnek Kullanımları ######################
     * # Örneğin omer ile başlayan veya içerisinde faruk geçen değerler için kullanımı => "%omer OR %faruk%"
     * # Yukarıdaki $like ve $rows örnek değerleri kullanıldığında name ve title sütunları içerisinde,
     * # omer ile başlayan veya içerisinde faruk geçen değerler bir array içerisinde alınır.
     * ###################################################################
     * $likRows  => Arama yapılacak olan sütun isimlerini taşıyan array değerini temsil eder
     * ################## $rows Örnek Kullanımı ########################
     * # Örneğin name ve title sütunlarında arama yapmak istiyorsak kullanımı => array('name','title');
     * #################################################################
     * $rangeRow  => Hangi sütunda bulunan değer için büyüktür küçüktür yapılacak belirleniyor
     * $rangeBig  => $rangeRow ile tanımlanan sütun üzerinde bulunan değerleri içinde $rangeBig değerinden büyükleri getir demektir.
     * $rangeSmall  => $rangeRow ile tanımlanan sütun üzerinde bulunan değerleri içinde $rangeSmall değerinden büyükleri getir demektir.
     * ################## Range Değerlieri Örnek Kullanımı ########################
     * # Örneğin id($rangeRow) sütünunda değeri 5ten($rangeBig) büyük 10dan($rangeSmall) küçük değerleri listelemek için:
     * # $rangeRow => "id" , $rangeBig => 5 , $rangeSmall => 10 şeklinde tanımlanır
     * #################################################################
     * $terms =>  örneğin status değerleri 1 olanları getir demek için kullanılır.
     * ################## $terms Örnek Kullanımı ########################
     * # Örneğin status değerleri 1,2,3,4 olan verileri listelemek için:
     * # $terms => array("status"=>array(1,2,3,4,5,6)) şeklinde tanımlanır.
     * #################################################################
     * $size => Alıncak veri sayısını temsil eder örneğin 10 tane.
     * $sort  =>Array olarak değer alır ve örneğin id değerine göre büyükten küçüğe sırala şeklinde çalışır
     * $page => sayfa değerini temsil eder.
     * Bakınız => documents/complex.php
     */
    public function searchComplex($index , $type , $like, $likeRows = array() , $rangeRow , $rangeBig = null , $rangeSmall = null , $terms = array() ,$sort=array() , $size = null , $page=null){
        $params['index'] = $index;
        $params['type']  = $type;
        
        $islem = 0;
        //Eğer like sorgusu içeriyorsa
        if($like && count($likeRows)>0){
            if(($rangeRow && ($rangeBig && $rangeSmall)) || count($terms)>0){
                $params['body']['query']['bool']['must']['query_string'] = array("fields"=>$likeRows,"query"=>"$like","use_dis_max"=>true);
            } else{
                $params['body']['query']['query_string'] = array("fields"=>$likeRows,"query"=>"$like","use_dis_max"=>true);
            }
            
            $islem ++;
        }
        
        if($rangeRow){
            if($rangeBig || $rangeSmall){
                $islem++;
            }
            if($rangeBig ){
                if($islem>1){
                    $params['body']['query']['bool']['filter']['bool']['must']['range'][$rangeRow]['gt'] = $rangeBig;//Büyük
                } else{
                    if(count($terms)>0){
                        $params['body']['query']['bool']['must']['range'][$rangeRow]['gt'] = $rangeBig;
                    } else{
                        $params['body']['query']['range'][$rangeRow]['gt'] = $rangeBig;//Büyük
                    }
                }
                
            }
            
            if ($rangeSmall) {
                if($islem>1){
                    $params['body']['query']['bool']['filter']['bool']['must']['range'][$rangeRow]['lte'] = $rangeSmall;//Büyük
                } else{
                    if(count($terms)>0){
                        $params['body']['query']['bool']['must']['range'][$rangeRow]['lte'] = $rangeSmall;
                    } else{
                        $params['body']['query']['range'][$rangeRow]['lte'] = $rangeSmall;//Büyük
                    }
                }
            }
        }
        
        if(count($terms)>0){
            if($islem == 1){
                $params['body']['query']['bool']['filter']['bool']['must']['terms'] = $terms;
            } elseif ($islem == 2) {
                $params['body']['query']['bool']['filter']['bool']['filter']['bool']['must']['terms'] = $terms;
            } else{
                $params['body']['query']['terms'] = $terms;
            }
        }
        
        //Tanımlı bir size değeri gönderildi isetanımlanıyor.
        if($size){
            $params['size'] = $size;
        } 
        
        if($page==0 || !$page){
            $page = 1;
        }
        
        if($size && $page && $page>0){
            $page = $page-1;
            $params['from'] = $size*$page;
        }

        if(count($sort)>0){
            $params['body']['sort'] = $sort;
        }
        
        //Arama Sonuçları alınıyor.
        $results = $this->client->search($params);
        
        //Arama sonuçları düzneli bir array haline getiriliyor.
        $data = array();$i=0;
        foreach ($results['hits']['hits'] as $key => $item) {
            foreach ($item as $value) {
                if(is_array($value) && $value['id']){
                    $data[] = $value;  
                }
            }
        }
        
        //Arama sonuçları döndürülüyor.
        return $data;
    }

    //Verilerin güncellenmesini sağlar
    /*
     * 3 değer alır ve array türünde değer döndürür:
     * $index => string türünde saklayacağımız verinin hangi isimde indexleneceğini belirtir. json olarak saklanır yani database gibi düşün
     * $type  => string türünde sakladığımız veriliern saklanma yeri yani veritabanındaki tablo gibi düşün
     * $data  => Array türünde güncelleyeceğimiz veriyi içerir $data = array("doc"=>array("name-5"=>"name5","name-3"=>"name3"));
     * $id    => Data set edilirken _id şeklinde tanımlanan değeri temsil eder.
     * $elasticSerchUrl  => Elasticsearch portunun çalıştığı adres linki "http://localhost:9200" genelde 9200 üzerinde çalışır.
     * Bakınız =>documents/update.php
     */
    public function dataUpdate($index,$type,$id,$data,$elasticSerchUrl){
        $data = json_encode($data);
      
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$elasticSerchUrl/$index/$type/$id/_update");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $respond = curl_exec($ch);
        curl_close($ch);
    }
    
    //İsmi verilen elasticsearch json değerini yani veritabanını siler
    /*
     * $index => Silinecek index adını temsil eder.
     *  Örnek Kullanım : Daha önce createIndex fonksiyonu ile tanımlamış olduğumuzu düşündüğümüz "yeni" adlı jsonu silmek için:
     *  deleteIndex("yeni"); şekilnde çağırmamız yeterli.
     */
    public function deleteIndex($index){
        $params = ['index' => "$index"];
        $response = $this->client->indices()->delete($params);
        return $response;
    }
    
    //Elasticseaqrch servis üzerinde tanımlı olan tüm indexleri getirir
    /*
     * 1 değer alır ve json veri döndürür.
     * $elasticSerchUrl  => Elasticsearch portunun çalıştığı adres linki "http://localhost:9200" genelde 9200 üzerinde çalışır.
     */
    public function getIndexs($elasticSerchUrl){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$elasticSerchUrl/_cat/indices/");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $respond = curl_exec($ch);
        curl_close($ch);
        
        return $respond;
    }
    
    //Elasticseaqrch servis üzerinde tanımlı olan belirli indexi getirir
    /*
     * 2 değer alır ve array veri döndürür.
     * $index => string türünde saklayacağımız verinin hangi isimde indexleneceğini belirtir. json olarak saklanır yani database gibi düşün
     * $elasticSerchUrl  => Elasticsearch portunun çalıştığı adres linki "http://localhost:9200" genelde 9200 üzerinde çalışır.
     */
    public function getIndex($index,$elasticSerchUrl){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$elasticSerchUrl/$index");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $respond = json_decode(curl_exec($ch));
        curl_close($ch);
        
        return $respond;
    }
    
    //Sql işlemlerinde kullanmış olduğumuz like kodunu kullandığımızı düşünün
    /*
     * $search => Aranacak string
     * $like => Aranacak row değerlerini taşır. $like değeri aşağıdaki gibi olmalı
     * $like = array("name-1","title-1","description"); 
     * Yukarıdaki array değerine göre aranmak istenen değer "name-1","title-1","description" sütunlarında aranacak.
     */
    public function likeSearch($index, $type, $search, $like = array()){
        $params['index'] = $index;
        $params['type']  = $type;

//        $params['body']['query']['bool']['must']['query_string'] = array("fields"=>array("code","name-1"),"query"=>"%001 OR %Bayan%","use_dis_max"=>true);
//        $params['body']['query']['bool']['filter']['bool']['must']['range'] = array("id"=>array("gte"=>11,"lte"=>52));
//        $params['body']['query']['bool']['filter']['bool']['must']['terms'] = array("id"=>array(2,51));
//        $params['body']['query']['bool']['filter']['bool']['filter']['bool']['must']['terms'] = array("id"=>array(2,51));

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

        $results = $this->client->search($params);
        print_r($results);
    }


    //Arama yapılacak değer class içerisine tanımlanıyor.
    public function setSearchText($search){
        $this->search = $search;
    }
    
    
}
