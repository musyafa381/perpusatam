<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

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
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/book', 'Home::book');
$routes->get('/book/(:segment)', 'Home::bookDetail/$1');
$routes->get('/buku-tamu', 'VisitorController::index');
$routes->get('/buku-tamu/search-member', 'VisitorController::searchMember');
$routes->post('/buku-tamu/checkin', 'VisitorController::checkin');
$routes->get('/api/live-data', 'Home::apiLiveData');

service('auth')->routes($routes);
$routes->get('login', 'Auth\LoginController::loginView');
$routes->post('login', 'Auth\LoginController::loginAction');

$routes->group('admin', ['filter' => 'session'], static function (RouteCollection $routes) {
    $routes->get('/', 'Dashboard\DashboardController');
    $routes->get('dashboard', 'Dashboard\DashboardController::dashboard');

    $routes->get('tv-content', 'Admin\TvContentController::index');
    $routes->get('tv-content/store', 'Admin\TvContentController::index');
    $routes->post('tv-content/store', 'Admin\TvContentController::store');
    $routes->get('tv-content/move/(:any)/(:any)', 'Admin\TvContentController::move/$1/$2');
    $routes->get('tv-content/delete/(:any)', 'Admin\TvContentController::delete/$1');

    $routes->get('members/cards', 'Members\MembersController::cards');
    $routes->post('members/cards/assign', 'Members\MembersController::assignManualTier');
    $routes->get('members/cards/(:num)', 'Members\MembersController::showCard/$1');
    $routes->post('members/cards/(:num)/status', 'Members\MembersController::updateCardStatus/$1');
    $routes->get('members/id-card/(:any)', 'Members\MembersController::idCard/$1');
    $routes->resource('members', ['controller' => 'Members\MembersController']);

    $routes->get('visitors', 'VisitorController::adminIndex');
    $routes->post('visitors/sessions/open', 'VisitorController::openSession');
    $routes->post('visitors/sessions/close/(:num)', 'VisitorController::closeSession/$1');

    $routes->get('reservations', 'Reservations\ReservationsController::index');
    $routes->post('reservations', 'Reservations\ReservationsController::store');
    $routes->post('reservations/(:num)/cancel', 'Reservations\ReservationsController::cancel/$1');
    $routes->post('reservations/(:num)/fulfill', 'Reservations\ReservationsController::fulfill/$1');
    $routes->post('reservations/(:num)/delete', 'Reservations\ReservationsController::delete/$1');
    $routes->post('books/items/(:num)/condition', 'Books\BooksController::updateItemCondition/$1');
    $routes->post('books/(:num)/copies', 'Books\BooksController::addCopy/$1');
    $routes->post('books/copies/(:num)/update', 'Books\BooksController::updateCopy/$1');
    $routes->post('books/copies/(:num)/delete', 'Books\BooksController::deleteCopy/$1');
    $routes->get('books/lookup-isbn', 'Books\BooksController::lookupIsbn');
    $routes->get('books/lookup-ai', 'Books\BooksController::lookupAi');
    $routes->get('books/update/(:segment)', 'Books\BooksController::edit/$1');
    $routes->post('books/update/(:segment)', 'Books\BooksController::update/$1');
    $routes->resource('books', ['controller' => 'Books\BooksController']);
    $routes->resource('authors', ['controller' => 'Books\AuthorsController']);
    $routes->resource('publishers', ['controller' => 'Books\PublishersController']);
    $routes->resource('categories', ['controller' => 'Books\CategoriesController']);
    $routes->resource('racks', ['controller' => 'Books\RacksController']);

    // Unified Book Attributes Routes
    $routes->get('book-attributes', 'Books\BookAttributesController::index');
    $routes->post('book-attributes/authors/store', 'Books\BookAttributesController::storeAuthor');
    $routes->post('book-attributes/authors/update/(:num)', 'Books\BookAttributesController::updateAuthor/$1');
    $routes->get('book-attributes/authors/delete/(:num)', 'Books\BookAttributesController::deleteAuthor/$1');

    $routes->post('book-attributes/publishers/store', 'Books\BookAttributesController::storePublisher');
    $routes->post('book-attributes/publishers/update/(:num)', 'Books\BookAttributesController::updatePublisher/$1');
    $routes->get('book-attributes/publishers/delete/(:num)', 'Books\BookAttributesController::deletePublisher/$1');

    $routes->post('book-attributes/categories/store', 'Books\BookAttributesController::storeCategory');
    $routes->post('book-attributes/categories/update/(:num)', 'Books\BookAttributesController::updateCategory/$1');
    $routes->get('book-attributes/categories/delete/(:num)', 'Books\BookAttributesController::deleteCategory/$1');

    $routes->post('book-attributes/racks/store', 'Books\BookAttributesController::storeRack');
    $routes->post('book-attributes/racks/update/(:num)', 'Books\BookAttributesController::updateRack/$1');
    $routes->get('book-attributes/racks/delete/(:num)', 'Books\BookAttributesController::deleteRack/$1');

    $routes->get('settings', 'Admin\SettingsController::index');
    $routes->post('settings', 'Admin\SettingsController::update');

    $routes->get('loans/new/members/search', 'Loans\LoansController::searchMember');
    $routes->get('loans/new/books/search', 'Loans\LoansController::searchBook');
    $routes->post('loans/new', 'Loans\LoansController::new');
    $routes->post('loans/(:segment)/add-item', 'Loans\LoansController::addItem/$1');
    $routes->post('loans/(:segment)/remove-item/(:num)', 'Loans\LoansController::removeItem/$1/$2');
    $routes->get('loans/seed-late', 'Loans\LoansController::seedLate');
    $routes->get('loans/receipt/(:segment)', 'Loans\LoansController::receipt/$1');
    $routes->resource('loans', ['controller' => 'Loans\LoansController']);

    $routes->get('returns/new/search', 'Loans\ReturnsController::searchLoan');
    $routes->get('returns/responsibility-letter/(:segment)', 'Loans\ReturnsController::responsibilityLetter/$1');
    $routes->resource('returns', ['controller' => 'Loans\ReturnsController']);

    $routes->get('fines/returns/search', 'Loans\FinesController::searchReturn');
    $routes->get('fines/pay/(:any)', 'Loans\FinesController::pay/$1');
    $routes->resource('fines/settings', ['controller' => 'Loans\FineSettingsController', 'filter' => 'group:superadmin']);
    $routes->resource('fines', ['controller' => 'Loans\FinesController']);

    $routes->group('users', ['filter' => 'group:superadmin'], static function (RouteCollection $routes) {
        $routes->get('new', 'Users\RegisterController::index');
        $routes->post('', 'Users\RegisterController::registerAction');
    });
    $routes->resource('users', ['controller' => 'Users\UsersController', 'filter' => 'group:superadmin']);
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
