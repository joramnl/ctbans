<?php

namespace System\Singletons;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use Illuminate\Support\Env;
use Kruzya\SteamIdConverter\SteamID;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use VipSystem\Controllers\SessionController;
use VipSystem\Models\User;

/**
 * Represents a Steam auth session.
 *
 * @property string $steamid
 * @property string $nickname
 * @property string $avatar
 * @property string $loggedIn
 */
class PageLoader extends Singleton
{
    /**
     * @var Environment|null
     */
    private ?Environment $twig;

    public function init (): void
    {
        $this->twig = new Environment(
            new FilesystemLoader( getcwd() . '/src/Templates' ),
            [
                'debug' => true
            ]
        );

        $this->addTwigFilters();
    }

    private function addTwigFilters (): void
    {
        $this->twig->addFilter( new TwigFilter( 'humanDiff', function ( int $timestamp )
        {
            $date = Carbon::createFromTimestamp( $timestamp );

            if ( Carbon::now()->subDay() < $date )
            {
                return $date->diffForHumans();
            }
            else
            {
                return $date->format( 'm/d/Y H:i' );
            }
        } ) );

        $this->twig->addFilter( new TwigFilter( 'minutesToHumanReadable', function ( int $minutes )
        {
            return CarbonInterval::minutes( $minutes )->cascade()->forHumans();
        } ) );
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function render ( string $template, array $pageData ): void
    {
        $data = [
            'layout' => $template
        ];

        $data = array_merge( $data, $pageData );
        echo $this->twig->render( 'base.twig', $data );
    }
}