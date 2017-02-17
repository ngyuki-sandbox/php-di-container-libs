<?php
namespace App;

use Zend\Di\Definition\ArrayDefinition;
use Zend\Di\DefinitionList;
use Zend\Di\Di;
use Zend\Di\Definition\CompilerDefinition;

class ZendDiTest extends \PHPUnit_Framework_TestCase
{
    function test()
    {
        $di = new Di();

        $di->instanceManager()->setParameters(Ore::class, [
            'val' => 123,
        ]);

        $are = $di->get(Are::class);
        var_dump($are->say()); // Are -> Ore(123)

        // 同じインスタンスが返る
        $xxx = $di->get(Are::class);
        var_dump($xxx === $are); // true

        // 新しいインスタンスが返る
        $xxx = $di->newInstance(Are::class);
        var_dump($xxx === $are); // false
    }

    function test_get_with_param()
    {
        $di = new Di();

        $di->instanceManager()->setParameters(Ore::class, [
            'val' => 123,
        ]);

        $obj = $di->get(Ore::class);
        var_dump($obj->say()); // Ore(123)

        $xxx = $di->get(Ore::class);
        var_dump($xxx === $obj); // true

        // 取得時にパラメータを変えられる
        $xxx = $di->get(Ore::class, ['val' => 456]);
        var_dump($xxx->say()); // Ore(456)
        var_dump($xxx === $obj); // false

        // 同じパラメータなら同じインスタンスが返る
        $zzz = $di->get(Ore::class, ['val' => 456]);
        var_dump($zzz === $xxx); // true
    }

    /**
     * ディレクトリをスキャンして定義を事前コンパイル
     */
    function test_compile()
    {
        $compiler = new CompilerDefinition();
        $compiler->addDirectory(__DIR__ . '/../src/');
        $compiler->compile();
        $definition = $compiler->toArrayDefinition()->toArray();
        $definitions = new DefinitionList([
            new ArrayDefinition($definition)
        ]);

        $di = new Di($definitions);

        $obj = $di->get(Are::class);
        var_dump($obj->say()); // Are -> Ore
    }

    /**
     * インタフェースのオートワイヤリング
     */
    function test_interface()
    {
        $di = new Di();

        // インタフェースと具象クラスの対応
        $di->instanceManager()->addTypePreference(OreInterface::class, Ore::class);

        $obj = $di->get(Sore::class);
        var_dump($obj->say()); // Sore -> Ore
    }

    /**
     * エイリアスで任意の名前のサービス
     */
    function test_alias()
    {
        $di = new Di();

        $di->instanceManager()->addAlias('are', Are::class);

        $obj = $di->get('are');
        var_dump($obj->say()); // Are -> Ore
    }
}
