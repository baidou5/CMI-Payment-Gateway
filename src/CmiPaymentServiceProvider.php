<?php

namespace baidouabdellah\CMIPaymentGateway;

use Illuminate\Support\ServiceProvider;

class CmiPaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        // تسجيل CmiPayment كـ Singleton داخل التطبيق
        $this->app->singleton(CmiPayment::class, function ($app) {
            return new CmiPayment(new CmiPaymentService());
        });

        // دمج ملف التكوين
        $this->mergeConfigFrom(__DIR__.'/../config/cmi.php', 'cmi');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            // نشر ملف التكوين عند تثبيت الحزمة
            $this->publishes([
                __DIR__.'/../config/cmi.php' => config_path('cmi.php'),
            ], 'config');
        }

        // تسجيل المسارات
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
