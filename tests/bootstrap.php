<?php

//use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

//if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
//    require dirname(__DIR__) . '/config/bootstrap.php';
//} elseif (method_exists(Dotenv::class, 'bootEnv')) {
//    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
//}
//
//$connection = createConnection();
//$connection->exec('CREATE DATABASE IF NOT EXISTS fossils_test;');
//
//$sql = '
//    USE fossils_test;
//    DROP TABLE IF EXISTS user;
//    DROP TABLE IF EXISTS messenger_messages;
//    DROP TABLE IF EXISTS fossil_form_field;
//    DROP TABLE IF EXISTS fossil_entity;
//    DROP TABLE IF EXISTS fossil;
//';
//$connection->exec($sql);
//
//$sql = 'USE fossils_test;' . file_get_contents(__DIR__ . '/../src/Setup/database.sql');
//$connection->exec($sql);
//
//function createConnection(): PDO
//{
//    $connectionString = sprintf('mysql:host=%s;port=%s;', 'mysql', '3306');
//
//    return new PDO(
//        $connectionString,
//        'root',
//        'root',
//        [
//            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
//        ]
//    );
//}