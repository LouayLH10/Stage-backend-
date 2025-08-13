<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
        protected $table="region";
    protected $fillable = [
"nom_region",
"ville_id"
    ];
     public $timestamps = false;
    
    public function Ville()
{
    return $this->belongsTo(Ville::class,'ville_id');
}
}
