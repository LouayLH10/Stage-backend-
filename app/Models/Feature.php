<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'Feature';

protected $fillable = ['optionId', 'projectId'];

public function Option(){
    return $this->belongsTo(Option::class,'optionId');
}
public function project(){
    return $this->belongsTo(project::class,'projectId');
}

}
