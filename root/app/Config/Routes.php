<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('search', 'Home::searchResults'); // use ?q=xxx
$routes->get('terms-of-service', 'Home::termsOfService');
$routes->get('privacy-policy', 'Home::privacyPolicy');

// BUSINESS //
// product info page
$routes->get('(:any)/product/(:any)', 'Business::productInfo/$1/$2');
// service info page
$routes->get('(:any)/service/(:any)', 'Business::serviceInfo/$1/$2');
//       - search sessions
$routes->get('(:any)/search-sessions', 'Business::serviceSessions/$1');
//       - search available slots
$routes->get('(:any)/search-slots', 'Business::serviceSlots/$1');
// cart page
$routes->get('(:any)/cart', 'Business::cart/$1');
// checkout page
$routes->get('(:any)/checkout', 'Business::checkout/$1');
// check my order status
$routes->get('(:any)/check-order-status/(:any)', 'Business::checkOrderStatus/$1/$2');
$routes->get('(:any)/my-order-history', 'Business::myOrderHistory/$1');
// other businesses' page
$routes->get('(:any)/pages/(:any)', 'Business::showBusinessPages/$1/$2');
// business main page
$routes->get('(:any)/home', 'Business::systemHome/$1'); // show main home
$routes->get('(:any)', 'Business::ownHome/$1'); // show self-built home if exist, otherwise show main home
// POST
$routes->post('(:any)/search-sessions', 'Business::searchSessions/$1');
$routes->post('(:any)/search-slots', 'Business::searchSlots/$1');
$routes->post('(:any)/add-to-cart', 'Business::addToCart/$1');
$routes->post('(:any)/remove-from-cart', 'Business::removeFromCart/$1');
$routes->post('(:any)/update-cart', 'Business::updateCart/$1');
$routes->post('(:any)/clear-cart', 'Business::clearCart/$1');
$routes->post('(:any)/checkout-init', 'Business::checkoutInit/$1');
$routes->post('(:any)/checkout-confirm', 'Business::checkoutConfirm/$1');
$routes->post('(:any)/retrieve-order-status', 'Business::retrieveOrderStatus/$1');
$routes->post('(:any)/retrieve-order-history', 'Business::retrieveOrderHistory/$1');