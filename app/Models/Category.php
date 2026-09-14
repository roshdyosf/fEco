<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;



    protected $fillable = [
        'family_id',
        'name',
        'type',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
