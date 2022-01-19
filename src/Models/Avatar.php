<?php

namespace System\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $steamid
 * @property $avatar
 * @property $expires
 */
class Avatar extends Model
{
    public $timestamps = false;
    public $primaryKey = "steamid";
    public $table = 'CTBan_Avatars';

    public function updateAvatar ()
    {
        $this->avatar = self::getAvatarFromSteamAPI( $this->steamid );
        $this->expires = Carbon::now()->addWeek()->toDateTimeString();
        $this->save();
    }

    public function getBase64Avatar (): string
    {
        return 'data:image/png;base64,' . $this->avatar;
    }


    private static function getAvatarFromSteamAPI ( int $steamid ): string
    {
        try
        {
            $json = self::url_get_contents( "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . $_ENV[ 'STEAM_APIKEY' ] . "&steamids=" . $steamid );
            $data = json_decode( $json, true );
            $avatar_url = $data[ 'response' ][ 'players' ][ 0 ][ 'avatarfull' ];
            return base64_encode( self::url_get_contents( $avatar_url ) );
        }
        catch ( Exception $e )
        {
            die( "Error getting from steam api: " . $e->getMessage() );
        }
    }

    /**
     * @throws Exception
     */
    private static function url_get_contents ( $Url ): string
    {
        if ( !function_exists( 'curl_init' ) )
        {
            throw new Exception( "CURL is not installed!" );
        }
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $Url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close( $ch );
        return $output;
    }
}