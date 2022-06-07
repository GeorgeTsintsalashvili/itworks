<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class MessageBox extends Model
{
    protected $table = 'shop_message_boxes';

    public function messages()
    {
      return Message::where('shop_message_box_id', $this -> id);
    }
}
