<?php

namespace App\Services;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class Storage
{
    public function uploads(string $directory): Filesystem
    {
        $adapter = new Local(ROOT . '/public/files/' . $directory);

        return new Filesystem($adapter);
    }
}