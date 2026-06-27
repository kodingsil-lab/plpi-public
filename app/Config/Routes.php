<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'PublicPage::index');
$routes->get('artikel', 'PublicPage::artikel');
$routes->get('artikel/(:segment)', 'PublicPage::detailArtikel/$1');

$routes->get('ajukan-loa', 'PublicPage::ajukanLoa');
$routes->get('verifikasi-loa', 'PublicPage::verifikasiLoa');
