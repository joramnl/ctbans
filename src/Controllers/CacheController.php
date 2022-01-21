<?php

namespace System\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kruzya\SteamIdConverter\SteamID;
use System\Models\Cache;

class CacheController
{
    public static function get ( string $steamid ): Cache
    {
        try
        {
            /**
             * @var $cache Cache
             */
            $cache = Cache::findOrFail( $steamid );
            $expires = Carbon::parse($cache->expires);

            if ($expires < Carbon::now())
            {
                $cache->updateCache();
            }

            return $cache;
        }
        catch ( ModelNotFoundException )
        {
            return self::cache( $steamid );
        }
    }

    private static function cache ( string $steamid ): Cache
    {
        $avatar = new Cache();
        $avatar->steamid = $steamid;
        $avatar->updateCache();
        $avatar->save();

        return $avatar;
    }
}