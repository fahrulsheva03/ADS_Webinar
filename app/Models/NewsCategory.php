<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    use HasFactory;

    protected $table = 'news_categories';

    protected $fillable = [
        'nama',
        'slug',
    ];

    public function news()
    {
        return $this->hasMany(News::class, 'news_category_id');
    }
}
