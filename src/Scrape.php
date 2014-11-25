<?php

namespace FF;

require(dirname(__DIR__).'/vendor/autoload.php');

use Sunra\PhpSimple\HtmlDomParser;
use FF\Espn;


$espn = new Espn(['username' => getenv('ESPN_USERNAME'), 'password' => getenv('ESPN_PASSWORD')]);

$login = $espn->login();

$dom = HtmlDomParser::str_get_html($login);

foreach($dom->find('a') as $a) {

	print_r($a);
}

// proof of concept at this point

//var_dump($dom);

