<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('search', 'Home::searchResults'); // use ?q=xxx
$routes->get('terms-of-service', 'Home::termsOfService');
$routes->get('privacy-policy', 'Home::privacyPolicy');

// BUSINESS //
// business product/service listing page
$routes->get('(:any)/catalog', 'Home::businessCatalog/$1');
// product info page
$routes->get('(:any)/product/(:any)', 'Home::businessProductInfo/$1/$2');
// service info page
$routes->get('(:any)/service/(:any)', 'Home::businessServiceInfo/$1/$2');
//       - search sessions
$routes->get('(:any)/service/(:any)/sessions', 'Home::businessServiceSessions/$1/$2');
//       - search available slots
$routes->get('(:any)/service/(:any)/slots', 'Home::businessServiceSlots/$1/$2');
// cart page
$routes->get('(:any)/cart', 'Home::businessCart/$1');
// checkout page
$routes->get('(:any)/checkout', 'Home::businessCheckout/$1');
// other businesses' page
$routes->get('(:any)/pages/(:any)', 'Home::showBusinessPages/$1/$2');
// business main page
$routes->get('(:any)', 'Home::showBusinessHome/$1');