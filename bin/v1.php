<?php

header('content-type: application/json');
header('access-control-allow-origin: *');
header('access-control-expose-headers: x-power-date, content-length');

header('x-power-date: ' . date('c'));

header('last-modified: ' . date('c'));

echo json_encode(['get' => $_GET, 'post' => $_POST, 'pathinfo' => isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/' ]);
