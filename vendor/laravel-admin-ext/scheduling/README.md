Scheduling task manager for laravel-admin
============================

[![StyleCI](https://styleci.io/repos/99676857/shield?branch=master)](https://styleci.io/repos/99676857)
[![Packagist](https://img.shields.io/packagist/l/laravel-admin-ext/scheduling.svg?maxAge=2592000)](https://packagist.org/packages/laravel-admin-ext/scheduling)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-admin-ext/scheduling.svg?style=flat-square)](https://packagist.org/packages/laravel-admin-ext/scheduling)
[![Pull request welcome](https://img.shields.io/badge/pr-welcome-green.svg?style=flat-square)]()

A web interface for manage task scheduling in laravel.

[Documentation](http://laravel-admin.org/docs/#/en/extension-scheduling) | [中文文档](http://laravel-admin.org/docs/#/zh/extension-scheduling)

## Screenshot

![wx20170810-101048](https://user-images.githubusercontent.com/1479100/29151552-8affc0b2-7db4-11e7-932a-a10d8a42ec50.png)

## Installation

```
$ composer require laravel-admin-ext/scheduling


$ php artisan admin:import scheduling
```

Open `http://your-host/admin/scheduling`.

Try to add a scheduling task in `app/Console/Kernel.php` like this:

```php
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')->everyTenMinutes();
        
        $schedule->command('route:list')->dailyAt('02:00');
    }
}

```

And you can find these tasks in scheduling panel.

License
------------
Licensed under [The MIT License (MIT)](LICENSE).
