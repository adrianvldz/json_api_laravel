<?php

namespace app\Models\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    public function getIncrementing()
    {
        return false;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = Str::uuid()->toString();
        });

    }
}
