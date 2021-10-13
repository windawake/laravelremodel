## 概要
laravel5.5〜laravel8のバージョンをインストールしてから、パッケージをインストールします。。
>composer require windawake/laravelremodel dev-master

まず、コマンド`php artisan laravelremodel:example-models`を実行して、`./vendor/windawake/laravelremodel/examples/Models`のディレクトリにある3つのファイルOrderDetailRemote.php、OrderRemote.php、ProductRemote.phpをアプリにコピーします。

```shel
laravel58
├── app
│   ├── Console
│   │   └── Kernel.php
│   ├── Exceptions
│   │   └── Handler.php
│   ├── Http
│   │   ├── Controllers
│   │   ├── Kernel.php
│   │   └── Middleware
│   ├── Models
│   │   ├── OrderDetailRemote.php
│   │   ├── OrderRemote.php
│   │   └── ProductRemote.php
```

次に、`php ./vendor/windawake/laravelremodel/examples/sqlite/build.php`を実行して、SQLiteデータベースファイルtest.dbを作成します。

phpunit.xmlはsqliteの構成を追加し、testsuiteRemoteを追加します
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit backupGlobals="false"
         backupStaticAttributes="false"
         bootstrap="vendor/autoload.php"
         colors="true"
         convertErrorsToExceptions="true"
         convertNoticesToExceptions="true"
         convertWarningsToExceptions="true"
         processIsolation="false"
         stopOnFailure="false">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>

        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>

        <testsuite name="Remote">
            	<directory>./vendor/windawake/laravelremodel/tests</directory>
        </testsuite>
    </testsuites>
    <filter>
        <whitelist processUncoveredFilesFromWhitelist="true">
            <directory suffix=".php">./app</directory>
        </whitelist>
    </filter>
    <php>
        <server name="APP_ENV" value="testing"/>
        <server name="BCRYPT_ROUNDS" value="4"/>
        <server name="CACHE_DRIVER" value="array"/>
        <server name="MAIL_DRIVER" value="array"/>
        <server name="QUEUE_CONNECTION" value="sync"/>
        <server name="SESSION_DRIVER" value="array"/>
        <server name="DB_CONNECTION" value="sqlite"/>
        <server name="DB_DATABASE" value="./test.db"/>
    </php>
</phpunit>

    </php>
</phpunit>

```
最後に、テストコマンド `./vendor/bin/phpunit --testsuit = Remote`を実行します
実行結果を以下に示します。18個のormの例がテストに合格しています。
```shell
root@DESKTOP-VQOELJ5:/web/linux/php/laravel/laravel58# ./vendor/bin/phpunit --testsuit=Remote
PHPUnit 7.5.20 by Sebastian Bergmann and contributors.

..................                                                18 / 18 (100%)

Time: 208 ms, Memory: 20.00 MB

OK (18 tests, 21 assertions)
```

## 特徴
1.アプリのバックエンドにあるコードをリファクタリングする必要はなく、ビジネスの基本的なサービスインターフェイスに徐々に接続します。
2. 1 + nクエリAPIを回避して、遅延読み込みをサポートします。
3.結合テーブル、結合テーブル、ネイティブSQLクエリ、集計クエリ、サブクエリなどをサポートします。laravelorm機能はほとんど使用できます。
4. laravelサービスコンテナの書き込み方法が使用されているため、クエリコンパイラと分散トランザクションの方法をカスタマイズできます。 （分散トランザクションはまだ実装されていません）。

## 原理
remote modelとは、リモート データ モデルの意味です。 基本サービスのAPIインターフェースはORMにカプセル化されています。 アプリのバックエンドにあるmodelは単なる仮想modelであり、ビジネスの基本的なサービスmodelの鏡像です。
![](https://cdn.learnku.com/uploads/images/202110/11/46914/okSl0tt7xc.png!large)
たとえば、紫色のProductModelは鏡像ですが、OrderLogicは白いProductModelとほぼ同じように使用します。
これを行うことの利点は何ですか？ Laravelmodelのすべての機能を再利用できます。 多くのパッケージがmodelに対して多くの新しい機能を実行するようになったため、それらを使用しないのは残念です。

## 使い方

RemoteModelクラスを継承する新しいProductRemoteクラスを作成します。
```php
<?php
namespace App\Models;

use Laravel\Remote2Model\RemoteModel;

class ProductRemote extends RemoteModel {
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = 'pid';
    protected $table = 'product';
    public $timestamps = false;

    public function getHandle()
    {
        /**
         * @var RemoteTool
         */
        $remoteTool = app('laravelremodel.tool');
        $condition = $remoteTool->queryToCondition($this->queryBuilder);

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'http://127.0.0.1:18001/api/product', [
            'query' => [
                'condition' => $condition
            ],
        ]);
        $json = $res->getBody()->getContents();
        
        $list = json_decode($json, true);
        return $list;
    }
    
}
```
getHandleのクエリの例を次に示します。 デフォルトでは、getHandle、updateHandle、insertGetIdHandle、deleteHandle、existsHandleの5つのメソッドが提供されています。 RemoteModelクラスを継承した後、getHandleなどのこれらのメソッドを定義せずに、通常のModelクラスと同じようにデフォルトでdbドライブを使用します。

|modelメソッド| mysql構文に類似|目的|
| ------------ | ------------ | ------------ |
| getHandle |選択|クエリリスト、クエリの詳細、クエリの集計操作（カウント、最大など）|
| updateHandle |更新|更新レコード|
| insertGetIdHandle |挿入|レコードの挿入|
| deleteHandle |削除|レコードの削除|
| presentHandle | select |存在するかどうかを判断します|
