<?php

namespace App\Traits;

trait BaseDataUpdatable {

  public static function updateBaseData($class, $data)
  {
    $object = $class::findOrFail($data['record-id']);

    $columnsToExcept = ['visibility'];

    if($data['visibility'] != $object -> visibility)
    {
      date_default_timezone_set('Asia/Tbilisi');

      if($object -> visibility == 0)
      {
        $object -> timestamp = date('Y-m-d H:i:s');
      }

      $object -> visibility = $data['visibility'];

      $object -> save();
    }

    if($data['title'] == $object -> title)
    {
      $columnsToExcept[] = 'title';
    }

    if($data['price'] == $object -> price)
    {
      $columnsToExcept[] = 'price';
    }

    if($data['discount'] == $object -> discount)
    {
      $columnsToExcept[] = 'discount';
    }

    if($data['conditionId'] == $object -> conditionId)
    {
      $columnsToExcept[] = 'conditionId';
    }

    if($data['stockTypeId'] == $object -> stockTypeId)
    {
      $columnsToExcept[] = 'stockTypeId';
    }

    $object -> update(\Arr::except($data, $columnsToExcept));
  }
}
