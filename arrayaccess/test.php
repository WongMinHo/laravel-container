<?php
/**
 * User: MinHow
 * Date: 2017/12/29
 * Time: 22:36
 */
//引入Container容器类
require '../Container.php';

$app = new Container();//实例化容器
//调用offsetSet方法
$app['test'] = 'MinHow';
//调用offsetExists方法
if (isset($app['test'])) {
    print_r('Using offsetExists');
}
//调用offsetGet方法
print_r($app['test']);
//调用offsetUnset方法
unset($app['test']);