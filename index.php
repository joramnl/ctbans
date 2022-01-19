<?php

use System\Core;
use System\Models\Ban;
use System\Singletons\PageLoader;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

require_once "vendor/autoload.php";

Core::init();

const ITEMS_PER_PAGE = 50;

$page = 1;
$offset = 0;
$amountOfBans = Ban::count();
$amountOfPages = (int)( $amountOfBans / ITEMS_PER_PAGE ) + 1;

if ( isset( $_GET[ 'page' ] ) && is_numeric( $_GET[ 'page' ] ) )
{

    $page = $_GET[ 'page' ];
    $offset = ( $page - 1 ) * ITEMS_PER_PAGE;

    if ($offset > $amountOfBans || $offset < 0)
    {
        $page = $amountOfPages;
        $offset = ( $page - 1 ) * ITEMS_PER_PAGE;
    }
}


$pageData = [
    'bans' => Ban::orderByDesc( 'timestamp' )->limit( ITEMS_PER_PAGE )->offset( $offset )->get(),
    'amountOfBans' => $amountOfBans,
    'itemsPerPage' => ITEMS_PER_PAGE,
    'page' => $page,
    'offset' => $offset,
    'previousPage' => ($page - 1 > 0) ? $page - 1 : 0,
    'nextPage' => ($page + 1 > $amountOfPages) ? $page - 1 : 0
];

for ($i = 1; $i <= $amountOfPages; $i++)
{
    $pageData['pages'][] = [
        "id" => $i,
        "active" => $i == $page
    ];
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