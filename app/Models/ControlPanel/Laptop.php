<?php

namespace App\Models\ControlPanel;

use Illuminate\Database\Eloquent\Model;

class Laptop extends Model
{
  public $timestamps = false;
  protected $fillable = ['title', 'price', 'discount', 'conditionId', 'stockTypeId', 'visibility'];
}
