<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
//matrciula 
/*
$routes->get('matricula','MatriculaController::index');// bug acces 
$routes->post('matricula','MatriculaController::index_post');
*/
$routes->get('matricula','MatriculaController::matricula_view');// bug acces 
$routes->post('matricula','MatriculaController::matricula_post');

// Endpoint para demostración de selects encadenados
$routes->get('privat/education','MatriculaController::education_dropdowns');$routes->get('privat/education','MatriculaController::education_dropdowns'); // alias bajo layout privat// APIs JSON
$routes->get('matricula/estructuras','MatriculaController::estructuras');
$routes->get('matricula/asignaturas','MatriculaController::asignaturas');
$routes->get('matricula/buscar','MatriculaController::buscar');
$routes->post('matricula/estructura/save','MatriculaController::saveEstructura');
$routes->post('matricula/estructura/delete/(:num)','MatriculaController::deleteEstructura/$1');
$routes->post('matricula/asignatura/save','MatriculaController::saveAsignatura');
$routes->post('matricula/asignatura/delete/(:num)','MatriculaController::deleteAsignatura/$1');

// Rutas bajo "privat" para layout persistente
$routes->get('privat/expedientes', 'MatriculaController::expedientes_view');
$routes->get('privat/validados', 'MatriculaController::validados_view');

$routes->post('privat/validados/(:segment)', 'MatriculaController::validados_view_2/$1');


$routes->get('privat/validar/(:num)', 'MatriculaController::validar_view/$1');


$routes->get('privat/historial', 'MatriculaController::historial_view');
$routes->get('privat/mensatges', 'MatriculaController::mensatges_view');
$routes->get('matricula/datos_alumne','MatriculaController::m_alumne_view');
$routes->post('matricula/datos_alumne','MatriculaController::m_alumne_post');
$routes->get('matricula/datos_pagament','MatriculaController::m_pagament_view');
$routes->post('matricula/datos_pagament','MatriculaController::m_pagament_post');
//login public 
$routes->get('public/login','LoginController::log');
$routes->post('public/login','LoginController::log_post');
$routes->get('public/login_code','LoginController::login_code');
$routes->post('public/login_code','LoginController::login_code_post');

