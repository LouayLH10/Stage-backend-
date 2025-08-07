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
        'ville_id',
        'nb_appartements',
        'surface',
        'photo_couverture',
        'gallerie_images',
        'gallerie_videos',
        'logo',
        'email',
        'user_id', // clé étrangère vers le promoteur
        
    ];

    protected $casts = [

        'gallerie_images' => 'array',
        'gallerie_videos' => 'array',
        'surface' => 'float',
        'nb_appartements' => 'integer',
    ];

    /**
     * Relation vers le promoteur (utilisateur).
     */
    public function promoteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation vers les appartements du projet.
     */
    public function appartements()
    {
        return $this->hasMany(Appartement::class);
    }
    public function caracteristiques (){
        return $this->hasMany(Caracteristique::class);
    }
public function user()
{
    return $this->belongsTo(User::class);
}
public function Ville(){
            return $this->belongsTo(Ville::class, 'ville_id');

}
}
