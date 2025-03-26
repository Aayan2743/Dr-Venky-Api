<?php

namespace App\Helpers;

class CustomHelper
{
    public static function formatDate($date)
    {
        return \Carbon\Carbon::parse($date)->format('d-m-Y');
    }

    public static function generateSlug($string)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }
    
    
    
    
    
}
