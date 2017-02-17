<?php
namespace App;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SymfonyDependencyInjectionTest extends \PHPUnit_Framework_TestCase
{
    function test()
    {
        $container = new ContainerBuilder();
        $container->setParameter('ore.param', 987);
        $container->register('ore', Ore::class)->addArgument("%ore.param%");
        $container->register('are', Are::class)->addArgument(new Reference('ore'));

        /* @var $ore Ore */
        $ore = $container->get('ore');
        var_dump($ore->say()); // Ore(987)

        /* @var $are Are */
        $are = $container->get('are');
        var_dump($are->say()); // Are -> Ore(987)
    }

    function test_AutoWiring()
    {
        $container = new ContainerBuilder();
        $container->register('are', Are::class)->setAutowired(true);
        $container->compile();

        /* @var $are Are */
        $are = $container->get('are');
        var_dump($are->say()); // Are -> Ore
    }

    /**
     * コンストラクタのタイプヒントがインタフェースでも
     * 該当するサービスが１つしか登録されていないならそれが注入される
     */
    function test_AutoWiring_by_interface()
    {
        $container = new ContainerBuilder();
        $container->register('ore', Ore::class);
        $container->register('sore', Sore::class)->setAutowired(true);
        $container->compile();

        $sore = $container->get('sore');
        var_dump($sore->say()); // Sore -> Ore
    }

    function test_Decorate()
    {
        // Ore を Are でデコレーション
        $container = new ContainerBuilder();
        $container->register('ore', Ore::class);

        $container->register('ore.decorating', Are::class)
            ->setDecoratedService('ore', 'ore.inner')
            ->addArgument(new Reference('ore.inner'))
        ;

        $container->compile();

        /* @var $are Are */
        $ore = $container->get('ore');
        var_dump($ore->say()); // Are -> Ore
    }

    /**
     * 実行時にインスタンスを設定する
     */
    function test_synthetic()
    {
        $container = new ContainerBuilder();

        // コンパイル時に必要な事があるので Synthetic なサービスとして定義
        $container->register('ore', Ore::class)->setSynthetic(true);
        $container->compile();

        // 実行時にインスタンスを設定
        $ore = new Ore();
        $container->set('ore', $ore);

        $obj = $container->get('ore');
        var_dump($obj->say()); // Ore

        var_dump($obj === $ore); // true
    }
}
