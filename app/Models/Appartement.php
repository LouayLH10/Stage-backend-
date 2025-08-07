<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appartement extends Model
{
    use HasFactory;

    protected $fillable = [
"etage",
"superfice",
"categorie_id",
"prix",
"plan",
"vue",
"project_id"
    ];

public function Categorie()
{
    return $this->belongsTo(Categorie::class,'categorie_id');
}

   
    public function Projet()
    {
        return $this->belongsTo(User::class, 'project_id');
    }


}
