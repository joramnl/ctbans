<?php

namespace System\Models;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Kruzya\SteamIdConverter\Exception\InvalidSteamIdException;
use Kruzya\SteamIdConverter\SteamID;

/**
 * @property $steamid
 * @property $name
 * @property $avatar
 * @property $expires
 */
class Cache extends Model
{
    public $timestamps = false;
    public $primaryKey = "steamid";
    public $keyType = 'string';
    public $table = 'CTBan_Cache';
    public static $theTable = 'CTBan_Cache';

    public function updateCache ()
    {
        $steamdata = $this->getPlayerSteamData();
        $this->name = $steamdata[ 'personaname' ];
        $this->avatar = $this->getCommunityID() . '.jpg';
        $this->storeAvatar( self::url_get_contents( $steamdata[ 'avatarfull' ] ) );
        $this->expires = Carbon::now()->addWeek()->toDateTimeString();
        $this->save();
    }

    private function storeAvatar ( string $avatar )
    {
        $file = getcwd() . '/img/' . $this->avatar;
        if ( file_exists( $file ) )
        {
            unlink( $file );
        }
        file_put_contents( $file, $avatar );
    }

    private function getCommunityID()
    {

        try
        {
            $steamid = new SteamID( $this->steamid );
            return $steamid->communityId();
        }
        catch ( InvalidSteamIdException )
        {
            die("Steamid error");
        }
    }

    public function getName (): string
    {
        return $this->name;
    }

    public function getAvatarUrl (): string
    {
        return $_ENV[ 'SITE_URL' ] . '/img/' . $this->getCommunityID() . '.jpg';
    }

    private function getPlayerSteamData (): array
    {
        try
        {
            $json = self::url_get_contents( "https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . $_ENV[ 'STEAM_APIKEY' ] . "&steamids=" . $this->getCommunityID() );
            $data = json_decode( $json, true );
            return $data[ 'response' ][ 'players' ][ 0 ];
        }
        catch ( Exception $e )
        {
            die( "Error getting from steam api: " . $e->getMessage() );
        }
    }

    private static function url_get_contents ( $Url ): string
    {
        $ch = curl_init(); // Curl should exist because it's required in composer.jsom
        curl_setopt( $ch, CURLOPT_URL, $Url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $output = curl_exec( $ch );
        curl_close( $ch );
        return $output;
    }
}