<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Topic extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class);
    }

    public function scopeName(Builder $query, string $name)
    {
        return $query->where('name', self::normalizeName($name));
    }

    public static function normalizeName(string $name)
    {
        return Str::slug(preg_replace('/(%20)+/', ' ', $name));
    }
}
