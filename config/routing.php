<?php

declare(strict_types=1);

use Salle\PixSalle\Controller\API\BlogAPIController;
use Salle\PixSalle\Controller\BlogController;
use Salle\PixSalle\Controller\SignUpController;
use Salle\PixSalle\Controller\UserSessionController;
use Salle\PixSalle\Controller\LandController;
use Salle\PixSalle\Controller\ProfileController;
use Salle\PixSalle\Controller\ExploreController;
use Salle\PixSalle\Controller\MembershipController;
use Salle\PixSalle\Controller\PortController;

use Salle\PixSalle\Controller\WalletController;
use Slim\App;

function addRoutes(App $app): void
{
    $app->get('/', LandController::class . ':showLanding')->setName('landing');
    $app->get('/sign-in', UserSessionController::class . ':showSignInForm')->setName('signIn');
    $app->post('/sign-in', UserSessionController::class . ':signIn');
    $app->get('/sign-up', SignUpController::class . ':showSignUpForm')->setName('signUp');
    $app->post('/sign-up', SignUpController::class . ':signUp');
    $app->get('/profile', ProfileController::class . ':showProfile')->setName('profile');
    $app->post('/profile', ProfileController::class . ':logProfile');
    $app->get('/profile/changePassword', ProfileController::class . ':getChangePass')->setName('changePassword');
    $app->post('/profile/changePassword', ProfileController::class . ':postChangePass');
    $app->get('/explore', ExploreController::class . ':getExplore')->setName('explore');
    $app->post('/explore', ExploreController::class . ':postExplore');
    $app->get('/user/membership', MembershipController::class . ':getMembership')->setName('getMembership');
    $app->post('/user/membership', MembershipController::class . ':postMembership');
    $app->get('/user/wallet', WalletController::class . ':getFromWallet')->setName('get_from_wallet');
    $app->post('/user/wallet', WalletController::class . ':addToWallet')->setName('add_To_Wallet');
    $app->get('/portfolio', PortController::class . ':showPortfolio')->setName('portfolio');
    $app->get('/blog', BlogController::class . ':blogLanding')->setName('blog_landing');
    $app->get('/blog/{id}', BlogController::class . ':showRequestedBlog');
    $app->post('/api/blog', BlogController::class . ':createBlog');
    $app->get('/api/blog', BlogController::class . ':showBlogs');
    $app->put('/api/blog/{id}', BlogController::class . ':updateBlog');
    $app->get('/api/blog/{id}', BlogController::class . ':getRequestedBlog');
    $app->delete('/api/blog/{id}', BlogController::class . ':deleteBlog');
}