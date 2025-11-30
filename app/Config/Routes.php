<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;
use Config\Services;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// Default homepage routes
$routes->get('/', 'Home::index');
$routes->get('/book', 'Home::book');

// Authentication routes
service('auth')->routes($routes);

/*
 * --------------------------------------------------------------------
 * ADMIN ROUTES (HANYA AKSES ADMIN)
 * --------------------------------------------------------------------
 */

$routes->group('admin', ['filter' => 'session'], static function (RouteCollection $routes) {

    // Dashboard
    $routes->get('/', 'Dashboard\DashboardController');
    $routes->get('dashboard', 'Dashboard\DashboardController::dashboard');

    // ⭐ CUSTOM ROUTE HARUS DI ATAS RESOURCE!
    // Route khusus untuk download kartu anggota PDF
    $routes->get('members/card/(:segment)', 'Members\MembersController::memberCard/$1');

    // Members (Resource Route)
    $routes->resource('members', ['controller' => 'Members\MembersController']);

    // Books
    $routes->resource('books', ['controller' => 'Books\BooksController']);
    $routes->resource('categories', ['controller' => 'Books\CategoriesController']);
    $routes->resource('racks', ['controller' => 'Books\RacksController']);

    // Loans
    $routes->get('loans/new/members/search', 'Loans\LoansController::searchMember');
    $routes->get('loans/new/books/search', 'Loans\LoansController::searchBook');
    $routes->post('loans/new', 'Loans\LoansController::new');
    $routes->resource('loans', ['controller' => 'Loans\LoansController']);

    // Returns
    $routes->get('returns/new/search', 'Loans\ReturnsController::searchLoan');
    $routes->resource('returns', ['controller' => 'Loans\ReturnsController']);

    // Fines
    $routes->get('fines/returns/search', 'Loans\FinesController::searchReturn');
    $routes->get('fines/pay/(:any)', 'Loans\FinesController::pay/$1');
    $routes->resource('fines/settings', [
        'controller' => 'Loans\FineSettingsController',
        'filter' => 'group:superadmin'
    ]);
    $routes->resource('fines', ['controller' => 'Loans\FinesController']);

    // Users
    $routes->group('users', ['filter' => 'group:superadmin'], static function (RouteCollection $routes) {
        $routes->get('new', 'Users\RegisterController::index');
        $routes->post('', 'Users\RegisterController::registerAction');
    });

    $routes->resource('users', [
        'controller' => 'Users\UsersController',
        'filter' => 'group:superadmin'
    ]);
});

/*
 * --------------------------------------------------------------------
 * Additional Routing (Environment Based)
 * --------------------------------------------------------------------
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
