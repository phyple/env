<?php

use Phyple\Env\Facades\Env;


if (!function_exists('env')) {

    /**
     * Get environment variable data
     *
     * @param $key
     * @param mixed|null $default
     * @return mixed
     */
    function env($key, mixed $default = null): mixed
    {
        return Env::getEnv($key, $default);
    }
}

if (!function_exists('add_env')) {

    /**
     * Add new environment variable
     *
     * @param ...$data
     * @return void
     */
    function add_env(...$data): void
    {
        foreach ($data as $key => $value) {
            Env::addEnv($key, $value);
        }
    }
}