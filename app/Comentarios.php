<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comentarios extends Model
{
    protected $table = 'comentarios';
    
    public function users(){
        return $this->belongsTo('app\User');
    }
    public function productos(){
        return $this->belongsTo('app\Productos');
    }
}
