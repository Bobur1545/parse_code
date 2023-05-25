<?php

$config = [
    'db' => [
        'host' => 'localhost',
        'name' => 'parser_db',
        'user' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ],
];

try {
    $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['name'];
    $db = new PDO($dsn, $config['db']['user'], $config['db']['password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('SET character_set_connection = ' . $config['db']['charset']);
    $db->exec('SET character_set_client = ' . $config['db']['charset']);
    $db->exec('SET character_set_results = ' . $config['db']['charset']);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    // var_dump($e);
}
