<?php


namespace Wqy;

class GenCode
{
    public function handle($config, $file)
    {
        $code = "<?php \n\n\n" . $this->genClassCode($config);
        file_put_contents($file, $code);
        echo 'output to file ', realpath($file), "\n";
    }

    public function genClassCode($config)
    {
        $cls = $this->getClassName($config);
        $funcs = $this->genFuncCodes($config);
        $code = "class $cls\n{\n\n$funcs\n\n}\n";

        return $code;
    }

    private
    function getClassName($config)
    {
        if (isset($config['class_name'])) {
            return $config['class_name'];
        }
        $p = $this->parseUri($config['uri']);

        return $this->toCamel($p['ctrl'] . 'Controller', true);
    }

    private
    function getActionFuncName($config)
    {
        if (isset($config['func_name'])) {
            return $config['func_name'];
        }
        $p = $this->parseUri($config['uri']);

        return $this->toCamel($p['action'] . 'Action', false);
    }

    private
    function toCamel($str, $big)
    {
        $rt = preg_replace_callback('#\W+(\w)#', function ($mat) {
            return strtoupper($mat[1]);
        }, $str);

        return $big ? ucfirst($rt) : $rt;
    }

    private function parseUri($uri)
    {
        $pat = '#/?([\w-]+)/([\w-]+)#';
        if (preg_match($pat, $uri, $mat)) {
            return ['ctrl' => $mat[1], 'action' => $mat[2]];
        }
        return ['ctrl' => 'TODOCtrl', 'action' => 'TODOAction'];
    }

    private function genDocComment($config)
    {
        ob_start();
?>

    /**
     *
     * @gendoc
     * @uri <?=$config['uri']?>

     * @method <?=$config['method']?>

     * @desc <?=$config['summary']?>
<?php if (isset($config['desc'])) {
        foreach ($config['desc'] as $one) {
?>

     * <?=$one?>
<?php } } ?>

     *
<?php if (isset($config['tags'])) { ?>
     * @tags <?=implode(' ', $config['tags'])?>
<?php } ?>
<?php if (isset($config['param'])) {
        foreach ($config['param'] as $one) {
?>

     * @param <?=$this->genTypeCmt($one)?>
<?php } } ?>

     *
<?php if (isset($config['errorCode'])) {
        foreach ($config['errorCode'] as $code => $desc) {
?>
     * @errorCode <?=$code?> <?=$desc?>

<?php } } ?>
     *
     * @return array
<?=$this->genReturnTypes($config['return'])?>
     *
     * @end
     *
     */
<?
        return ob_get_clean();
    }

    private function genReturnTypes($return)
    {
        $this->genReturnTypesRecursive($return, $res);

        $rt = '';
        foreach ($res as $one) {
            $rt .= "     *\n$one";
        }

        return $rt;
    }

    private function genReturnTypesRecursive($type, & $res)
    {
        $typeName = $type['type'];
        if (empty($res[$typeName])) {
            $res[$typeName] = '';
        }

        $cmt = "     * @$typeName\n";
        if (isset($type['desc'])) {
            $cmt .= "     * {$type['desc']}\n";
        }

        $fields = $type['fields'];
        foreach ($fields as $one) {
            $cmt .= "     * {$this->genTypeCmt($one)}\n";
            if (! empty($one['fields'])) {
                $this->genReturnTypesRecursive($one, $res);
            }
        }

        $res[$typeName] .= $cmt;
    }

    private function genTypeCmt($field)
    {
        $type = isset($field['type']) ? $field['type'] : 'string';
        if (!empty($field['fields'])) {
            $type = "@{$type}[]";
        }
        $rt = $type . ' ';
        $rt .= isset($field['name']) ? $field['name'] : 'TODO input name';
        $rt .= ' ';
        if (! empty($field['required'])) {
            $rt .= 'required ';
        }
        if (isset($field['desc'])) {
            $rt .= $field['desc'];
        }
        return $rt;
    }

    public function genFuncCodes($config)
    {
        $this->genActionFunc($config, $codes);

        if ($config['debug']) {
            $this->genDebugFunc($config, $codes);
        }

        $ret = implode("\n", $codes);
        return $ret;
    }

    private function getDebugFuncName($config)
    {
        $fnName = empty($config['debug_func_name']) ? 'debugOutput' : $config['debug_func_name'];
        return $fnName;
    }

    private function genDebugFunc($config, & $codes)
    {
        $fnName = $this->getDebugFuncName($config);

        ob_start();
?>
    private $<?=$fnName?>Var = true;
    private function <?=$fnName?>($var)
    {
        if ($this-><?=$fnName?>Var) {
            var_dump($var);
        }
    }

<?php
        $codes[] = $code = ob_get_clean();
        return $code;
    }

    private function genDebugCodeLine($config, $names)
    {
        if ($config['debug']) {
            $fnName = $this->getDebugFuncName($config);
            ob_start();
            foreach ((array) $names as $name) {
?>

        // TODO delete debug for <?=$name?> var
        $this-><?=$fnName?>('var name:<?=$name?>, value:');
        $this-><?=$fnName?>(<?=$name?>);

<?php
            }

            return ob_get_clean();
        }
    }

    private function genActionFunc($config, & $codes)
    {
        ob_start();
        $requestMethodName = $config['method'] == 'POST' ? 'getPost' : 'get';
?>
<?=$this->genDocComment($config)?>
    public function <?=$this->getActionFuncName($config)?>()
    {
        $uid = $this->getUserId();

<?=$this->genDebugCodeLine($config, '$uid')?>

<?php foreach ($config['param'] as $param) {
        $name = $param['name'];
?>
        // TODO confirm
        /** <?=$param['type']?> <?=$param['desc']?> */
        $<?=$name?> = $this->request-><?=$requestMethodName?>('<?=$name?>');
<?php if (isset($param['assertion'])) {
        foreach ($param['assertion'] as $assert) {
?>
        Assertion::<?=$assert[0]?>(<?=$this->getAssertionParams($name, $assert)?>);
<?php } } ?>

<?=$this->genDebugCodeLine($config, '$' . $name); ?>

<?php } ?>
        return $this-><?=$this->getDataFuncName($config)?>(<?=$this->getDataFuncParam($config['param'])?>);
    }

<?php
        $codes[] = $code = ob_get_clean();

        $this->genActionDataFunc($config, $codes);

        return $code;
    }

    private function getDataFuncName($config)
    {
        return $this->getActionFuncName($config) . 'Data';
    }

    private function getAssertionParams($first, $assert)
    {
        $ret = ['$' . $first];
        if (($len = count($assert)) > 1) {
            for ($i = 1; $i < $len; $i ++) {
                $ret[] = var_export($assert[$i], true);
            }
        }
        return implode(', ', $ret);
    }

    private function getDataFuncParam($paramArray)
    {
        $arr = ['$uid'];

        foreach ($paramArray as $one) {
            $arr[] = '$' . $one['name'];
        }

        return implode(', ', $arr);
    }

    private function genActionDataFunc($config, & $codes)
    {
        $hasPage = $this->hasPageParam($config['param']);
        ob_start();
?>
    private function <?=$this->getDataFuncName($config)?>(<?=$this->getDataFuncParam($config['param'])?>)
    {
<?php if ($hasPage) { ?>
        $pageNo = (int) max(1, $pageNo);
        $pageSize = (int) max(1, $pageSize);

        $offset = ($pageNo - 1) * $pageSize;
        $size = $pageSize;

<?=$this->genDebugCodeLine($config, ['$pageNo', '$pageSize', '$offset', '$size'])?>

        $data = $this-><?=$this->getDataServiceFuncName($config)?>(/** TODO add params */);

        $hasMore = isset($data['hasMore']) <?='&&'?> $data['hasMore'];
        $totalSize = isset($data['totalSize']) ? $data['totalSize'] : 0;
        $rawList = isset($data['list']) ? $data['list'] : [];

<?=$this->genDebugCodeLine($config, ['$data', '$hasMore', '$totalSize', '$rawList'])?>

        $list = [];
        foreach ($rawList as $one) {
            $list[] = $this-><?=$this->getBuildItemFuncName($config)?>($one);
        }

        $ret = [
            'hasMore' => $hasMore,
            'totalSize' => $totalSize,
            'pageNo' => $pageNo,
            'pageSize' => $pageSize,
            'list' => $list,
        ];

<?=$this->genDebugCodeLine($config, ['$list', '$ret'])?>

        return $ret;
<?php } else { ?>

        $data = $this-><?=$this->getDataServiceFuncName($config)?>(/** TODO add params */);
        $ret = $this-><?=$this->getBuildItemFuncName($config)?>($data);

<?=$this->genDebugCodeLine($config, ['$data', '$ret'])?>

        return $ret;
<?php } ?>
    }

<?php

        $codes[] = $code = ob_get_clean();

        $this->genDataServiceFunc($config, $codes);
        $this->genBuildItemFunc($config, $codes);

        return $code;
    }

    private function getBuildItemFuncName($config)
    {
        return $this->getDataServiceFuncName($config) . 'BuildItem';
    }

    private function getDataServiceFuncName($config)
    {
        return $this->getDataFuncName($config) . 'Service';
    }

    private function hasPageParam($param)
    {
        foreach ($param as $one) {
            if (in_array($one['name'], ['pageNo', 'pageSize'])) {
                return true;
            }
        }
        return false;
    }

    private function genDataServiceFunc($config, & $codes)
    {
        $codes[] = $code = $this->genRequestServiceFunc($this->getDataServiceFuncName($config));
        return $code;
    }

    private function genRequestServiceFunc($name)
    {
        ob_start();
?>
    private function <?=$name?>(/** TODO add params */)
    {
        // TODO add service
        $serv = [servObject /** TODO */, servMethod /** TODO */];
        $params = [
            // TODO add service params
        ];

        $rs = call_user_func($serv, $params);
        return isset($rs['data']) ? $rs['data'] : [];
    }

<?php
        $code = ob_get_clean();
        return $code;
    }

    private function genBuildItemFunc($config, & $codes)
    {
        $codes[] = $code = $this->genAssignFunc($this->getBuildItemFuncName($config), $config['return']['fields']);
        return $code;
    }

    private function genAssignFunc($fnName, $fields)
    {
        ob_start();
?>
    private function <?=$fnName?>($data)
    {
        $rs = [];

<?php foreach ($fields as $f) {
        echo $this->getFieldAssignCodeLine($f);
        if (isset($f['fields'])) {
            foreach ($f['fields'] as $one) {
                echo $this->getFieldAssignCodeLine($one);
            }
        }
      }
?>

        return $rs;
    }

<?php
        $code = ob_get_clean();
        return $code;
    }


    private function getFieldAssignCodeLine($one)
    {
        ob_start();
?>
        // TODO confirm
        $key = '<?=$one['name']?>';  /** <?=$one['type']?> <?=$one['desc']?> */
        $rs['<?=$one['name']?>'] = isset($data[$key]) ? $data[$key] : null;

<?php

        return ob_get_clean();
    }
}
