<?php

namespace Ravuthz\LaravelCrud\Crud;

class Template
{
    public static function generate(string $stub, array $replaces = [])
    {
        if (!empty($replaces)) {
            $search = array_keys($replaces);
            $replace = array_values($replaces);

            return str_replace(
                $search,
                $replace,
                file_get_contents($stub)
            );
        }
        return "";
    }

    public static function write(string $file, string $template)
    {
        file_put_contents(base_path($file), $template);
    }
}