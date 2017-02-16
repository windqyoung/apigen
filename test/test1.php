<?php

/**
 * @gendoc
 * @method   GET
 * @uri /user/doc2
 * @desc 测试生成文档2
 * 说明的内容
 * 说明内容第二行
 * @param string $id required 说明j GOod
 * @param string $code 说明code
 * @param string desc 说明desc
 * @param @mystring descm 说明desc自定
 * @errorCode 123 说明123
 *
 *
 * @return array|PDO
 * @data
 * string account  required 说明1
 * string vcode  vcode说明2
 * @arraykeytype arraykey  required 说明
 *
 * @arraykeytype
 * string key1 required 说明3
 * @arraykey2 arraykey2  数组说明2
 *
 * @arraykey2
 * type2 key2 说明4
 * type3 key3 说明5
 *
 *
 * @example
 * helo, world
 * @example 例子1
 *
 * {"json": "返回示例",
 * "json2": "返回2"
 *
 * }
 *
 * @example 测试例子
 * 盒子1
 * 例子2
 *
 * @example
 * 例子333
 *
 * @end
 *
 * nocheck
 */