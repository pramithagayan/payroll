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
        'uf_link'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y H:i');
    }

    public function getUfLinkAttribute($value)
    {
        return '<a href="'.Storage::url('payroll/'.$value).'" class="uf-btn" download>Download</a>';
    }
}
