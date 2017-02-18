<?php

header('content-type: application/json');
header('access-control-allow-origin: *');
echo json_encode(['get' => $_GET, 'post' => $_POST]);
