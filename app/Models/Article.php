<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'user_id' => 'string',
    ];

    // public $resourceType = 'articles';

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function scopeYear(Builder $query, $year)
    {
        $query->whereYear('created_at', $year);

    }

    public function scopeMonth(Builder $query, $month)
    {
        $query->whereMonth('created_at', $month);

    }

    public function scopeCategories(Builder $query, $categories)
    {
        $categoriesSlugs = explode(',', $categories);
        $query->whereHas('category', function ($q) use ($categoriesSlugs) {
            $q->whereIn('slug', $categoriesSlugs);
        });
    }

    public function scopeAuthors(Builder $query, $authors)
    {
        $authorNames = explode(',', $authors);
        $query->whereHas('author', function ($q) use ($authorNames) {
            $q->whereIn('name', $authorNames);
        });
    }
}
