<?php


use Wqy\GenCode;

if (PHP_SAPI != 'cli') {
    exit;
}

if (empty($argv[1])) {
    exit('usage: php code.php <config file>');
}

$file = $argv[1];
if (! is_file($file)) {
    exit('file not exists ' . $file);
}

require __DIR__ . '/../src/GenCode.php';
$gen = new GenCode();

$config = require $file;

$dir = __DIR__ . '/../output/';
if (! is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$save = $dir . preg_replace('#[/\\\\]#', '__', $config['uri']) . date('_Y_m_d_H_i_s') . '.php';
$gen->handle($config, $save);
