<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
        protected $table="region";
    protected $fillable = [
"region",
"cityId"
    ];
     public $timestamps = false;
    
    public function City()
{
    return $this->belongsTo(City::class,'cityId');
}
}
