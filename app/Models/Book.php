<?php

namespace App\Models;

use DateTime;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Creating localScope :a general set of rule which can be reuse
     */
    public function scopeTitle(Builder $query, string $title): Builder
    {
        return $query->where('title', 'LIKE', '%'.$title.'%');
    }

    /***
     * Get Popular Book with highest reviews count
     */
    public function scopePopular(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withCount([
            'reviews' => fn (Builder $q) => $this->getDateRangeFilter($q, $from, $to),
        ])
            ->orderBy('reviews_count', 'desc');
    }

    /**
     * Local Scope: Get highest rated book
     */
    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder
    {
        return $query->withAvg(
            [
                'reviews' => fn (Builder $q) => $this->getDateRangeFilter($q, $from, $to),
            ],
            'rating'
        )
            ->orderBy('reviews_avg_rating', 'desc');
    }

    private function getDateRangeFilter(Builder $query, $from = null, $to = null)
    {
        $from = $from ?? DateTime::createFromFormat('Y-m-d', $from ?? '');
        $to = $to ?? DateTime::createFromFormat('Y-m-d', $to ?? '');

        if ($from && ! $to) {
            $query->where('created_at', '>=', $from);
        } elseif (! $from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
    }
}
