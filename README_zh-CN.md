## 快速预览
安装laravel5.5 - laravel8之间的版本，然后安装快速服务化的package
>composer require windawake/laravelremodel dev-master

首先执行命令`php artisan laravelremodel:example-models`把`./vendor/windawake/laravelremodel/examples/Models`目录下面的OrderDetailRemote.php、OrderRemote.php、 ProductRemote.php三个文件复制到app文件夹下面。

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

然后执行 `php ./vendor/windawake/laravelremodel/examples/sqlite/build.php` 创建sqlite的数据库文件test.db

phpunit.xml增加sqlite的配置并且增加testsuite Remote
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
最后运行测试命令 `./vendor/bin/phpunit --testsuit=Remote`
运行结果如下所示，18个orm例子测试通过。
```shell
root@DESKTOP-VQOELJ5:/web/linux/php/laravel/laravel58# ./vendor/bin/phpunit --testsuit=Remote
PHPUnit 7.5.20 by Sebastian Bergmann and contributors.

..................                                                18 / 18 (100%)

Time: 208 ms, Memory: 20.00 MB

OK (18 tests, 21 assertions)
```

## 功能特性
1. app后端的代码不需要重构，渐进式地跟业务基础服务接口对接。
2. 支持懒加载，避免了1+n查询api。
3. 支持连表，联表，原生sql查询，聚合查询，子查询等等，laravel orm特性几乎可以使用。
4. 使用了laravel服务容器写法，所以query编译器，分布式事务方法可以自定义。（分布式事务暂未实现）。

## 原理解析
remote model是远程数据模型的意思。基础服务的api接口被封装成ORM。app后端的model只是虚拟的model，是业务基础服务model的一个镜像。
![](https://cdn.learnku.com/uploads/images/202110/11/46914/okSl0tt7xc.png!large)
例如紫色的ProductModel是镜像，但是OrderLogic使用它跟使用白色的ProductModel几乎一样。
这样子做有什么好处呢？可以复用laravel model的所有特性。因为现在很多package包针对model做了很多新功能，不用它们太可惜了。

## 如何使用

新建一个ProductRemote类继承RemoteModel类。
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
        $remoteTool = app('remote.tool');
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
这里以查询getHandle为例子。默认提供getHandle，updateHandle，insertGetIdHandle，deleteHandle和existsHandle 这5个方法。继承RemoteModel类后，不定义getHandle等这些方法，它会默认走db驱动，跟普通的Model类一样。

|  model方法 | 类似mysql语法  | 用途  |
| ------------ | ------------ | ------------ |
|  getHandle |  select | 查询列表，查询详情，查询聚合运算（count，max等）  |
|  updateHandle |  update | 更新记录  |
|  insertGetIdHandle |  insert | 插入记录  |
|  deleteHandle | delete  | 删除记录  |
|  existsHandle |  select |  判断是否存在 |


## 个人总结
这一次是入门篇，下一次估计会出源码篇。写了那么多年的代码，老是会发现服务化接口，就是查询一张表数据然后返回。搞得我经常怀疑人生。现在我坚定起来了，平时，我经常跟同事说laravel可以实现一秒服务化，他们都不相信。这次我可以证明给他们看。
