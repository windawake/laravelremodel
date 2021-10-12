# laravel remote model
Create remote driver to convert remote api request into laravel model
## overview
Install the version between laravel5.5-laravel8, and then install the quick service package.

>composer require windawake/laravelremodel dev-master

First execute the command vendor:publish to copy the three files OrderDetailRemote.php, OrderRemote.php, and ProductRemote.php under the `./vendor/windawake/laravelremodel/examples/Models directory` to the `app` folder.

```shell
php artisan vendor:publish --provider="Laravel\Remote2Model\RemoteModelServiceProvider"
```
```shell
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

Then execute `php ./vendor/windawake/laravelremodel/examples/sqlite/build.php` to create the SQLite database file test.db

Add the configuration of sqlite and adds testsuite of Remote in phpunit.xml.
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
```
Finally, run the test command `./vendor/bin/phpunit --testsuit=Remote`
The running results are shown below, 18 orm examples have passed in the test.
```shell
root@DESKTOP-VQOELJ5:/web/linux/php/laravel/laravel58# ./vendor/bin/phpunit --testsuit=Remote
PHPUnit 7.5.20 by Sebastian Bergmann and contributors.

..................                                                18 / 18 (100%)

Time: 208 ms, Memory: 20.00 MB

OK (18 tests, 21 assertions)
```

## features
1. The app backend code does not need to be refactored, and it gradually connects with the basic service interface of the business.
2. Support lazy loading, avoiding 1+n query api.
3. Supports join tables, join tables, native SQL queries, aggregate queries, sub-queries, etc. Almost all laravel orm features can be used.
4. The laravel service container writing method is used, so the query compiler and distributed transaction methods can be customized. (Distributed transactions have not yet been implemented).

## principle
The api interface of the basic service is encapsulated into an ORM. The model at the back end of the app is just a virtual model, which is a mirror image of the business basic service model. ![](https://cdn.learnku.com/uploads/images/202110/11/46914/okSl0tt7xc.png!large)
For example, the purple ProductModel is a mirror image, but OrderLogic uses it almost the same as the white ProductModel.
What are the benefits of doing this? All the features of the laravel model can be reused. Because many packages now do a lot of new functions for the model, it is a pity not to use them.

## how to use

Create a new ProductRemote class that inherits the RemoteModel class.
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
Here is an example of querying getHandle. By default, 5 methods are provided: getHandle, updateHandle, insertGetIdHandle, deleteHandle and existsHandle. After inheriting the RemoteModel class, without defining these methods such as getHandle, it will use the db drive by default, just like the normal Model class.

| model method | similar to mysql syntax | purpose |
| ------------ | ------------ | ------------ |
| getHandle | select | Query list, query details, query aggregation operations (count, max, etc.) |
| updateHandle | update | Update record |
| insertGetIdHandle | insert | Insert record |
| deleteHandle | delete | Delete record |
| existsHandle | select | Determine whether it exists |
