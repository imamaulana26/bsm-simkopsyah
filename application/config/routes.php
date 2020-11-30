<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['sales/koperasi-channeling/rekonsel/(:any)'] = 'sales/koperasi/rekonsel/rekon_channeling/$1';
// koperasi channeling
$route['sales/koperasi-channeling'] = 'sales/koperasi/channeling';
$route['sales/koperasi-channeling/export/(:any)'] = 'sales/koperasi/channeling/export/$1';
$route['sales/koperasi-channeling/template'] = 'sales/koperasi/channeling/template';
$route['sales/koperasi-channeling/import'] = 'sales/koperasi/channeling/import';
$route['sales/koperasi-channeling/insert'] = 'sales/koperasi/channeling/insert';
$route['sales/koperasi-channeling/update'] = 'sales/koperasi/channeling/update';
$route['sales/koperasi-channeling/delete/(:any)'] = 'sales/koperasi/channeling/delete/$1';
$route['sales/koperasi-channeling/find/(:any)'] = 'sales/koperasi/channeling/find/$1';
$route['sales/koperasi-channeling/(:any)'] = 'sales/koperasi/channeling/get_koperasi/$1';

// route anggota koperasi channeling
$route['sales/koperasi-channeling/template/end-user'] = 'sales/koperasi/channeling/temp_enduser';
$route['sales/koperasi-channeling/import/end-user'] = 'sales/koperasi/channeling/import_enduser';
$route['sales/koperasi-channeling/details/(:any)'] = 'sales/koperasi/channeling/details/$1';
$route['sales/koperasi-channeling/anggota/edit/(:any)'] = 'sales/koperasi/channeling/get_anggota/$1';
$route['sales/koperasi-channeling/anggota/delete/(:any)'] = 'sales/koperasi/channeling/delete_anggota/$1';
$route['sales/koperasi-channeling/anggota/update'] = 'sales/koperasi/channeling/update_anggota';
$route['sales/koperasi-channeling/anggota/insert'] = 'sales/koperasi/channeling/save_anggota';
