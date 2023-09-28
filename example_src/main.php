<?php

if(!isset($argv) || !is_array($argv) || count($argv) < 3){
	die("[ERROR] Missing arugments!\n\nUsage: `php example type letter`\nSupported:\n\ttype: [word || country]\n\tletter [a .. e]\n\nExample for getting a random word starting with the letter c:\n  php example word c".PHP_EOL);
}

$targetFile=$argv[1].DIRECTORY_SEPARATOR.$argv[2].".php";

if(!file_exists($targetFile)){die("[ERROR] Unsupported type or letter!".PHP_EOL);}

require_once($targetFile);

echo $data[array_rand($data)].PHP_EOL;
