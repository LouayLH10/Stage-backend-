<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appartement extends Model
{
    use HasFactory;

    protected $fillable = [
        "floor",
        "surface",
        "categoryId",
        "price",
        "plan",
        "view",
        "projectId"
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }

    public function projet()
    {
        return $this->belongsTo(Project::class, 'projectId');
    }

    // Accessors for English attribute names

    public function getFloorAttribute()
    {
        return $this->attributes['floor'];
    }

    public function getSurfaceAttribute()
    {
        return $this->attributes['surface'];
    }

    public function getCategoryIdAttribute()
    {
        return $this->attributes['categoryId'];
    }

    public function getPriceAttribute()
    {
        return $this->attributes['price'];
    }

    public function getPlanAttribute()
    {
        return $this->attributes['plan'];
    }

    public function getViewAttribute()
    {
        return $this->attributes['view'];
    }

    public function getProjectIdAttribute()
    {
        return $this->attributes['projectId'];
    }
}
