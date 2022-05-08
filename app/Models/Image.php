<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'extension',
        'mime',
        'size',
    ];

    /**
     * @return MorphTo
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
