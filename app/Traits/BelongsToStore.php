<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToStore
{
    protected static function bootBelongsToStore(): void
    {
        static::addGlobalScope('store', function (Builder $builder) {
            $storeId = session('current_store_id') ?? app()->has('current_store') ? app('current_store')->id : null;
            if ($storeId && ! app()->runningInConsole()) {
                $builder->where($builder->getModel()->getTable().'.store_id', $storeId);
            }
        });

        static::creating(function ($model) {
            if (! $model->store_id && session()->has('current_store_id')) {
                $model->store_id = session('current_store_id');
            }
        });
    }

    public function scopeForStore(Builder $query, $storeId)
    {
        return $query->withoutGlobalScope('store')->where('store_id', $storeId);
    }
}
