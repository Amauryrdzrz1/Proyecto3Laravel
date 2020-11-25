<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $table = 'productos'; 
    
    public function comentarios(){
        return $this->hasMany('app\Comentarios');
    }
    
    public function users(){
        return $this->belongsTo('app\User');
    }
}
