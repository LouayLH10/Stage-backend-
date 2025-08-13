<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ville extends Model
{
    protected $table="ville";
    protected $fillable=[
    "nom_ville"
];
 public $timestamps = false;
        
}
