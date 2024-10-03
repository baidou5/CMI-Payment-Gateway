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
 if (!function_exists('cmi_generate_order_id')) {
     function cmi_generate_order_id()
     {
         return 'ORD-' . strtoupper(uniqid());
     }
 }

 if (!function_exists('cmi_format_amount')) {
     function cmi_format_amount($amount)
     {
         return number_format($amount, 2, '.', '');
     }
 }
