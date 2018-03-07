# LaraSetting

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

#### Laravel 设置持久化工具包  / A persistent settings package for Laravel 5.5+
 
> * 数据库存储   / Database persistent
> * Laravel 框架自带缓存  / Cache by Laravel framework
> * 运行时缓存   / Runtime cache

## Install

Via Composer

``` bash
$ composer require jason-xt/lara-setting

$ php artisan vendor:publish --tag=config
$ php artisan vendor:publish --tag=migrations
$ php artisan migrate
```

## Config 
``` php

return [

    //use framework's cache drive
    'cache' => [

        'enable' => true,
        'prefix' => 'settings_',
        //cache time .minutes
        'ttl' => 60,
    ],
    //运行缓存
    'runtime' => true,

    //Facade name   LaraSetting::get(..)
    'facade' => 'LaraSetting',
];

```    

## Usage

``` php

LaraSetting::set('group.key', 'setting-value');
    
LaraSetting:: get('group.key');
```

## TODO

* 后台管理 / Setting manage interface
* 自定义缓存驱动 / Custom cache drive 

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jason-xt/lara-setting.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/jason-xt/lara-setting/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/jason-xt/lara-setting.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jason-xt/lara-setting
[link-travis]: https://travis-ci.org/jason-xt/lara-setting
[link-downloads]: https://packagist.org/packages/jason-xt/lara-setting
[link-author]: https://github.com/jason-xt
