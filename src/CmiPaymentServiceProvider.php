<?php
/**
 * CMI Payment Gateway Library for Laravel
 *
 * This library provides a simple way to integrate CMI payment gateway into your Laravel application.
 * It allows you to process payments, manage transactions, and handle payment responses from the CMI platform.
 *
 * @package    CMI-Payment-Gateway
 * @version    1.0.0
 * @license    MIT
 * @author     Baidou Abdellah <baidou.abd@gmail.com>
 * @link       https://github.com/baidou5/CMI-Payment-Gateway
 *
 * ### Requirements:
 * - PHP 7.4 or higher
 * - Laravel 8.x or higher
 * - CMI payment account
 *
 * ### License:
 *
 * This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
 */
namespace baidouabdellah\CMIPaymentGateway;

use Illuminate\Support\ServiceProvider;

class CmiPaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
      // Register CmiPayment as Singleton within the app
        $this->app->singleton(CmiPayment::class, function ($app) {
            return new CmiPayment(new CmiPaymentService());
        });

      // Merge configuration file
        $this->mergeConfigFrom(__DIR__.'/../config/cmi.php', 'cmi');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
          // Publish configuration file when installing package
            $this->publishes([
                __DIR__.'/../config/cmi.php' => config_path('cmi.php'),
            ], 'cmi-config');
        }

      // Record tracks
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
