<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatrolSubmission extends Model
{
    protected $fillable = [
        'user_id','barcode','area','photo_path','lat','lng','address','submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function user(){
    return $this->belongsTo(\App\Models\User::class);
    
    }

}




