<?php

namespace System\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kruzya\SteamIdConverter\SteamID;
use System\Models\Avatar;

class AvatarController
{
    public static function getAvatar ( SteamID $steamid ): Avatar
    {
        try
        {
            $avatar = Avatar::findOrFail( $steamid->communityId() );
            $expires = Carbon::parse($avatar->expires);

            if ($expires < Carbon::now())
            {
                $avatar->updateAvatar();
            }

            return $avatar;
        }
        catch ( ModelNotFoundException )
        {
            return self::createAvatar( $steamid );
        }
    }

    private static function createAvatar ( SteamID $steamid ): Avatar
    {
        $avatar = new Avatar();
        $avatar->steamid = $steamid->communityId();
        $avatar->updateAvatar();
        $avatar->save();

        return $avatar;
    }
}