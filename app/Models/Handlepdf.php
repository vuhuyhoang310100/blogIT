<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Handlepdf extends Model
{
    protected $table = 'handle_pdfs';
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function handlePdfs()
    {
        return $this->hasMany(HandlePdf::class);
    }
}
