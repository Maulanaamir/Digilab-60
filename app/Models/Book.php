<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'title',
        'author',
        'published_year',
        'stock',
        'category_id'
    ];

    public function category() {
        return $this->belongsto(Category::class);
    }

    public function loans() {
        return $this->hasMany(Loan::class);
    }
}
