<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    use HasFactory;

    protected $table = 'configs';

    protected $fillable = ['key', 'value'];

    public static function getConfig($key = "")
    {
        return SiteConfig::query()->firstWhere('key', $key)?->value;
    }
}
