<?php


namespace App\Services;


use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManager;
use InvalidArgumentException;
use RedBeanPHP\R;

class ImageService
{
    /**
     * Upload an image
     * @param string $pathname
     * @return int Image id
     */
    public function create(string $pathname): int
    {
        $manager = new ImageManager(['driver' => 'gd']);

        try {
            $image = $manager->make($pathname);
        } catch (NotReadableException $e) {
            throw new InvalidArgumentException('Failed to read the image');
        }

        $image->fit(300, 300);

        $bean = R::dispense('images');
        $bean->image = base64_encode($image->encode('jpg')->getEncoded());
        R::store($bean);

        return $bean->id;
    }

    /**
     * @param int $imageId
     * @return string|null base64 URI
     */
    public function getImageContent(int $imageId)
    {
        $bean = R::findOne('images', 'id = ?', [$imageId]);
        if (!$bean) {
            return null;
        }

        return base64_decode($bean->image);
    }
}