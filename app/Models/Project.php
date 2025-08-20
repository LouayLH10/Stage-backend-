<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'presentation',
        'regionId',
        'numberOfAppartements',
        'surface',
        'coverphoto',
        'galleryimages',
        'galleryvideos',
        'typeId',
        'logo',
        'email',
        'userId', // clé étrangère vers le promoteur
        
    ];

    protected $casts = [

        'galleryimages' => 'array',
        'galleryvideos' => 'array',
        'surface' => 'float',
        'numberOfAppartements' => 'integer',
    ];

    /**
     * Relation vers le promoteur (utilisateur).
     */
    public function promoteur()
    {
        return $this->belongsTo(User::class, 'userId');
    }
   public function type()
    {
        return $this->belongsTo(Type::class, 'typeId');
    }
    /**
     * Relation vers les appartements du projet.
     */
    public function appartements()
    {
        return $this->hasMany(Appartement::class);
    }
    public function Features (){
        return $this->hasMany(Feature::class,'projectId');
    }
public function user()
{
    return $this->belongsTo(User::class,'userId');
}
public function Region(){
            return $this->belongsTo(Region::class, 'regionId');

}
}
