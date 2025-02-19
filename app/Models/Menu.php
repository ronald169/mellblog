<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    // use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    public function submenus(): HasMany
    {
        return $this->hasMany(Submenu::class);
    }
}
