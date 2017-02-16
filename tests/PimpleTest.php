<?php
namespace App;

use Pimple\Container;

class PimpleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * サービス定義
     */
    function test_Defining_Services()
    {
        $container = new Container();

        $container['ore'] = function () {
            return new Ore();
        };

        $container['are'] = function (Container $c) {
            return new Are($c['ore']);
        };

        /* @var $are Are */
        $are = $container['are'];
        var_dump($are->say()); // Are -> Ore

        $obj = $container['are'];
        var_dump($obj === $are); // true
    }

    /**
     * ファクトリサービス
     */
    function test_Defining_Factory_Services()
    {
        $container = new Container();

        $container['ore'] = function () {
            return new Ore();
        };

        $container['are'] = $container->factory(function (Container $c) {
            return new Are($c['ore']);
        });

        /* @var $are Are */
        $are = $container['are'];
        var_dump($are->say()); // Are -> Ore

        $obj = $container['are'];
        var_dump($obj === $are); // false
    }

    /**
     * スカラー値をサービスに定義
     */
    function test_Defining_Parameters()
    {
        $container = new Container();

        $container['val'] = 123;

        $container['ore'] = function (Container $c) {
            return new Ore($c['val']);
        };

        /* @var $ore Ore */
        $ore = $container['ore'];
        var_dump($ore->say()); // Ore(123)
    }

    /**
     * callable そのものをサービスに登録
     */
    function test_Protecting_Parameters()
    {
        $container = new Container();

        mt_srand(0);

        $container['random_val'] = function () {
            return mt_rand();
        };

        $container['random_func'] = $container->protect(function () {
            return mt_rand();
        });

        // クロージャーがサービスファクトリとみなされるので関数を実行した結果になってしまう
        var_dump($container['random_val']); // 963932192
        var_dump($container['random_val']); // 963932192

        // クロージャー自身がサービスとみなされる
        var_dump(get_class($container['random_func'])); // Closure
    }

    /**
     * サービスが作成された後に加工する
     */
    function test_Modifying_Services_after_Definition()
    {
        $container = new Container();

        $container['aaa'] = 100;
        $container['zzz'] = 9;

        $container['ore'] = function (Container $c) {
            return new Ore($c['aaa']);
        };

        $container->extend('ore', function (Ore $ore, Container $c) {
            $ore->set($ore->get() * 2 + $c['zzz']);
            return $ore;
        });

        /* @var $ore Ore */
        $ore = $container['ore'];
        var_dump($ore->say()); // Ore(209) ... 100 * 2 + 9
    }

    /**
     * デコデーター
     */
    function test_Decorator()
    {
        $container = new Container();

        $container['ore'] = function (/*Container $c*/) {
            return new Ore();
        };

        $container->extend('ore', function (Ore $ore/*, Container $c*/) {
            return new Are($ore);
        });

        /* @var $ore Ore */
        $ore = $container['ore'];
        var_dump($ore->say()); // Are -> Ore
    }
}
