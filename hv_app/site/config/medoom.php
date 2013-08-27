<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// medoo配置
$config['database_type'] = 'mysql';
$config['server'] = 'localhost';
$config['username'] = 'root';
$config['password'] ='123456';
$config['charset'] = 'utf8';
$config['database_name'] ='pharos';
$config['option'] = array(PDO::ATTR_CASE => PDO::CASE_NATURAL);
