<?php

namespace Ravuthz\LaravelCrud\Crud;

class Template
{
    public static function generate(string $stub, array $replaces = [])
    {
        $template = file_get_contents($stub);

        if (empty($replaces)) {
            return $template;
        }

        return str_replace(
            array_keys($replaces),
            array_values($replaces),
            $template
        );
    }

    public static function write(string $file, string $template)
    {
        file_put_contents(base_path($file), $template);
    }
}
