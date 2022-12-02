<?php

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salle\PixSalle\Repository\UserRepository;
use Slim\Views\Twig;


class WalletController
{
    const KEY_MONEY = "amount";
    private Twig $twig;
    private UserRepository $userDb;


    public function __construct(Twig $twig, UserRepository $userRepository) {
        $this->twig = $twig;
        $this->userDb = $userRepository;
    }

    public function getFromWallet(Request $request, Response $response): Response
    {
        if ($this->checkLogin($response) == 1){
            return $response;
        }
        $user = $this->userDb->getUserById($_SESSION["user_id"]);
        $current_balance = $this->toAssociativeArray($user->balance);
        return $this->twig->render($response, 'wallet.twig', ["money_amount" => $current_balance]);
    }

    public function addToWallet(Request $request, Response $response): Response
    {
        try {
            $user = $this->userDb->getUserById($_SESSION["user_id"]);
            $data = $request->getParsedBody();
            $money = $data['money'];

            if($money <= 0){
                $current_balance = $this->toAssociativeArray($user->balance);
                return $this->twig->render($response, 'wallet.twig', ["money_amount" => $current_balance]);            }
            else{
                $money  = (string)($money  + $user->balance);//$current_amount
                $this->userDb->updateUserBalance($user->id, $money);
                $money_added = $this->toAssociativeArray($money);
                $this->twig->render($response, 'wallet.twig', ["money_amount" => $money_added]);
            }

        } catch (Exception $exception) {
            // You could render a .twig template here to show the error
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }

        return $response->withStatus(201);
    }

    private function toAssociativeArray(string $money_in) {
        return [
            self::KEY_MONEY => $money_in
        ];
    }

    private function checkLogin(Response &$response): int{
        if (empty($_SESSION["user_id"])){
            //echo "<script>alert('You must be logged in!');</script>";
            $response = $response->withStatus(302)->withHeader('Location', '/sign-in');
            return 1;
        }
        return 0;
    }

}