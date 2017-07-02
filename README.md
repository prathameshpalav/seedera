# Seedera

A Laravel package for generating seeder files for tables having prefilled data.


## Install

Via Composer

``` bash
$ composer require pradility/sedera:dev-master
```

Then in your config/app.php in providers array add

``` bash
Pradility\Seedera\SeederaServiceProvider::class
```

## Usage

To generate seeder for table in database.

``` bash
php artisan build:seeder <table-name>
```
