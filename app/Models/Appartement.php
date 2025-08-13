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

    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function projet()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Accessors for English attribute names

    public function getFloorAttribute()
    {
        return $this->attributes['etage'];
    }

    public function getSurfaceAttribute()
    {
        return $this->attributes['superfice'];
    }

    public function getCategoryIdAttribute()
    {
        return $this->attributes['categorie_id'];
    }

    public function getPriceAttribute()
    {
        return $this->attributes['prix'];
    }

    public function getPlanAttribute()
    {
        return $this->attributes['plan'];
    }

    public function getViewAttribute()
    {
        return $this->attributes['vue'];
    }

    public function getProjectIdAttribute()
    {
        return $this->attributes['project_id'];
    }
}
