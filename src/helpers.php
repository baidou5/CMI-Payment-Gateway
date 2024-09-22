<?php

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
