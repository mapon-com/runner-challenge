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

    public static function getMaxUploadSize()
    {
        $upload = (int)(ini_get('upload_max_filesize'));
        $post = (int)(ini_get('post_max_size'));
        return min($upload, $post);
    }
}