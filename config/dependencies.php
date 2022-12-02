<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

use Salle\PixSalle\Controller\BlogController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\LandController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\WalletController;
use Salle\PixSalle\Controller\PortController;
use Salle\PixSalle\Repository\MySQLUserRepository;
use Salle\PixSalle\Repository\PDOConnectionBuilder;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Repository\PicRepo;
use Salle\PixSalle\Repository\BlogRepository;
use Slim\Views\Twig;

function addDependencies(ContainerInterface $container): void
{
    $container->set(
        'view',
        function () {
            return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
        }
    );

    $container->set('upload_directory', __DIR__ . '/uploads');

    $container->set('db', function () {
        $connectionBuilder = new PDOConnectionBuilder();
        return $connectionBuilder->build(
            $_ENV['MYSQL_ROOT_USER'],
            $_ENV['MYSQL_ROOT_PASSWORD'],
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_PORT'],
            $_ENV['MYSQL_DATABASE']
        );
    });

    $container->set('user_repository', function (ContainerInterface $container) {
        return new MySQLUserRepository($container->get('db'));
    });

    $container->set('pic_repository', function (ContainerInterface $container) {
        return new PicRepo($container->get('db'));
    });

    $container->set('blog_repository', function (ContainerInterface $container) {
        return new BlogRepository($container->get('db'));
    });

    $container->set(
        UserSessionController::class,
        function (ContainerInterface $c) {
            return new UserSessionController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        SignUpController::class,
        function (ContainerInterface $c) {
            return new SignUpController($c->get('view'), $c->get('user_repository'));
        }
    );

    $container->set(
        LandController::class,
        function (ContainerInterface $c) {
            $controller = new LandController($c->get('view'));
            return $controller;
        }
    );

    $container->set(
        ProfileController::class,
        function (ContainerInterface $c) {
            $controller = new ProfileController($c->get('view'), $c->get('user_repository'));
            return $controller;
        }
    );

    $container->set(
        ExploreController::class,
        function (ContainerInterface $c) {
            $controller = new ExploreController($c->get('view'), $c->get('user_repository'), $c->get("pic_repository"));
            return $controller;
        }
    );

    $container->set(
        MembershipController::class,
        function (ContainerInterface $c) {
            $controller = new MembershipController($c->get('view'), $c->get('user_repository'));
            return $controller;
        }
    );

    $container->set(
        WalletController::class,
        function (ContainerInterface $c) {
            $controller = new WalletController($c->get('view'), $c->get('user_repository'));
            return $controller;
        }
    );

    $container->set(
        PortController::class,
        function (ContainerInterface $c) {
            $controller = new PortController($c->get('view'), $c->get('user_repository'));
            return $controller;
        }
    );

    $container->set(
        BlogController::class,
        function (ContainerInterface $c) {
            $controller = new BlogController($c->get('view'), $c->get('user_repository'), $c->get('blog_repository'));
            return $controller;
        }
    );
}
