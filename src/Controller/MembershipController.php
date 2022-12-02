<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Salle\PixSalle\Repository\UserRepository;
use Slim\Views\Twig;

final class MembershipController{
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

    public function getMembership(Request $request, Response $response): Response
    {

        if ($this->checkLogin($response) == 1){
            return $response;
        }

        $user = $this->userDb->getUserById($_SESSION["user_id"]);
        $toggMem = (strcmp($user->membership, "active") === 0);

        return $this->twig->render(
            $response,
            'membership.twig',
            [
                "toggMem" => $toggMem,
                "currMem" => $user->membership
            ]
        );
    }

    public function postMembership(Request $request, Response $response): Response
    {
        if ($this->checkLogin($response) == 1){
            return $response;
        }
        $data = $request->getParsedBody();

        if(strcmp($data["newMem"], "Change to Cool Membership") === 0){
            $this->userDb->updateUserMembership($_SESSION["user_id"], "cool");
        }
        else{
            $this->userDb->updateUserMembership($_SESSION["user_id"], "active");
        }

        $user = $this->userDb->getUserById($_SESSION["user_id"]);
        $toggMem = (strcmp($user->membership, "active") === 0);

        return $this->twig->render(
            $response,
            'membership.twig',
            [
                "toggMem" => $toggMem,
                "currMem" => $user->membership
            ]
        );
    }
}