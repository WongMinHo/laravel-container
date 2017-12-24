<?php
/**
 * User: MinHow
 * Date: 2017/12/2
 *
 * 运行 php test.php
 */
//注册自动加载
spl_autoload_register('autoload');

function autoload($class)
{
    require './' . $class . '.php';
}
//引入Container容器类
require '../Container.php';

$app = new Container();//实例化容器
$app->bind('Car', 'Benz');//Car 是接口，Benz 是 Benz 类
$app->bind('driver', 'Driver');//driver 是 Driver 类的别名

$driver = $app->make('driver');//通过解析，得到了 Driver 类的实例

$driver->driveCar();//因为之前已经把 Car 接口绑定了 Benz，所以调用 driveCar 方法，会显示 'Driving Benz!'