<?php

namespace Services;

use Classes\Collections\EntityCollection;
use Classes\Router;
use Classes\Slider;
use Classes\Interfaces\SliderableInterface;
use Exceptions\AssetsServiceException;
use Repositories\Configuration;
class SliderService
{
    public static function generateSlider(string $sliderClass, string $imagesPath, ?string $template = null): string
    {
        if (!class_exists($sliderClass)) {
            throw new AssetsServiceException("The class $sliderClass does not exist.");
        }

        $slider = new Slider($sliderClass);
        $images = AssetsService::getAllImagesRelativeInFolder($imagesPath);
        foreach ($images as $pos => $image) {
            $name = str_replace($imagesPath, '', $image['name']);
            $nameSnakeCase = strtolower(str_replace(['/', ' ', '.'], '_', $name));
            $description = Configuration::get("slider_description_$nameSnakeCase", $pos);
            $lien = Configuration::get("slider_lien_$nameSnakeCase", $pos);
            /** @var SliderableInterface $entity */
            $entity = new $sliderClass(
                $name,
                '',
                $description,
                Router::generateUrl('app_media_image', ['path' => $image['name'], 'extension' => $image['extension']]),
                $lien,
                $pos === 0
            );
            $slider->add($entity);
        }

        return $slider->renderSlider($template);
    }

    public static function generateSliderFromModel(string $modelClass, ?string $template = null): string
    {

        if (!class_exists($modelClass)) {
            throw new AssetsServiceException("The class $modelClass does not exist.");
        }
        if (!class_implements($modelClass) || !in_array(SliderableInterface::class, class_implements($modelClass))) {
            throw new AssetsServiceException("The entity must implement the SliderableInterface.");
        }

        $slider = new Slider($modelClass);
        $repository = str_replace(['Models', 'Model'], ['Repositories', 'Repository'], $modelClass);

        /**
         * @var EntityCollection $entities
         */
        $entities = $repository::getAll();
        foreach ($entities->toArray()  as $pos => $entity) {
            $slider->add($entity);
        }

        return $slider->renderSlider($template);
    }
}