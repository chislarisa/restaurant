<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FoodsCategories extends Model
{
    protected $fillable = ['category_id', 'food_id'];
}
