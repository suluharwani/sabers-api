<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function (RouteCollection $routes) {
    // Auth
    $routes->post('login', 'Api\AuthController::login');
    $routes->post('register', 'Api\AuthController::register');

    // Protected Routes (Require Token)
    $routes->group('', ['filter' => 'token'], function ($routes) {
        // Resource Routes
        $routes->resource('blogs', ['controller' => 'Api\BlogController']);
        $routes->resource('blog-categories', ['controller' => 'Api\BlogCategories']);
        $routes->resource('blog-tags', ['controller' => 'Api\BlogTags']);
        $routes->resource('clients', ['controller' => 'Api\Clients']);
        $routes->resource('contacts', ['controller' => 'Api\Contacts']);
        $routes->resource('projects', ['controller' => 'Api\Projects']);
        $routes->resource('sliders', ['controller' => 'Api\Sliders']);
        $routes->resource('sponsors', ['controller' => 'Api\Sponsors']);
    });

    // Route untuk menampilkan gambar yang diupload
    $routes->get('uploads/(:segment)', function ($filename) {
        $path = WRITEPATH . 'uploads/' . $filename;
        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $file = file_get_contents($path);
        $mimeType = mime_content_type($path);

        return $this->response
            ->setStatusCode(200)
            ->setContentType($mimeType)
            ->setBody($file);
    });
});