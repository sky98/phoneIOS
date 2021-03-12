<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name', 'phoneNumber', 'user_id',
    ];
    protected $hidden = [
        'id'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
}
