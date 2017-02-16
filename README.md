# DI Container をいろいろ使ってみる

- Pimple
    - http://pimple.sensiolabs.org/
    - https://github.com/silexphp/Pimple
- Symfony/DependencyInjection
    - https://symfony.com/doc/current/components/dependency_injection.html
    - https://github.com/symfony/dependency-injection
- Aura.Di
    - https://github.com/auraphp/Aura.Di
- PHP-DI
    - http://php-di.org/
    - https://github.com/PHP-DI/PHP-DI
- Zend\Di
    - https://github.com/zendframework/zend-di
- Zend\ServiceManager
    - https://github.com/zendframework/zend-servicemanager

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
- setSynthetic とか setAbstract とか気になる名前でもある

## Aura.Di

- ひと通りのことはできそう
- オートワイヤリングもできる
- require/include の遅延ロードもできる
    - callable の遅延実行で十分では・・
- newInstance でサービス定義なしに直接インスタンス化可能

## Zend\ServiceManager

- ZF2 で使ったけどいろいろおもしろ機能があった
    - AbstractFactory
        - サービス名のパターンを元に動的にインスタンス作成
        - コントローラーのインスタンス化で活躍
        - Aura.Di の newInstance みたいなのでも十分な気がするけど
    - Initializer
        - マーカーインタフェースを元にセッターインジェクションしたり
        - いわゆる AwareInterface と AwareTrait で実現
        - Symfony にも ContainerAwareInterface とかあるけどどう使うのか？
