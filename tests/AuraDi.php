<?php
namespace App;

use Aura\Di\ContainerBuilder;

class AuraDiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * コンストラクタインジェクション 引数の名前
     */
    function test_constructor_injection_name()
    {
        $di = (new ContainerBuilder())->newInstance();

        $di->params[Are::class]['ore'] = new Ore();

        $obj1 = $di->newInstance(Are::class);
        var_dump($obj1->say()); // Are -> Ore

        $obj2 = $di->newInstance(Are::class);
        var_dump($obj2 === $obj1); // false
    }

    /**
     * コンストラクタインジェクション 引数の順序
     */
    function test_constructor_injection_num()
    {
        $di = (new ContainerBuilder())->newInstance();

        $di->params[Are::class] = [new Ore()];

        $obj1 = $di->newInstance(Are::class);
        var_dump($obj1->say()); // Are -> Ore

        $obj2 = $di->newInstance(Are::class);
        var_dump($obj2 === $obj1); // false
    }

    /**
     * サービス
     */
    function test_service()
    {
        $di = (new ContainerBuilder())->newInstance();

        $di->set('ore', $di->lazyNew(Ore::class, [123]));
        $di->set('are', $di->lazyNew(Are::class, [$di->lazyGet('ore')]));

        $obj1 = $di->get('are');
        var_dump($obj1->say()); // Are -> Ore(123)

        $obj2 = $di->get('are');
        var_dump($obj2 === $obj1); // true
    }

    /**
     * Lazy Call
     */
    function test_call_01()
    {
        $di = (new ContainerBuilder())->newInstance();

        $di->set('val', $di->lazy(function () {
            return mt_rand();
        }));

        mt_srand(0);

        var_dump($di->get('val')); // 963932192
        var_dump($di->get('val')); // 963932192
        var_dump($di->get('val')); // 963932192
    }

    /**
     * Lazy Call ↑との違いがわからん
     */
    function test_call_02()
    {
        $di = (new ContainerBuilder())->newInstance();

        $di->set('val', function () {
            return mt_rand();
        });

        mt_srand(0);

        var_dump($di->get('val')); // 963932192
        var_dump($di->get('val')); // 963932192
        var_dump($di->get('val')); // 963932192
    }

    /**
     * スカラー値をサービスにするのがちょっとめんどい？
     */
    function test_value()
    {
        $di = (new ContainerBuilder())->newInstance();

        $di->set('v1', function(){ return 123; });

        $di->values['v2'] = 456;
        $di->set('v2', $di->lazyValue('v2'));

        var_dump($di->get('v1')); // 123
        var_dump($di->get('v2')); // 456
    }

    /**
     * オートワイヤリング
     */
    function test_auto_by_class()
    {
        $di = (new ContainerBuilder())->newInstance(ContainerBuilder::AUTO_RESOLVE);

        $di->params[Ore::class] = [999];

        $obj = $di->newInstance(Are::class);
        var_dump($obj->say()); // Are -> Ore(999)
    }

    /**
     * オートワイヤリング インタフェースの場合
     */
    function test_auto_by_interface()
    {
        $di = (new ContainerBuilder())->newInstance(ContainerBuilder::AUTO_RESOLVE);

        $di->types[OreInterface::class] = $di->lazyNew(Ore::class);

        $obj = $di->newInstance(Sore::class);
        var_dump($obj->say()); // Sore -> Ore
    }
}
