<?php

namespace App\Traits;

trait RecordDeletable{

  public static function deleteRecord($className, $id)
  {
      $object = $className::findOrFail($id);

      $classBaseName = class_basename($className);
      $imagesDirectoryName = lcfirst(\Str::plural($classBaseName));
      $imagesTableName = \Str::snake($imagesDirectoryName) . '_images';
      $foreignKeyColName = lcfirst($classBaseName) . 'Id';

      $mainImage = $object -> mainImage;
      $imagesDir = realpath('./images/' . $imagesDirectoryName);

      $originalMainImageFullPath = $imagesDir . '/main/original/' . $mainImage;
      $resizedMainImageFullPath = $imagesDir . '/main/preview/' . $mainImage;

      $originalSlideImagesDir = $imagesDir . '/slides/original/';
      $resizedSlideImagesDir = $imagesDir . '/slides/preview/';

      $slideImagesQuery = \DB::table($imagesTableName) -> where($foreignKeyColName, $id);
      $slideImages = $slideImagesQuery -> get();

      $slideImages -> each(function($image) use ($originalSlideImagesDir, $resizedSlideImagesDir){

          $originalSlideImageFullPath = $originalSlideImagesDir . $image -> image;
          $resizedSlideImageFullPath = $resizedSlideImagesDir . $image -> image;

          if(\File::exists($originalSlideImageFullPath))
          {
            \File::delete($originalSlideImageFullPath);
          }

          if(\File::exists($resizedSlideImageFullPath))
          {
            \File::delete($resizedSlideImageFullPath);
          }
      });

      if(\File::exists($originalMainImageFullPath))
      {
        \File::delete($originalMainImageFullPath);
      }

      if(\File::exists($resizedMainImageFullPath))
      {
        \File::delete($resizedMainImageFullPath);
      }

      $slideImagesQuery -> delete();

      $object -> delete();
  }
}
