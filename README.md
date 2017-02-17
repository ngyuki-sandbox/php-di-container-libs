# DI Container をいろいろ使ってみる

- Pimple
    - http://pimple.sensiolabs.org/
    - https://github.com/silexphp/Pimple
    - https://packagist.org/packages/pimple/pimple
- Symfony/DependencyInjection
    - https://symfony.com/doc/current/components/dependency_injection.html
    - https://github.com/symfony/dependency-injection
    - https://packagist.org/packages/symfony/dependency-injection
- Aura.Di
    - https://github.com/auraphp/Aura.Di
    - https://packagist.org/packages/aura/di
- PHP-DI
    - http://php-di.org/
    - https://github.com/PHP-DI/PHP-DI
    - https://packagist.org/packages/php-di/php-di
- Zend\Di
    - https://github.com/zendframework/zend-di
    - https://packagist.org/packages/zendframework/zend-di
- Zend\ServiceManager
    - https://github.com/zendframework/zend-servicemanager
    - https://packagist.org/packages/zendframework/zend-servicemanager

## Pimple

- callable でサービスを定義する
- すごいシンプルで必要最小限のことしかできない
- オートワイヤリングとかはない
- `extend()` で Initialize っぽいことができる
    - サービスごとに定義する必要がある
    - ラッパーを返せばデコレーションもできる

## Symfony/DependencyInjection

- ひと通りのことはできそう
- オートワイヤリングもできる
- YAML で定義するのが基本っぽい
    - PHP で出来ないわけではないけどやや冗長な感じする
- クロージャーをサービスのファクトリにはできない
    - 静的メソッドならファクトリにできる
    - 基本的にコンテナは静的でシリアライザブルなものらしい

## Aura.Di

- ひと通りのことはできそう
- オートワイヤリングもできる
- require/include の遅延ロードもできる
    - callable の遅延実行で十分では・・
- newInstance でサービス定義なしに直接インスタンス化可能

## PHP-DI

- ひと通りのことはできそう
- オートワイヤリングもできる
- デコレーションもできる
- サービス定義なしでもクラス名＝サービス名の扱い
- `call` という特徴的な機能
    - callable の引数をオートワイヤリングで解決して呼び出す
    - コントローラーのアクションのような callable への注入に使えそう
- `injectOn` という機能で作成済オブジェクトへのプロパティインジェクションを適用できる
    - あんまり使いみちはなさそう
- サービス名をワイルドカードを使える
    - サービスファクトリで要求されたサービス名が得られるので
    - `Zend\ServiceManager` の AbstractFactory のように使える
    - ワイルドカード -> ワイルドカードな対応もできる
        - `Blog\Domain\*RepositoryInterface` => `Blog\Architecture\*DoctrineRepository`
- アノテーションでインジェクションするサービスを指定できる
    - あまり好きではないけど・・

## Zend\ServiceManager

- ZF2 で使ったけどいろいろおもしろ機能があった
    - AbstractFactory
        - サービス名のパターンを元に動的にインスタンス作成
        - コントローラーのインスタンス化で活躍
    - Initializer
        - マーカーインタフェースを元にセッターインジェクションしたり
        - いわゆる AwareInterface と AwareTrait で実現
- オートワイヤリングはできない
