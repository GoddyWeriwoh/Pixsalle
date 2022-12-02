<?php


namespace Salle\PixSalle\Controller;

use Composer\Autoload\ClassLoader;
use http\Message\Body;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface;
use Salle\PixSalle\Model\Blog;
use Salle\PixSalle\Repository\BlogRepository;
use Salle\PixSalle\Repository\PicRepo;
use Salle\PixSalle\Repository\UserRepository;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class BlogController
{
    const KEY_LOGIN = "login";
    const KEY_TITLE = "title";
    const KEY_CONTENT = "content";
    const KEY_AUTHOR = "author";
    private Twig $twig;
    private UserRepository $userRepository;
    private BlogRepository $blogDb;


    public function __construct(Twig $twig, UserRepository $userRepository, BlogRepository $blogDb)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->blogDb = $blogDb;
    }

    public function blogLanding(Request $request, Response $response): Response
    {

        $listOfBlogs = $this->blogDb->getAllBlogs();
       return $this->twig->render($response, 'blog.twig', ["blogs" => $listOfBlogs]);
    }

    private function checkLogin(Response &$response): int{
        if (empty($_SESSION["user_id"])){
            //echo "<script>alert('You must be logged in!');</script>";
            $response = $response->withStatus(302)->withHeader('Location', '/sign-in');
            return 1;
        }
        return 0;
    }

    public function createBlog(Request $request, Response $response){
        $error_messages = "";
        $data = $request->getParsedBody();
        $userExists = $this -> blogDb -> checkUserId($data['userId']);
        $response = $response->withHeader('Content-Type', 'application/json');

        if($data['title']==null || $data['content']=="" || $data['userId']==null){
                $status = 400;
                $response = $response->withStatus($status, "'title' and/or 'content' and/or 'userId' key missing");
                $arr = array('message' => "'title' and/or 'content' and/or 'userId' key missing");
                $response_json = json_encode($arr);
                $response->getBody()->write($response_json);
            return $response;
            }
            else{
                if($userExists){
                    $blog = new Blog($data['title'], $data['content'], $data['userId'], $data['author']);
                    $createdDate = $this->blogDb->createBlog($blog);
                    $blog_info = $this->blogDb->getBlogInfo($createdDate);
                    $currBlogJson = $this->generateBlogJson($blog_info);
                    $response->getBody()->write($currBlogJson);
                    return $response;
                }else{
                    $errors = [];
                    $errors['login_error'] = "The user Id provided does not exist";
                    $error_messages = $this->toAssociativeArray($errors);
                }
            }
        return $this->twig->render($response, 'blog.twig', ["errors" => $error_messages]);
    }

    public function showBlogs(Request $request, Response $response){
        //[{ <br> &emsp;id: int <br> &emsp;title: string <br> &emsp;content: string <br>&emsp;userId: int <br> }]
        $listOfBlogs = $this->blogDb->getAllBlogs();
        $blogArray = [];
        for($i = 0; $i < sizeof($listOfBlogs); $i++){
            $currBlogJson = $this->generateBlogJson($listOfBlogs[$i]);
            $blogArray[$i] = $currBlogJson;
        }
        $response->getBody()->write($blogArray);
        return $response;
        //return $this->twig->render($response, 'blog.twig');
    }

    private function toAssociativeArray(array $errors)
    {
        return [
            self::KEY_LOGIN => $errors['login_error'],
        ];
    }

    private function generateBlogJson($i)
    {
        $blogArr = array('id' => $i['id'], 'title' => $i['title'],'content' => $i['content'],'userId' => $i['userId']);
        return json_encode($blogArr);
    }

    public function updateBlog(Request $request, Response $response)
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        $data = $request->getParsedBody();
        $routeContext = RouteContext::fromRequest($request);
        $id = $routeContext->getRoute()->getArgument("id");
        $requestedBlog = $this->blogDb->getRequestedBlog($id);
        $my_bool = true;

        if($requestedBlog == null){
            $status = 404;
            $response = $response->withStatus($status, "Blog entry with id {id} does not exist");
            $arr = array('message' => "Blog entry with id ${id} does not exist");
            $response_json = json_encode($arr);
            $response->getBody()->write($response_json);
            $my_bool = false;
        }
        if($data['title']==null || $data['content']=="" || $data['userId']==null){
            $status = 400;
            $response = $response->withStatus($status, "'title' and/or 'content' and/or 'userId' key missing");
            $arr = array('message' => "'title' and/or 'content' and/or 'userId' key missing");
            $response_json = json_encode($arr);
            $response->getBody()->write($response_json);
            $my_bool = false;
        }

        if($my_bool){
            $currBlogJson = $this->generateBlogJson($requestedBlog);
            $response->getBody()->write($currBlogJson);
            $this->blogDb->updateBlog($id, $data);
            return $response;
        }
        return $response;
    }

    public function getRequestedBlog(Request $request, Response $response){
        $routeContext = RouteContext::fromRequest($request);
        $id = $routeContext->getRoute()->getArgument("id");
        $requestedBlog = $this->blogDb->getRequestedBlog($id);
        $response = $response->withHeader('Content-Type', 'application/json');

        if($requestedBlog == null){
            $status = 404;
            $response = $response->withStatus($status, "Blog entry with id ${id} does not exist");
            $arr = array('message' => "Blog entry with id ${id} does not exist");
            $response_json = json_encode($arr);
            $response->getBody()->write($response_json);
            return $response;
        }

        $currBlogJson = $this->generateBlogJson($requestedBlog);
        $response->getBody()->write($currBlogJson);
        return $response;
    }

    public function deleteBlog(Request $request, Response $response){
        $routeContext = RouteContext::fromRequest($request);
        $id = $routeContext->getRoute()->getArgument("id");
        $requestedBlog = $this->blogDb->getRequestedBlog($id);
        $response = $response->withHeader('Content-Type', 'application/json');


        if($requestedBlog == null){
            $status = 404;
            $response = $response->withStatus($status, "Blog entry with id ${id} does not exist");
            $arr = array('message' => "Blog entry with id ${id} does not exist");
            $response_json = json_encode($arr);
           $response->getBody()->write($response_json);
           return $response;
        }
        $this->blogDb->deleteBlog($id);
        $arr = array('message' => "Blog entry with id ${id} was successfully deleted");
        $response->getBody()->write(json_encode($arr));
        return $response;
    }

    public function showRequestedBlog(Request $request, Response $response){
        $routeContext = RouteContext::fromRequest($request);
        $id = $routeContext->getRoute()->getArgument("id");
        $requestedBlog = $this->blogDb->getRequestedBlog($id);
        $info =  $this->toAssociativeArrayBlogs($requestedBlog);
        return $this->twig->render($response, 'blog-individual.twig', ["blog" => $info]);
    }

    private function toAssociativeArrayBlogs($requestedBlog)
    {
        return [
            self::KEY_TITLE => $requestedBlog['title'],
            self::KEY_CONTENT => $requestedBlog['content'],
            self::KEY_AUTHOR => $requestedBlog['author'],
        ];
    }
}