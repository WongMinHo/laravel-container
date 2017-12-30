<?php
/**
 * User: MinHow
 * Date: 2017/1/23
 */
//容器类，简写版的Laravel的容器类，方便测试
class Container implements ArrayAccess
{
    //用于存储提供实例的回调函数
    protected $bindings = [];

    //绑定接口和生成相应实例的回调函数
    public function bind($abstract, $concrete = null, $shared = false)
    {
        //如果提供的参数不是回调函数，则产生默认的回调函数
        if (! $concrete instanceof Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }

        //将返回的 $concrete 赋值到 $bindings[$abstract] 上
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    //默认生成实例的回调函数
    protected function getClosure($abstract, $concrete)
    {
        return function ($c) use ($abstract, $concrete) {
            $method = ($abstract == $concrete) ? 'build' : 'make';

            return $c->$method($concrete);
        };
    }
    //从容器中解析函数
    public function make($abstract)
    {
        $concrete = $this->getConcrete($abstract);

        //判断是否能实例化
        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }

        return $object;
    }
    //是否能实例化
    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    //获取绑定的回调函数
    protected function getConcrete($abstract)
    {
        if (! isset($this->bindings[$abstract])) {
            return $abstract;
        }

        return $this->bindings[$abstract]['concrete'];
    }

    //实例化对象
    public function build($concrete)
    {
        //当 $concrete 为闭包，直接返回执行的方法
        if ($concrete instanceof Closure) {
            return $concrete($this);
        }
        //先构建反射类
        $reflector = new ReflectionClass($concrete);
        //当该类是抽象类或者接口时，抛出异常，这里为了方便测试，去掉异常抛出
        if (! $reflector->isInstantiable()) {
            echo $message = "Target [$concrete] is not instantiable";
        }
        //获取类的构造器
        $constructor = $reflector->getConstructor();
        //如果没有构造器，直接实例化这个实体类
        if (is_null($constructor)) {
            return new $concrete;
        }
        //获取构造器参数构成的数组
        $dependencies = $constructor->getParameters();
        //获取依赖的实例对象
        $instances = $this->getDependencies($dependencies);
        //返回实例化传入类实体的实体对象 (即 $concrete)
        return $reflector->newInstanceArgs($instances);
    }

    //解决通过反射机制实例化对象时的依赖
    protected function getDependencies(array $parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (is_null($dependency)) {
                $dependencies[] = NULL;
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }

        return (array) $dependencies;
    }

    //解析依赖类
    protected function resolveClass(ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }

    //判断 bindings[$key] 是否存在，例：isset($container['data'])
    public function offsetExists($key)
    {
        return isset($this->bindings[$key]);
    }

    //取得实例化对象，调用 make() 方法，例：$container['data']
    public function offsetGet($key)
    {
        return $this->make($key);
    }

    //设置对象，例：$container['data'] = $value
    public function offsetSet($key, $value)
    {
        // 如果 $value 不是闭包函数，就先将 $value 重新赋值为一个闭包
        if (! $value instanceof Closure) {
            $value = function () use ($value) {
                return $value;
            };
        }
        // 如果 $value 是一个闭包函数，就直接绑定
        $this->bind($key, $value);
    }

    //释放 bindings[$key]，例：unset($container['data'])
    public function offsetUnset($key)
    {
        unset($this->bindings[$key]);
    }
}