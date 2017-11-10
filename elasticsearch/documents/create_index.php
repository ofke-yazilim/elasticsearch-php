<?php

require 'vendor/autoload.php';
$client = Elasticsearch\ClientBuilder::create()->build();

$params = 
['index' => 'yeni',//Yeni oluşturduğumuz index isimi veritabanı ismi gibi düşün.
    'body' =>  [ 
         'mappings' => [
            '_default_' => [    
                'properties' => [
                    'title-1' => [
                        'type' => 'text',
                        "fielddata"=> true
                    ],
                    'name-1' => [
                        'type' => 'text',
                        "fielddata"=> true
                    ],
                    'name-2' => [
                        'type' => 'text',
                        "fielddata"=> true
                    ],
                    'code' => [
                        'type' => 'text',
                        "fielddata"=> true
                    ],
                    'id' => [
                        'type' => 'integer'//Bir değişken integer olursa eğer fielddata değeri true olamaz fielddata değeri true olmadığı için sort olarak kullanılamaz.
                    ],
                    'hit' => [
                        'type' => 'integer',
                        "fielddata"=> true
                    ]
                ]
            ]
        ]
    ]
];
    
$client->indices()->create($params);