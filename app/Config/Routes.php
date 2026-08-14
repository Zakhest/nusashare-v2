<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

/*
|--------------------------------------------------------------------------
| 기본 설정
|--------------------------------------------------------------------------
*/
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('HomeController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false); // WAJIB: biar aman & disiplin

/*
|--------------------------------------------------------------------------
| PUBLIC AREA (NO AUTH)
|--------------------------------------------------------------------------
*/

// Home / Landing
$routes->get('/', 'Home::index');

// Auth (User)
$routes->get('login', 'App\AuthController::login');
$routes->post('login', 'App\AuthController::attempt');
$routes->get('register', 'App\AuthController::register');
$routes->post('register', 'App\AuthController::store');
$routes->get('forgot-password', 'App\AuthController::forgotPassword');
$routes->post('forgot-password', 'App\AuthController::attemptForgot');
$routes->get('reset-password/(:any)', 'App\AuthController::resetPassword/$1');
$routes->post('reset-password', 'App\AuthController::attemptReset');
$routes->get('logout', 'App\AuthController::logout');

// Explore & Public Content
$routes->get('explore', 'App\ExploreController::index');
$routes->get('explore/gallery', 'App\ExploreController::gallery');
$routes->get('explore/story', 'App\ExploreController::story');
$routes->get('search', 'App\ExploreController::search');
$routes->get('user/(:segment)', 'App\ProfileController::show/$1');
$routes->get('works/(:num)', 'App\WorkController::show/$1');
$routes->get('works/(:num)/download', 'App\WorkController::download/$1');
$routes->get('works/(:num)/read/(:num)', 'App\WorkController::read/$1/$2');
$routes->post('works/(:num)/view', 'App\WorkController::recordView/$1'); // 2-menit threshold view
$routes->get('content/(:segment)', 'App\ContentController::show/$1');
$routes->get('artikel/(:segment)', 'App\ArticleController::show/$1');

// Legal
$routes->get('terms', 'Home::terms');
$routes->get('privacy', 'Home::privacy');

// Admin Panel (Auth: Admin only)
$routes->get('alpha-admin', 'App\AdminController::index', ['filter' => 'auth:admin']);

// Admin — User Management API (AJAX, auth:admin)
$routes->get('alpha-admin/api/users',                   'App\AdminController::apiUsers',            ['filter' => 'auth:admin']);
$routes->get('alpha-admin/api/users/(:segment)',         'App\AdminController::apiUserDetail/$1',    ['filter' => 'auth:admin']);
$routes->post('alpha-admin/api/users/(:segment)/status', 'App\AdminController::apiUpdateUserStatus/$1', ['filter' => 'auth:admin']);
$routes->post('alpha-admin/api/users/(:segment)/adjust-cc', 'App\AdminController::apiAdjustUserCC/$1',  ['filter' => 'auth:admin']);

// Admin — Creator Management API (AJAX, auth:admin)
$routes->get('alpha-admin/api/creators',                          'App\AdminController::apiCreators',                    ['filter' => 'auth:admin']);
$routes->get('alpha-admin/api/creators/(:segment)',               'App\AdminController::apiCreatorDetail/$1',            ['filter' => 'auth:admin']);
$routes->post('alpha-admin/api/creators/(:segment)/status',       'App\AdminController::apiUpdateCreatorStatus/$1',      ['filter' => 'auth:admin']);
$routes->post('alpha-admin/api/creators/(:segment)/starsoul-status', 'App\AdminController::apiUpdateCreatorStarsoulStatus/$1', ['filter' => 'auth:admin']);

// Admin — Transaction History API (AJAX, auth:admin)
$routes->get('alpha-admin/api/transactions',         'App\AdminController::apiTransactions',      ['filter' => 'auth:admin']);
$routes->get('alpha-admin/api/transactions/stats',   'App\AdminController::apiTransactionStats',  ['filter' => 'auth:admin']);




// Top Up CC
$routes->get('topup', 'App\TopupController::index', ['filter' => 'auth:user']);
$routes->post('topup/checkout', 'App\TopupController::checkout', ['filter' => 'auth:user']);

// Image Proxy (Reverse Proxy)
$routes->get('image/proxy-remote', 'App\ImageController::proxyRemote');
$routes->get('image/cover/(:num)', 'App\ImageController::cover/$1');
$routes->get('image/gallery/(:num)', 'App\ImageController::gallery/$1');
$routes->get('image/profile/(:segment)', 'App\ImageController::profile/$1');
$routes->get('image/chapter/(:num)/(:segment)', 'App\ImageController::chapterImage/$1/$2');




/*
|--------------------------------------------------------------------------
| USER AREA (AUTH: USER)
|--------------------------------------------------------------------------
*/
$routes->group('', ['filter' => 'auth:user'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'App\DashboardController::index');

    // Profile
    $routes->get('me/profile', 'App\ProfileController::index');
    $routes->post('me/profile', 'App\ProfileController::update');

    // Bookmark / Wishlist
    $routes->get('me/bookmarks', 'App\BookmarkController::index');
    $routes->post('bookmark/(:segment)', 'App\BookmarkController::store/$1');
    $routes->post('bookmark/(:segment)/remove', 'App\BookmarkController::destroy/$1');

    // Cart
    $routes->get('me/cart', 'App\CartController::index');
    $routes->post('cart/add/(:num)', 'App\CartController::add/$1');
    $routes->post('cart/remove/(:num)', 'App\CartController::remove/$1');
    $routes->post('cart/checkout', 'App\CartController::checkout');
    $routes->get('cart/download-all', 'App\CartController::downloadAll');
    $routes->get('cart/download/(:num)', 'App\CartController::download/$1');

    // Follow Creator
    $routes->get('me/follows', 'App\FollowController::index');
    $routes->post('follow/(:segment)', 'App\FollowController::store/$1');
    $routes->post('follow/(:segment)/remove', 'App\FollowController::destroy/$1');
    $routes->get('follow/(:segment)/stats', 'App\FollowController::stats/$1');

    // Likes & Comments for Works
    $routes->post('works/(:num)/like', 'App\LikeController::toggle/$1');
    $routes->post('works/(:num)/comment', 'App\CommentController::store/$1');
    $routes->post('works/(:num)/unlock', 'App\WorkController::unlock/$1');
    $routes->post('chapters/(:num)/unlock', 'App\WorkController::unlockChapter/$1');
    $routes->post('comments/(:num)/delete', 'App\CommentController::destroy/$1');

    // Notifications
    $routes->get('notifications/fetch', 'App\NotificationController::fetch');
    $routes->post('notifications/read-all', 'App\NotificationController::readAll');
    $routes->post('notifications/(:num)/read', 'App\NotificationController::read/$1');
});



/*
|--------------------------------------------------------------------------
| CREATOR AUTH (PUBLIC)
|--------------------------------------------------------------------------
*/
$routes->get('creator', 'Creator\AuthController::login');
$routes->get('creator/login', 'Creator\AuthController::login');
$routes->post('creator/login', 'Creator\AuthController::attempt');
$routes->get('creator/register', 'Creator\AuthController::register');
$routes->post('creator/register', 'Creator\AuthController::store');
$routes->get('creator/logout', 'Creator\AuthController::logout');



/*
|--------------------------------------------------------------------------
| CREATOR AREA (AUTH: CREATOR)
|--------------------------------------------------------------------------
*/
$routes->group('creator', ['filter' => 'auth:creator'], function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'Creator\DashboardController::index');

    // Content Management
    $routes->get('content', 'Creator\ContentController::index');
    $routes->get('content/create', 'Creator\ContentController::create');
    $routes->post('content', 'Creator\ContentController::store');

    $routes->get('content/(:num)/edit', 'Creator\ContentController::edit/$1');
    $routes->post('content/(:num)/update', 'Creator\ContentController::update/$1');

    $routes->post('content/(:num)/publish', 'Creator\ContentController::publish/$1');
    $routes->post('content/(:num)/archive', 'Creator\ContentController::archive/$1');
    $routes->post('content/(:num)/delete', 'Creator\ContentController::destroy/$1');
    $routes->post('artikel/upload-image', 'Creator\ArticleUploadController::uploadImage');

    // Chapter Management (for text works)
    $routes->get('content/(:num)/chapters', 'Creator\ChapterController::index/$1');
    $routes->get('content/(:num)/chapters/create', 'Creator\ChapterController::create/$1');
    $routes->post('content/(:num)/chapters', 'Creator\ChapterController::store/$1');
    $routes->get('content/(:num)/chapters/(:num)/edit', 'Creator\ChapterController::edit/$1/$2');
    $routes->post('content/(:num)/chapters/(:num)/update', 'Creator\ChapterController::update/$1/$2');
    $routes->post('content/(:num)/chapters/(:num)/delete', 'Creator\ChapterController::destroy/$1/$2');

    // Image Gallery Management (for image works)
    $routes->get('content/(:num)/images', 'Creator\ImageGalleryController::index/$1');
    $routes->post('content/(:num)/images/upload', 'Creator\ImageGalleryController::upload/$1');
    $routes->post('content/(:num)/images/(:num)/delete', 'Creator\ImageGalleryController::destroy/$1/$2');


    // Content Stats (simple)
    $routes->get('stats', 'Creator\StatsController::index');
    $routes->get('monetization', 'Creator\MonetizationController::index');
    $routes->post('monetization/withdraw', 'Creator\MonetizationController::withdraw');
    $routes->get('monetization/history', 'Creator\MonetizationController::history');
    $routes->get('monetization/export/excel', 'Creator\MonetizationController::exportExcel');
    $routes->get('monetization/export/pdf', 'Creator\MonetizationController::exportPdf');
    $routes->get('monetization/receipt/(:num)', 'Creator\MonetizationController::receipt/$1');
    $routes->get('stats/works/(:num)', 'Creator\StatsController::work/$1');
    $routes->get('content/(:num)/stats', 'Creator\StatsController::work/$1');

    // Settings
    $routes->get('settings', 'Creator\SettingsController::index');
    $routes->post('settings', 'Creator\SettingsController::update');
});

/*
|--------------------------------------------------------------------------
| API v1 AREA (MOBILE/FLUTTER)
|--------------------------------------------------------------------------
*/
$routes->group('api/v1', function ($routes) {
    // Auth
    $routes->post('login', 'Api\AuthApiController::login');
    $routes->post('register', 'Api\AuthApiController::register');
    $routes->get('logout', 'Api\AuthApiController::logout');

    // Protected Routes (User Only API)
    $routes->group('', ['filter' => 'api_auth:user'], function ($routes) {
        // Profile
        $routes->get('user/profile', 'Api\UserApiController::getProfile');
        $routes->post('user/profile', 'Api\UserApiController::updateProfile');

        // Works Interaction
        $routes->post('works/(:num)/like', 'Api\WorkApiController::toggleLike/$1');
        $routes->post('works/(:num)/comment', 'Api\WorkApiController::postComment/$1');
        $routes->post('works/(:num)/bookmark', 'Api\WorkApiController::toggleBookmark/$1');

        // Topup
        $routes->get('topup/balance', 'Api\TopupApiController::getBalance');
        $routes->post('topup/checkout', 'Api\TopupApiController::checkout');
    });

    // Public API
    $routes->get('works', 'Api\WorkApiController::index');
    $routes->get('works/(:num)', 'Api\WorkApiController::show/$1');
});

// Public Creator Profile (Placed at the end to avoid shadowing system routes)
$routes->get('creator/(:segment)', 'App\ProfileController::show/$1');
