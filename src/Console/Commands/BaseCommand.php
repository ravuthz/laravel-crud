<?php

namespace Ravuthz\LaravelCrud\Console\Commands;

use Illuminate\Console\Command;
use Ravuthz\LaravelCrud\Crud\Template;

abstract class BaseCommand extends Command
{
    public function writeInfo($template, ...$args)
    {
        $this->components->info(sprintf($template, ...$args));
    }

    /**
     * @throws \Exception
     */
    public function writeError($template, ...$args)
    {
        $this->components->error(sprintf($template, ...$args));
        throw new \Exception(sprintf($template, ...$args));
    }

    public function createTemplate(string $type, string $path, $template)
    {
        $this->createDirectory($path);

        if (file_exists(base_path($path))) {
            $this->writeError('%s [%s] already exists.', $type, $path);
        }

        Template::write($path, $template);
        $this->writeInfo('%s [%s] created successfully.', $type, $path);
    }

    public function updateTemplate(string $type, string $path, $template)
    {
        $this->createDirectory($path);

        if (!file_exists(base_path($path))) {
            $this->writeError('%s [%s] not exists.', $type, $path);
        }

        Template::write($path, $template);
        $this->writeInfo('%s [%s] updated successfully.', $type, $path);
    }

    public function createDirectory($path)
    {
        $directory = dirname(base_path($path));

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                $this->writeError('Failed to create directory: %s', $directory);
                return;
            }
        }
    }
}
