<?php
namespace App;

use DI\Factory\RequestedEntry;
use Interop\Container\ContainerInterface;

use DI;
use DI\ContainerBuilder;

use function DI\object;
use function DI\get;
use function DI\value;
use function DI\factory;
use function DI\decorate;

class PhpDiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * デフォでタイプヒントからオートワイヤリング
     */
    function test()
    {
        $container = ContainerBuilder::buildDevContainer();

        $obj = $container->get(Are::class);
        var_dump($obj->say()); // Are -> Ore
    }

    /**
     * サービスの定義
     */
    function test_set()
    {
        $container = (new ContainerBuilder())->addDefinitions([
            'are' => object(Are::class)->constructor(get('ore')),
            'ore' => object(Ore::class)->constructor(get('val')),
            'val' => value(123),
        ])->build();

        $obj = $container->get('are');
        var_dump($obj->say()); // Are -> Ore(123)
    }

    /**
     * ファクトリでサービスの定義
     */
    function test_factory()
    {
        $container = (new ContainerBuilder())->addDefinitions([

            'are' => function (ContainerInterface $c) {
                return new Are($c->get('ore'));
            },

            'ore' => factory(function ($val) {
                return new Ore($val);
            })->parameter('val', 789),

        ])->build();

        $obj = $container->get('are');
        var_dump($obj->say()); // Are -> Ore(789/ore)
    }

    /**
     * サービスを取得のたびに作成
     */
    function test_make()
    {
        $container = ContainerBuilder::buildDevContainer();
        $container->set('are', DI\object(Are::class));

        $obj1 = $container->get('are');
        $obj2 = $container->get('are');
        var_dump($obj1 === $obj2); // true

        $obj1 = $container->make('are');
        $obj2 = $container->make('are');
        var_dump($obj1 === $obj2); // false
    }

    /**
     * サービスをオートワイヤリングでインジェクションしてクロージャーを呼ぶ
     */
    function test_call()
    {
        $container = ContainerBuilder::buildDevContainer();

        $container->call(function (Ore $ore, $val) {
            var_dump($ore->say()); // Ore
            var_dump($val);        // 123
        }, [
            'val' => 123,
        ]);
    }

    /**
     * ファクトリで要求されたサービスの名前をサービスの定義
     */
    function test_factory_requested()
    {
        $container = (new ContainerBuilder())->addDefinitions([

            'oreore' => function (RequestedEntry $entry) {
                return new Ore($entry->getName()); // 要求されたサービスの名前が得られる
            },

        ])->build();

        $obj = $container->get('oreore');
        var_dump($obj->say()); // Ore(oreore)
    }

    /**
     * デコレーション
     */
    function test_decoration()
    {
        $builder = new ContainerBuilder();

        $builder->addDefinitions([
            'ore' => function () {
                return new Ore(456);
            },
        ]);

        $builder->addDefinitions([
            'ore' => decorate(function (Ore $previous) {
                return new Sore($previous);
            })
        ]);

        $container = $builder->build();

        $obj = $container->get('ore');
        var_dump($obj->say()); // Sore -> Ore(456)
    }

    /**
     * ワイルドカード
     */
    function test_wildcard()
    {
        $builder = new ContainerBuilder();

        $builder->addDefinitions([
            'ore.*' => function (RequestedEntry $entry) {
                list (, $val) = explode('.', $entry->getName(), 2);
                return new Ore($val);
            },
        ]);

        $container = $builder->build();

        $obj = $container->get('ore.123');
        var_dump($obj->say()); // Sore -> Ore(123)

        $obj = $container->get('ore.456');
        var_dump($obj->say()); // Sore -> Ore(456)
    }
 }
