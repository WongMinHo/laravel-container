<?php

/**
 * User: MinHow
 * Date: 2017/12/2
 */
//奔驰类，实现汽车类接口
class Benz implements Car
{
    public function drive()
    {
        echo 'Driving Benz!';
    }
}