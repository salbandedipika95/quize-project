<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 *
 * CI4 Route Syntax (CI3 array syntax is NOT supported in CI4):
 *   $routes->get('uri',  'Controller::method');
 *   $routes->post('uri', 'Controller::method');
 *
 * Two controllers only:
 *   AdminController  →  all admin pages
 *   UserController   →  auth pages + all user pages
 */

// -------------------------------------------------------
// DEFAULT
// -------------------------------------------------------
$routes->get('/', 'UserController::login');

// -------------------------------------------------------
// USER AUTH  (public — no filter)
// -------------------------------------------------------
$routes->get( 'login',           'UserController::login');
$routes->post('login',           'UserController::loginProcess');
$routes->get( 'register',        'UserController::register');
$routes->post('register',        'UserController::registerProcess');
$routes->get( 'logout',          'UserController::logout');

// -------------------------------------------------------
// ADMIN AUTH  (public — no filter)
// -------------------------------------------------------
$routes->get( 'admin/login',     'AdminController::login');
$routes->post('admin/login',     'AdminController::loginProcess');
$routes->get( 'admin/logout',    'AdminController::logout');

// -------------------------------------------------------
// ADMIN PROTECTED  (AdminFilter applied inside controller)
// -------------------------------------------------------
$routes->get( 'admin/dashboard',              'AdminController::dashboard');

// Users
$routes->get( 'admin/users',                  'AdminController::users');
$routes->get( 'admin/users/create',           'AdminController::userCreate');
$routes->post('admin/users/store',            'AdminController::userStore');
$routes->get( 'admin/users/edit/(:num)',      'AdminController::userEdit/$1');
$routes->post('admin/users/update/(:num)',    'AdminController::userUpdate/$1');
$routes->post('admin/users/toggle/(:num)',    'AdminController::userToggle/$1');

// Categories
$routes->get( 'admin/categories',             'AdminController::categories');
$routes->post('admin/categories/store',       'AdminController::categoryStore');
$routes->get( 'admin/categories/edit/(:num)', 'AdminController::categoryEdit/$1');
$routes->post('admin/categories/update/(:num)','AdminController::categoryUpdate/$1');
$routes->post('admin/categories/toggle/(:num)','AdminController::categoryToggle/$1');

// Quizzes
$routes->get( 'admin/quizzes',                'AdminController::quizzes');
$routes->get( 'admin/quizzes/create',         'AdminController::quizCreate');
$routes->post('admin/quizzes/store',          'AdminController::quizStore');
$routes->get( 'admin/quizzes/edit/(:num)',    'AdminController::quizEdit/$1');
$routes->post('admin/quizzes/update/(:num)', 'AdminController::quizUpdate/$1');
$routes->post('admin/quizzes/toggle/(:num)', 'AdminController::quizToggle/$1');

// Questions
$routes->get( 'admin/questions/(:num)',          'AdminController::questions/$1');
$routes->get( 'admin/questions/create/(:num)',   'AdminController::questionCreate/$1');
$routes->post('admin/questions/store',           'AdminController::questionStore');
$routes->get( 'admin/questions/edit/(:num)',     'AdminController::questionEdit/$1');
$routes->post('admin/questions/update/(:num)',   'AdminController::questionUpdate/$1');
$routes->post('admin/questions/toggle/(:num)',   'AdminController::questionToggle/$1');

// Attempts
$routes->get( 'admin/attempts',               'AdminController::attempts');
$routes->get( 'admin/attempts/view/(:num)',   'AdminController::attemptView/$1');

// Profile
$routes->get( 'admin/profile',               'AdminController::profile');
$routes->post('admin/profile/update',        'AdminController::profileUpdate');
$routes->post('admin/profile/password',      'AdminController::profilePassword');

// -------------------------------------------------------
// USER PROTECTED  (UserFilter applied inside controller)
// -------------------------------------------------------
$routes->get( 'user/dashboard',              'UserController::dashboard');

// Quizzes (browse)
$routes->get( 'user/quizzes',                'UserController::quizzes');
$routes->get( 'user/quizzes/view/(:num)',    'UserController::quizView/$1');
$routes->post('user/quizzes/start/(:num)',   'UserController::quizStart/$1');

// Quiz Attempt
$routes->get( 'user/attempt/(:num)',         'UserController::attempt/$1');
$routes->post('user/attempt/save-answer',    'UserController::attemptSaveAnswer');
$routes->post('user/attempt/submit/(:num)', 'UserController::attemptSubmit/$1');

// Results
$routes->get( 'user/result/(:num)',          'UserController::result/$1');
$routes->get( 'user/attempts',              'UserController::attempts');

// Profile
$routes->get( 'user/profile',               'UserController::profile');
$routes->post('user/profile/update',        'UserController::profileUpdate');
$routes->post('user/profile/password',      'UserController::profilePassword');