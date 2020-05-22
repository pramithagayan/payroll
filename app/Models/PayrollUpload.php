<?php

namespace App\Models;

use A17\Twill\Models\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PayrollUpload extends Model 
{
    
    protected $fillable = [
        'published',
        'title',
        'of_link',
        'uf_link',
        'user_id'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:ia');
    }

    public function getUserInfoAttribute($value)
    {
        $user = \A17\Twill\Models\User::where('id', $this->user_id)->first();

        return $user['name'];
    }
}
