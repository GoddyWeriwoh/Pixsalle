<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Salle\PixSalle\Repository\UserRepository;
use Slim\Views\Twig;

final class PortController{
    private Twig $twig;
    private UserRepository $userDb;
    
    public function __construct(Twig $twig, UserRepository $userRepository) {
        $this->twig = $twig;
        $this->userDb = $userRepository;
    }

    private function checkLogin(Response &$response): int{
        if (empty($_SESSION["user_id"])){
            //echo "<script>alert('You must be logged in!');</script>";
            $response = $response->withStatus(302)->withHeader('Location', '/sign-in');
            return 1;
        }
        return 0;
    }

    public function showPortfolio(Request $request, Response $response): Response
    {
        if ($this->checkLogin($response) == 1){
            return $response;
        }
        $data = $request->getQueryParams();
        if (!empty($data["name"])){
            $this->userDb->updatePortfolio($_SESSION["user_id"], $data["name"]);
        }

        $user = $this->userDb->getUserById($_SESSION["user_id"]);
        
        if (empty($user->portfolio)){
            $noPort = true;
            $portName = "";
        }
        else{
            $noPort = false;
            $portName = $user->portfolio;
        }
        
        
        return $this->twig->render(
            $response,
            'portfolio.twig',
            [
                "portName" => $portName,
                "noPort" => $noPort
            ]
        );
    }
}