<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class option extends Model
{
     use HasFactory;
protected $fillable=[
    'name_opt',
    'icon_opt'
];

}
