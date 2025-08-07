<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caracteristique extends Model
{
    protected $table = 'caracteristique';

protected $fillable = ['opt_id', 'project_id'];

public function Option(){
    return $this->belongsTo(Option::class,'opt_id');
}
public function project(){
    return $this->belongsTo(project::class,'project_id');
}

}
