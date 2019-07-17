<?php

namespace App\Models;

use App\Services\Storage;
use League\Flysystem\Filesystem;
use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

class ImageModel
{
    /** @var int */
    public $id;

    /** @var string */
    public $directory;

    /** @var string */
    public $extension;

    /** @var int */
    public $createdAt;

    public static function fromBean(OODBBean $bean): ImageModel
    {
        $model = new ImageModel;
        $model->id = (int)$bean->id;
        $model->directory = $bean->directory;
        $model->extension = $bean->extension;
        $model->createdAt = (int)$bean->created_at;

        return $model;
    }

    /**
     * @param int $imageId
     * @return ImageModel|null
     */
    public static function getById(int $imageId)
    {
        $bean = R::findOne('uploads', 'id = ?', [$imageId]);
        if ($bean) {
            return self::fromBean($bean);
        }

        return null;
    }


    public function save()
    {
        $bean = R::dispense('uploads');

        if ($this->id) {
            $bean->id = $this->id;
        }

        $bean->directory = $this->directory;
        $bean->extension = $this->extension;
        $bean->created_at = $this->createdAt;

        R::store($bean);

        $this->id = (int)$bean->id;

        return true;
    }

    public function getLargeFilename(): string
    {
        return $this->getFilename('l');
    }

    public function getSmallFilename(): string
    {
        return $this->getFilename('s');
    }

    public function getLargeUrl(): string
    {
        return $this->getUrl('l');
    }

    public function getSmallUrl(): string
    {
        return $this->getUrl('s');
    }

    public function getUrl($suffix): string
    {
        return asset('files/' . $this->directory . '/' . $this->getFilename($suffix), true);
    }

    public function getStorage(): Filesystem
    {
        return (new Storage)->uploads($this->directory);
    }

    private function getFilename($suffix): string
    {
        return "{$this->id}_{$suffix}_" . md5($this->id) . ".{$this->extension}";
    }
}