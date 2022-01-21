<?php

use System\Core;
use System\Models\Ban;
use System\Models\Cache;
use System\Singletons\PageLoader;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require_once "vendor/autoload.php";

Core::init();

const ITEMS_PER_PAGE = 10;


$search = "";

if ( isset( $_GET[ 'search' ] ) )
{
    $search = htmlspecialchars( $_GET[ 'search' ] );
}

$bans = Ban::join(Cache::$theTable, Ban::$theTable . '.perp_steamid', '=', Cache::$theTable.'.steamid' )
    ->where( Cache::$theTable.'.name', 'like', '%'.$search.'%' )
    ->orWhere( 'perp_steamid', 'like', '%'.$search.'%' );

$page = 1;
$offset = 0;
$amountOfBans = $bans->count();
$amountOfPages = (int)( $amountOfBans / ITEMS_PER_PAGE ) + 1;

if ( isset( $_GET[ 'page' ] ) && is_numeric( $_GET[ 'page' ] ) )
{

    $page = $_GET[ 'page' ];
    $offset = ( $page - 1 ) * ITEMS_PER_PAGE;

    if ( $offset > $amountOfBans || $offset < 0 )
    {
        $page = $amountOfPages;
        $offset = ( $page - 1 ) * ITEMS_PER_PAGE;
    }
}

$bans = $bans->orderByDesc( 'timestamp' )->limit( ITEMS_PER_PAGE )->offset( $offset )->get();


$pageData = [
    'bans' => $bans,
    'amountOfBans' => $amountOfBans,
    'itemsPerPage' => ITEMS_PER_PAGE,
    'page' => $page,
    'offset' => $offset,
    'previousPage' => ( $page - 1 > 0 ) ? $page - 1 : 0,
    'nextPage' => ( $page + 1 <= $amountOfPages ) ? $page + 1 : 0,
    'firstPage' => 1,
    'lastPage' => $amountOfPages,
    'search' => $search
];

$pages = [];
for ( $i = 1; $i <= $amountOfPages; $i++ )
{
    $pages[] = [
        "id" => $i,
        "active" => $i == $page,
        "disabled" => false
    ];
}

//$pageData['pages'][];

function getPage ( &$pages, $id )
{
    foreach ( $pages as $p )
    {
        if ( $p[ "id" ] == $id )
        {
            return $p;
        }
    }

    return null;
}

$max = 2;
for ( $i = -2; $i <= $max; $i++ )
{
    $p = getPage( $pages, $page + $i );
    if ( $p == null )
    {
        continue;
    }
    $pageData[ 'pages' ][] = $p;
    if ( $i == $max && sizeof( $pageData[ 'pages' ] ) != 5 )
    {
        $max++;
    }
}

try
{
    PageLoader::getInstance()->render( 'banlist.twig', $pageData );
}
catch ( LoaderError | RuntimeError | SyntaxError $e )
{
    die( $e->getMessage() );
}

?>

<pre>
</pre>