<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = ['name', 'label', 'subject', 'body'];

    public static function forName(string $name): ?self
    {
        return static::where('name', $name)->first();
    }
}
