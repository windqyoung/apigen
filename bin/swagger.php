<?php


require __DIR__ . '/../src/ApiDocGen.php';

use Wqy\ApiDocGen;

$gen = new ApiDocGen();

$gen->handleSwagger();
