<?php

/**
 * User: MinHow
 * Date: 2017/12/2
 */
//宝马类，实现汽车类接口
class Bmw implements Car
{
    public function drive()
    {
        echo 'Driving BMW!';
    }
}