<?php

namespace App\Models\ControlPanel;

use Illuminate\Database\Eloquent\Model;
use App\Library\Paginator;

class NetworkDevice extends Model
{
  public $timestamps = false;
  protected $fillable = ['title', 'price', 'discount', 'conditionId', 'stockTypeId', 'visibility'];
}
