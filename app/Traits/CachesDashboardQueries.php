<?php

namespace App\Traits;

use Auth;
use Cache;

trait CachesDashboardQueries
{
    /**
     * Logged-in user's company/state/district scope, without writing to session.
     */
    protected function dashboardScope()
    {
        $user = Auth::user();

        return array(
            'user'        => $user,
            'company_id'  => $user->company_id,
            'state_id'    => $user->state_id,
            'district_id' => $user->district_id,
        );
    }

    /**
     * Cache a dashboard response, keyed by endpoint + user scope + request query params.
     */
    protected function dashboardCache($key, $ttl, \Closure $callback)
    {
        $scope = $this->dashboardScope();

        $cacheKey = sprintf(
            'dashboard:%s:%s:%s:%s:%s',
            $key,
            $scope['company_id'],
            $scope['state_id'],
            $scope['district_id'],
            md5(json_encode(request()->query()))
        );

        return Cache::remember($cacheKey, $ttl, $callback);
    }
}
