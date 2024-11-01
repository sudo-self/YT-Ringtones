<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConvertedFile extends Model
{
    
    protected $table = 'converted_files';

    protected $fillable = [
        'original_url',
        'file_name',
        'file_path',
        'file_type',
    ];

  
    protected $casts = [
        'file_type' => 'string',
    ];

  
    public function getFileUrlAttribute()
    {
        return asset($this->file_path);
    }
}

