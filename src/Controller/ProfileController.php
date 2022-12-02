<?php

declare(strict_types=1);

namespace Salle\PixSalle\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;
use Salle\PixSalle\Repository\UserRepository;
use Slim\Views\Twig;
use Ramsey\Uuid\Uuid;
use Salle\PixSalle\Service\ValidateUpdate;
use Salle\PixSalle\Service\ValidatorService;

final class ProfileController{
    private Twig $twig;
    private UserRepository $userDb;
    private ValidateUpdate $validator;
    private ValidatorService $valUsr;
    
    public function __construct(Twig $twig, UserRepository $userRepository) {
        $this->twig = $twig;
        $this->userDb = $userRepository;
        $this->validator = new ValidateUpdate();
        $this->valUsr = new ValidatorService();
    }

    private function checkLogin(Response &$response): int{
        if (empty($_SESSION["user_id"])){
            //echo "<script>alert('You must be logged in!');</script>";
            $response = $response->withStatus(302)->withHeader('Location', '/sign-in');
            return 1;
        }
        return 0;
    }

    public function showProfile(Request $request, Response $response): Response
    {
        if ($this->checkLogin($response) == 1){
            return $response;
        }

        $user = $this->userDb->getUserById($_SESSION["user_id"]);
        if (!($user->photo === null || $user->photo === "")){
            $toggPhoto = true;
        }
        else {
            $toggPhoto = false;
        }
        
        return $this->twig->render(
            $response,
            'profile.twig',
            [
                "username" => $user->username,
                "email" => $user->email,
                "phone" => $user->phone,
                "photo" => '/uploads/' . $user->photo,
                "toggPhoto" => $toggPhoto
            ]
        );
    }

    public function logProfile(Request $request, Response $response): Response{
        if ($this->checkLogin($response) == 1){
            return $response;
        }
        $path = __DIR__ . '/../../public/uploads';
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        $errImg = $this->validator->valImg($_FILES);
        $errPhone = $this->validator->valPhone($data["phone"]);

        if (strcmp($errImg, "") === 0 && strcmp($errPhone, "") === 0){
            //echo $path;
            $filename = "";
            $file = $files["photo"];
            $user = $this->userDb->getUserById($_SESSION["user_id"]);
            if (!($user->photo === null || $user->photo === "")){
                unlink($path . '/' . $user->photo);
            }
            if ($file->getError() === UPLOAD_ERR_OK){
                $filename = $this->moveUploadedFile($path, $file);
            }

            $this->userDb->updateUserById($_SESSION["user_id"], $data["phone"],$data["usr"], $filename);
        }

        $user = $this->userDb->getUserById($_SESSION["user_id"]);
        if (!($user->photo === null || $user->photo === "")){
            $toggPhoto = true;
        }
        else {
            $toggPhoto = false;
        }
        
        

        return $this->twig->render(
            $response,
            'profile.twig',
            [
                "username" => $user->username,
                "email" => $user->email,
                "phone" => $user->phone,
                "photo" => '/uploads/' . $user->photo,
                "errImg" => $errImg,
                "errPhone" => $errPhone,
                "toggPhoto" => $toggPhoto
            ]
        );
    }

    private function moveUploadedFile(string $directory, UploadedFileInterface $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        // see http://php.net/manual/en/function.random-bytes.php
        $uuid = Uuid::uuid4();
        $filename = sprintf('%s.%0.8s', $uuid, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public function getChangePass(Request $request, Response $response): Response
    {
        if ($this->checkLogin($response) == 1){
            return $response;
        }

        return $this->twig->render(
            $response,
            'changePass.twig',
            [
                "errPass" => ""
            ]
        );
    }

    public function postChangePass(Request $request, Response $response): Response
    {
        if ($this->checkLogin($response) == 1){
            return $response;
        }

        $data = $request->getParsedBody();
        $user = $this->userDb->getUserById($_SESSION["user_id"]);
        $passErr = "";
        $succ = "";

        if (md5($data["oldPass"]) != $user->password){
            $passErr = "The old password is not correct";
        }
        else if (strcmp($data["newPass"], $data["conPass"]) !== 0){
            $passErr = "The new passwords don't match";
        }
        else {
            $passErr = $this->valUsr->validatePassword($data["newPass"]);
        }

        if (strcmp($passErr, "") === 0){
            $this->userDb->updateUserPass($_SESSION["user_id"], $data["newPass"]);
            $succ = "Password changed correctly";
        }

        return $this->twig->render(
            $response,
            'changePass.twig',
            [
                "errPass" => $passErr,
                "succ" => $succ
            ]
        );
    }
}