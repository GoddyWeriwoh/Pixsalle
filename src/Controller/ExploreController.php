<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Salle\PixSalle\Repository\UserRepository;
use Salle\PixSalle\Repository\PicRepo;
use Slim\Views\Twig;
use stdClass;

final class ExploreController{
    private Twig $twig;
    private UserRepository $userDb;
    private PicRepo $picDb;
    
    public function __construct(Twig $twig, UserRepository $userRepository, PicRepo $pictureRepository) {
        $this->twig = $twig;
        $this->userDb = $userRepository;
        $this->picDb = $pictureRepository;
    }

    private function checkLogin(Response &$response): int{
        if (empty($_SESSION["user_id"])){
            //echo "<script>alert('You must be logged in!');</script>";
            $response = $response->withStatus(302)->withHeader('Location', '/sign-in');
            return 1;
        }
        return 0;
    }

    public function getExplore(Request $request, Response $response): Response
    {
        if ($this->checkLogin($response) == 1){
            return $response;
        }

        $rawPics = $this->picDb->getAllPics();
        $pics = [];
        if ($rawPics !== null){
            for($i = 0; $i < sizeof($rawPics); $i++){
                $curr = $rawPics[$i];
                $tmp = $this->userDb->getUserById($curr->usrId);
                $name = $tmp->username;
                $pics[] = $this->genPhoto($name, $curr->url);
            }
        }
        

        return $this->twig->render(
            $response,
            'explore.twig',
            [
                "pics" => $pics
            ]
        );
    }

    private function genPhoto($name, $url){
        $tmp = new stdClass();
        $tmp->url = $url;
        $tmp->usr = $name;
        return $tmp;
    }

    public function postExplore(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $this->picDb->createPic($_SESSION["user_id"], $data["url"]);

        return $this->getExplore($request, $response);
    }
}