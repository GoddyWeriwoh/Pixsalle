<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

final class LandController{
    private Twig $twig;
    
    public function __construct(Twig $twig) {
        $this->twig = $twig;
    }

    public function showLanding(Request $request, Response $response): Response
    {
        return $this->twig->render(
            $response,
            'landing.twig',
            []
        );
    }
}