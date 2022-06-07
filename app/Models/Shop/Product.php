<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  public function stockType()
  {
    return $this -> belongsTo(StockType::class, 'stockTypeId');
  }

  public function condition()
  {
    return $this -> belongsTo(Condition::class, 'conditionId');
  }

  public function warranty()
  {
    return $this -> belongsTo(Warranty::class, 'warrantyId');
  }
}
