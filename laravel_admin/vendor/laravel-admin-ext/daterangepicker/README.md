Integrates daterangepicker into laravel-admin
======

This is an extension to integrates [Date Range Picker](http://www.daterangepicker.com/) into laravel-admin.

## Screenshot

![qq20180914-014825](https://user-images.githubusercontent.com/1479100/45505950-8521f880-b7c0-11e8-97cf-4a792e8f4c0e.png)

## Installation 

```bash
composer require laravel-admin-ext/daterangepicker

php artisan vendor:publish --tag=laravel-admin-daterangepicker
```

## Configurations

Open `config/admin.php`, add configurations that belong to this extension at `extensions` section.
```php

    'extensions' => [

        'daterangepicker' => [
        
            // Set to `false` if you want to disable this extension
            'enable' => true,
            
            // Find more configurations http://www.daterangepicker.com/
            'config' => [
            
            ]
        ]
    ]

```

## Usage

Use it in the form:
```php
//Single column

$form->daterangepicker('date_range', 'Date range');

// Predefine Date Ranges.
$form->daterangepicker('date_range', 'Date range')
    ->ranges([
        'Today'        => [Carbon::today()->toDateString(), Carbon::today()->toDateString()],
        'Yesterday'    => [Carbon::yesterday()->toDateString(), Carbon::yesterday()->toDateString()],
        'Last 7 Days'  => [Carbon::today()->subDays(6)->toDateString(), Carbon::today()->toDateString()],
        'Last 14 Days' => [Carbon::today()->subDays(13)->toDateString(), Carbon::today()->toDateString()],
        'Last 30 Days' => [Carbon::today()->subDays(29)->toDateString(), Carbon::today()->toDateString()],
        'This Month'   => [Carbon::today()->startOfMonth()->toDateString(), Carbon::today()->endOfMonth()->toDateString()],
        'Last Month'   => [Carbon::today()->subMonth()->firstOfMonth()->toDateString(), Carbon::today()->subMonth()->lastOfMonth()->toDateString()],
    ]);
    
// multilpe column
$form->daterangepicker(['created_at', 'updated_at'], 'Date range');
```

## Donate

> Help keeping the project development going, by donating a little. Thanks in advance.

[![PayPal Me](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/zousong)

![-1](https://cloud.githubusercontent.com/assets/1479100/23287423/45c68202-fa78-11e6-8125-3e365101a313.jpg)

License
------------
Licensed under [The MIT License (MIT)](LICENSE).
