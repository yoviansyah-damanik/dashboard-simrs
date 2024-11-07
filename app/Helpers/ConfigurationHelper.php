<?php

namespace App\Helpers;

use App\Models\Configuration;
use Illuminate\Support\Facades\Schema;

class ConfigurationHelper
{
    private static $configs;

    public function __construct()
    {
        if (Schema::hasTable('configurations'))
            static::$configs = Configuration::get();
    }

    public static function get($config)
    {
        return collect(static::$configs)
            ->where('attribute', $config)
            ->first()
            ->value;
    }
}
