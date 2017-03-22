<?php


return [
    'debug' => true,
    'debug_func_name' => 'debugOutput',
    'uri' => '/my-cls/my-func',
    'method' => 'POST',
    'summary' => 'summary-1',
    'desc' => ['desc1', 'desc2', 'desc3'],
    'prefix' => 'pre',
    'tags' => ['tag1', 'tag2', 'tag3'],
//     'class_name' => 'XyzController',
//     'func_name' => 'AbcAction',
    'param' => [
        ['name' => 'p1', 'type' => 'int', 'required' => true, 'desc' => 'p1 desc',
            'assertion' => [
                ['numeric', '提示1'],
                ['min', 1, '提示2, min'],
                ['notEmpty', '提示3, 不能为空'],
            ],
        ],
        ['name' => 'p2', 'type' => 'int', 'required' => true, 'desc' => 'p2 desc'],
        ['name' => 'pageNo', 'type' => 'int', 'required' => false, 'desc' => 'p3 desc'],
        ['name' => 'pageSize', 'type' => 'int', 'required' => false, 'desc' => 'p3 desc'],
    ],
    'errorCode' => [
        123 => '123 err',
        234 => '234 err',
    ],
    'return' => [
        'name' => 'data',
        'type' => 'data',
        'desc' => 'data return desc',
        'fields' => [
            ['name' => 'rt1', 'type' => 'boolean', 'required' => true, 'desc' => 'rt1 desc'],
            ['name' => 'rt2', 'type' => 'string', 'required' => true, 'desc' => 'rt2 desc'],
            ['name' => 'rt3', 'type' => 'int', 'required' => false, 'desc' => 'rt3 desc',
                'fields' => [
                    ['name' => 'fd1', 'type' => 'int', 'required' => true, 'desc' => 'fd1 desc'],
                    ['name' => 'fd2', 'type' => 'string', 'required' => true, 'desc' => 'fd2 desc'],
                    ['name' => 'fd3', 'type' => 'string', 'required' => false, 'desc' => 'fd3 desc'],
                ],

            ],
            ['name' => 'rt4', 'type' => 'rt3_list', 'required' => true, 'desc' => 'rt4 desc',
                'fields' => [
                    ['name' => 'fd1', 'type' => 'int', 'required' => true, 'desc' => 'fd1 desc'],
                    ['name' => 'fd2', 'type' => 'string', 'required' => true, 'desc' => 'fd2 desc'],
                    ['name' => 'fd3', 'type' => 'string', 'required' => false, 'desc' => 'fd3 desc'],
                ],
            ],
        ],
    ],

];
