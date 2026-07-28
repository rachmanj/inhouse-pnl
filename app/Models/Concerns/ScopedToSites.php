<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopedToSites
{
    public static function bootScopedToSites(): void
    {
        static::addGlobalScope('site', function (Builder $builder) {
            $user = Auth::user();

            if (! $user) {
                return;
            }

            if ($user->hasRole(['Super Admin', 'Finance Manager'])) {
                return;
            }

            $siteIds = $user->projectSites()->pluck('project_sites.id');

            $builder->whereIn(
                $builder->getModel()->qualifyColumn('project_site_id'),
                $siteIds
            );
        });
    }
}
