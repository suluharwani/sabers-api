<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

// Documentation Route
$routes->get('/', 'DocsController::index');
$routes->options('/', 'Api\AuthController::options');

// Public Auth Routes
$routes->options('api/login', 'Api\AuthController::options');
$routes->options('api/register', 'Api\AuthController::options');
$routes->options('api/clients', 'Api\AuthController::options');
$routes->options('api/clients/(:num)', 'Api\AuthController::options');
$routes->post('api/login', 'Api\AuthController::login');
$routes->post('api/register', 'Api\AuthController::register');

// Public GET Routes (tanpa filter auth) dengan OPTIONS
$routes->options('api/users', 'Api\AuthController::options');
$routes->options('api/users/(:num)', 'Api\AuthController::options');
$routes->options('api/sliders', 'Api\AuthController::options');
$routes->options('api/sliders/(:num)', 'Api\AuthController::options');
$routes->options('api/sponsors', 'Api\AuthController::options');
$routes->options('api/sponsors/(:num)', 'Api\AuthController::options');
$routes->options('api/contacts', 'Api\AuthController::options');
$routes->options('api/contacts/(:num)', 'Api\AuthController::options');
$routes->options('api/clients', 'Api\AuthController::options');
$routes->options('api/clients/(:num)', 'Api\AuthController::options');
$routes->options('api/projects', 'Api\AuthController::options');
$routes->options('api/projects/(:num)', 'Api\AuthController::options');
$routes->options('api/blogs', 'Api\AuthController::options');
$routes->options('api/blogs/(:num)', 'Api\AuthController::options');

$routes->get('api/users', 'Api\UserController::index');
$routes->get('api/users/(:num)', 'Api\UserController::show/$1');
$routes->get('api/sliders', 'Api\SliderController::index');
$routes->get('api/sliders/(:num)', 'Api\SliderController::show/$1');
$routes->get('api/sponsors', 'Api\SponsorController::index');
$routes->get('api/sponsors/(:num)', 'Api\SponsorController::show/$1');
$routes->get('api/contacts', 'Api\ContactController::index');
$routes->get('api/contacts/(:num)', 'Api\ContactController::show/$1');
$routes->get('api/clients', 'Api\ClientController::index');
$routes->get('api/clients/(:num)', 'Api\ClientController::show/$1');
$routes->get('api/projects', 'Api\ProjectController::index');
$routes->get('api/projects/(:num)', 'Api\ProjectController::show/$1', ['filter' => 'cors']);
$routes->get('api/blogs', 'Api\BlogController::index');
$routes->get('api/blogs/(:num)', 'Api\BlogController::show/$1');

// Protected API Routes (dengan filter auth) dengan OPTIONS
$routes->options('api/users', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/users/(:num)', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/sliders', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/sliders/(:num)', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/sponsors', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/sponsors/(:num)', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/contacts', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/contacts/(:num)', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/clients', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/clients/(:num)', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/projects', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/projects/(:num)', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/blogs', 'Api\AuthController::options', ['filter' => 'auth']);
$routes->options('api/blogs/(:num)', 'Api\AuthController::options', ['filter' => 'auth']);

$routes->post('api/users', 'Api\UserController::create', ['filter' => 'auth']);
$routes->put('api/users/(:num)', 'Api\UserController::update/$1', ['filter' => 'auth']);
$routes->delete('api/users/(:num)', 'Api\UserController::delete/$1', ['filter' => 'auth']);

$routes->post('api/sliders', 'Api\SliderController::create', ['filter' => 'auth']);
$routes->put('api/sliders/(:num)', 'Api\SliderController::update/$1', ['filter' => 'auth']);
$routes->delete('api/sliders/(:num)', 'Api\SliderController::delete/$1', ['filter' => 'auth']);

$routes->post('api/sponsors', 'Api\SponsorController::create', ['filter' => 'auth']);
$routes->put('api/sponsors/(:num)', 'Api\SponsorController::update/$1', ['filter' => 'auth']);
$routes->delete('api/sponsors/(:num)', 'Api\SponsorController::delete/$1', ['filter' => 'auth']);

$routes->post('api/contacts', 'Api\ContactController::create', ['filter' => 'auth']);
$routes->put('api/contacts/(:num)', 'Api\ContactController::update/$1', ['filter' => 'auth']);
$routes->delete('api/contacts/(:num)', 'Api\ContactController::delete/$1', ['filter' => 'auth']);

$routes->post('api/clients', 'Api\ClientController::create', ['filter' => 'auth']);
$routes->put('api/clients/(:num)', 'Api\ClientController::update/$1', ['filter' => 'auth']);
$routes->delete('api/clients/(:num)', 'Api\ClientController::delete/$1', ['filter' => 'auth']);

$routes->post('api/projects', 'Api\ProjectController::create', ['filter' => 'auth']);
$routes->put('api/projects/(:num)', 'Api\ProjectController::update/$1', ['filter' => 'auth']);
$routes->delete('api/projects/(:num)', 'Api\ProjectController::delete/$1', ['filter' => 'auth']);

$routes->post('api/blogs', 'Api\BlogController::create', ['filter' => 'auth']);
$routes->put('api/blogs/(:num)', 'Api\BlogController::update/$1', ['filter' => 'auth']);
$routes->delete('api/blogs/(:num)', 'Api\BlogController::delete/$1', ['filter' => 'auth']);

// Certification API Routes dengan OPTIONS
$routes->options('api/certifications', 'Api\AuthController::options');
$routes->options('api/certifications/(:num)', 'Api\AuthController::options');
$routes->options('api/certifications/active', 'Api\AuthController::options');
$routes->options('api/certifications/expired', 'Api\AuthController::options');
$routes->options('api/certifications/type/(:any)', 'Api\AuthController::options');

$routes->get('api/certifications', 'Api\CertificationController::index');
$routes->get('api/certifications/(:num)', 'Api\CertificationController::show/$1');
$routes->post('api/certifications', 'Api\CertificationController::create', ['filter' => 'auth']);
$routes->put('api/certifications/(:num)', 'Api\CertificationController::update/$1', ['filter' => 'auth']);
$routes->delete('api/certifications/(:num)', 'Api\CertificationController::delete/$1', ['filter' => 'auth']);

// Additional custom routes with auth filter
$routes->get('api/certifications/active', 'Api\CertificationController::activeCertifications');
$routes->get('api/certifications/expired', 'Api\CertificationController::expiredCertifications');
$routes->get('api/certifications/type/(:any)', 'Api\CertificationController::byType/$1');