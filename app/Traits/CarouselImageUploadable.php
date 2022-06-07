<?php

namespace App\Traits;

trait CarouselImageUploadable{

  public static function uploadImage($className, $id, $file)
  {
    $classBaseName = class_basename($className);
    $imagesDirectoryName = lcfirst(\Str::plural($classBaseName));
    $imagesTableName = \Str::snake($imagesDirectoryName) . '_images';
    $foreignKeyColName = lcfirst($classBaseName) . 'Id';

    $originalImagesPath = realpath('./images/'. $imagesDirectoryName .'/slides/original');
    $resizedImagesPath = realpath('./images/' . $imagesDirectoryName . '/slides/preview');

    $extension = $file -> getClientOriginalExtension();
    $fileName = md5(microtime()) . mt_rand(1, 10000) . '.' . $extension;

    $file -> move($originalImagesPath, $fileName);

    $originalImageFullName = $originalImagesPath . '/' . $fileName;
    $toResizeImageFullName = $resizedImagesPath . '/' . $fileName;

    \File::copy($originalImageFullName, $toResizeImageFullName);

    $resizedOriginalImage = \Image::make($originalImageFullName);
    $resizedOriginalImage -> resize(600, 600) -> save();

    $resizedPreviewImage = \Image::make($toResizeImageFullName);
    $resizedPreviewImage -> resize(300, 300) -> save();
    $httpPreviewPath = '/images/' . $imagesDirectoryName . '/slides/preview/';
    $httpOriginalPath = '/images/' . $imagesDirectoryName . '/slides/original/';

    \DB::table($imagesTableName) -> insert([$foreignKeyColName => $id, 'image' => $fileName]);

    $controlData['id'] = \DB::table($imagesTableName) -> max('id');
    $controlData['previewSrc'] = $httpPreviewPath . $fileName;
    $controlData['originalSrc'] = $httpOriginalPath . $fileName;

    return $controlData;
  }
}
