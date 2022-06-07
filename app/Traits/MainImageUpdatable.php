<?php

namespace App\Traits;

trait MainImageUpdatable{

  public static function updateImage($className, $id, $file)
  {
    $record = $className::findOrFail($id);

    $classBaseName = class_basename($className);
    $imagesDirectoryName = lcfirst(\Str::plural($classBaseName));

    $originalImagesPath = realpath('./images/' . $imagesDirectoryName . '/main/original');
    $resizedImagesPath = realpath('./images/' . $imagesDirectoryName . '/main/preview');

    $oldImageName = $record -> mainImage;
    $oldPreviewImageFullName = $resizedImagesPath . '/' . $oldImageName;
    $oldOriginalImageFullName = $originalImagesPath . '/' . $oldImageName;

    if(file_exists($oldPreviewImageFullName) && file_exists($oldOriginalImageFullName))
    {
      \File::delete($oldPreviewImageFullName);
      \File::delete($oldOriginalImageFullName);
    }

    $watermarkPath = realpath('./images/general');
    $watermarkFullName = $watermarkPath . '/watermark.png';

    $extension = $file -> getClientOriginalExtension();
    $fileName = md5(microtime()) . mt_rand(1, 10000) . '.' . $extension;

    $file -> move($originalImagesPath, $fileName);

    $originalImageFullName = $originalImagesPath . '/' . $fileName;
    $toResizeImageFullName = $resizedImagesPath . '/' . $fileName;

    \File::copy($originalImageFullName, $toResizeImageFullName);

    $resizedPreviewImage = \Image::make($toResizeImageFullName);
    $resizedPreviewImage -> resize(400, 400);

    $resizedOriginalImage = \Image::make($originalImageFullName);
    $resizedOriginalImage -> resize(600, 600);

    if(file_exists($watermarkFullName))
    {
        $resizedOriginalImage -> insert($watermarkFullName, 'bottom-right', 150, 240);
        $resizedPreviewImage -> insert($watermarkFullName, 'bottom-right', 50, 150);
    }

    $resizedOriginalImage -> save();
    $resizedPreviewImage -> save();

    $record -> mainImage = $fileName;

    $record -> save();

    return ['newPreviewSrc' => '/images/' . $imagesDirectoryName . '/main/preview/' . $fileName,
            'oldPreviewSrc' => '/images/'. $imagesDirectoryName .'/main/preview/' . $oldImageName ];
  }
}
