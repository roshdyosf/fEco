<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
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
