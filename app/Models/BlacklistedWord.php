<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BlacklistedWord extends Model implements Auditable
{
    use HasFactory,\OwenIt\Auditing\Auditable;

    protected $fillable = ['word', 'user_id', 'is_enabled', 'added_through'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d h:m:s',
        ];
    }
}
