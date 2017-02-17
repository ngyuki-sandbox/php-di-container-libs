<?php
namespace App;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

class ZendServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    function test()
    {
        $serviceManager = new ServiceManager([
            'services' => [
                Ore::class => new Ore(999),
            ],
            'factories' => [
                Are::class => function (ContainerInterface $container, $requestedName) {
                    return new Are($container->get(Ore::class));
                }
            ],
            'abstract_factories' => [],
            'delegators'         => [],
            'shared'             => [],
        ]);

        $obj = $serviceManager->get(Are::class);
        var_dump($obj->say()); // Are -> Ore(999)

        $xxx = $serviceManager->get(Are::class);
        var_dump($xxx === $obj); // true

        $zzz = $serviceManager->build(Are::class);
        var_dump($zzz === $obj); // false
    }

    /**
     * アブストラクトファクトリ
     *
     * 多用には性能上の問題があるのでなるべく少なく
     */
    function test_abstract_factories()
    {
        $serviceManager = new ServiceManager([
            'abstract_factories' => [
                new class implements AbstractFactoryInterface {
                    public function canCreate(ContainerInterface $container, $requestedName)
                    {
                        return fnmatch('ore.*', $requestedName);
                    }
                    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
                    {
                        list (, $val) = explode('.', $requestedName, 2);
                        return new Ore($val);
                    }
                }
            ],
        ]);

        var_dump($serviceManager->get('ore.123')->say()); // Ore(123)
        var_dump($serviceManager->get('ore.456')->say()); // Ore(456)
        var_dump($serviceManager->get('ore.789')->say()); // Ore(789)
    }

    /**
     * イニシャライザ
     *
     * あまり好ましくなく、なるべくファクトリでコンストラクタインジェクションすべき
     * インタフェース＋セッターでインジェクションするならデリゲートファクトリのほうが良い
     */
    function test_initializers()
    {
        $serviceManager = new ServiceManager([
            'factories' => [
                Ore::class => function () {
                    return new Ore(123);
                },
            ],
            'initializers' => [
                function(ContainerInterface $container, $instance) {
                    if ($instance instanceof Ore) {
                        $instance->set($instance->get() * 2);
                    }
                }
            ],
        ]);

        var_dump($serviceManager->get(Ore::class)->say()); // Ore(246)
    }

    /**
     * デリゲータ
     *
     * いわゆるデコレータ
     */
    function test_delegators()
    {
        $serviceManager = new ServiceManager([
            'factories' => [
                Ore::class => function () {
                    return new Ore(123);
                },
            ],
            'delegators' => [
                Ore::class => [
                    function (ContainerInterface $container, $name, callable $callback, array $options = null) {
                        $ore = $callback();
                        return new Are($ore);
                    }
                ]
            ],
        ]);

        var_dump($serviceManager->get(Ore::class)->say()); // Are -> Ore(123)
    }
}
