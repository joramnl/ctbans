<?php

namespace System\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Kruzya\SteamIdConverter\Exception\InvalidSteamIdException;
use Kruzya\SteamIdConverter\SteamID;
use System\Controllers\AvatarController;

/**
 * @property $ban_id
 * @property $timestamp
 * @property $perp_steamid
 * @property $perp_name
 * @property $admin_steamid
 * @property $admin_name
 * @property $bantime
 * @property $timeleft
 * @property $reason
 *
 * @mixin Builder
 */
class Ban extends Model
{
    public $timestamps = false;
    public $primaryKey = "ban_id";
    public $table = 'CTBan_Log';

    public function getPlayerSteamID() : SteamID
    {
        return $this->getSteamID($this->perp_steamid);
    }

    public function getAdminSteamID() : SteamID
    {
        return $this->getSteamID($this->admin_steamid);
    }

    private function getSteamID(string $steamid) : SteamID
    {
        try
        {
            return new SteamID( $steamid );
        }
        catch ( InvalidSteamIdException )
        {
            die("Steamid error");
        }
    }

    public function getPlayerCommunityID (): int
    {
        return $this->getPlayerSteamID()->communityId();
    }

    public function getAdminCommunityID (): int
    {
        return $this->getPlayerSteamID()->communityId();
    }

    public function getPlayerAvatar() : Avatar
    {
        return AvatarController::getAvatar($this->getPlayerSteamID());
    }

    public function getAdminAvatar() : Avatar
    {
        return AvatarController::getAvatar($this->getAdminSteamID());
    }

    public function getPlayerCommunityProfileUrl (): string
    {
        return $this->getCommunityProfileUrl($this->getPlayerCommunityID());
    }

    public function getAdminCommunityProfileUrl (): string
    {
        return $this->getCommunityProfileUrl($this->getAdminCommunityID());
    }

    private function getCommunityProfileUrl ($id): string
    {
        return "//steamcommunity.com/profiles/" . $id;
    }
}