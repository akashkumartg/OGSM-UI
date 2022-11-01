<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Objective');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->get('/', 'Objective::index');

//users
$routes->get('/getUser', 'User::get');
$routes->post('/addUser', 'User::add');
$routes->post('/updateUser', 'User::update');
$routes->post('/AddAllUsers', 'User::AddAllUsers');


//Objective
$routes->post('/addObjective', 'Objective::add');
$routes->get('/getObjective', 'Objective::get');
$routes->post('/editObjective', 'Objective::edit');
$routes->post('/updateObjective', 'Objective::update');
$routes->post('/deleteObjective', 'Objective::delete');

//Goals
$routes->get('/objectiveDropdown', 'Goal::objectiveDropdown');
$routes->post('/addGoal', 'Goal::add');
$routes->get('/getGoal', 'Goal::get');
$routes->post('/editGoal', 'Goal::edit');
$routes->post('/updateGoal', 'Goal::update');
$routes->post('/deleteGoal', 'Goal::delete');
$routes->post('/approvedGoal', 'Goal::approved');
$routes->get('/uom', 'Goal::uom');


//Strategies
$routes->post('/goalDropdown', 'Strategie::goalDropdown');
$routes->post('/addStrategie', 'Strategie::add');
$routes->get('/getStrategie', 'Strategie::get');
$routes->post('/editStrategie', 'Strategie::edit');
$routes->post('/updateStrategie', 'Strategie::update');
$routes->post('/deleteStrategie', 'Strategie::delete');
$routes->post('/approvedStrategie', 'Strategie::approved');
$routes->post('/rejectedStrategie', 'Strategie::rejected');

//Long Term Priorities
$routes->get('/strategieDropdown', 'LongTermPriority::strategieDropdown');
$routes->post('/addLTP', 'LongTermPriority::add');
$routes->get('/getLTP', 'LongTermPriority::get');
$routes->post('/editLTP', 'LongTermPriority::edit');
$routes->post('/updateLTP', 'LongTermPriority::update');
$routes->post('/deleteLTP', 'LongTermPriority::delete');
$routes->post('/approvedLTP', 'LongTermPriority::approved');
$routes->post('/rejectedLTP', 'LongTermPriority::rejected');

//Company Annual Priorities
$routes->post('/ltpDropdown', 'CompanyAnnualPriority::ltpDropdown');
$routes->post('/addCAP', 'CompanyAnnualPriority::add');
$routes->get('/getCAP', 'CompanyAnnualPriority::get');
$routes->post('/editCAP', 'CompanyAnnualPriority::edit');
$routes->post('/updateCAP', 'CompanyAnnualPriority::update');
$routes->post('/deleteCAP', 'CompanyAnnualPriority::delete');
$routes->post('/approvedCAP', 'CompanyAnnualPriority::approved');
$routes->post('/rejectedCAP', 'CompanyAnnualPriority::rejected');

$routes->post('/addUserAndOrganization', 'CompanyAnnualPriority::addUserAndOrganization');

//JC Priorities
$routes->get('/capDropdown', 'JCPriority::capDropdown');
$routes->post('/addJCP', 'JCPriority::add');
$routes->get('/getJCP', 'JCPriority::get');
$routes->post('/editJCP', 'JCPriority::edit');
$routes->post('/updateJCP', 'JCPriority::update');
$routes->post('/deleteJCP', 'JCPriority::delete');
$routes->post('/approvedJCP', 'JCPriority::approved');




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
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
