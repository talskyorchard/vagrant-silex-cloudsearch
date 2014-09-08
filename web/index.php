<?php
//use CloudSearchCall;
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__.'/../vendor/autoload.php'; 

use CloudSearchCall;
use Curl\Curl;

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app['debug'] = true;
$app['config'] = parse_ini_file(__DIR__.'/../config/config.ini');

/*
 * PUBLIC PAGES
 */

$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig', array(
        'name' => 'test',
    ));
});

$app->get('/release/{release_id}', function($release_id) use ($app) {
	return $app['twig']->render('release.twig', array(
			'release_id' => $release_id,
	));
});







/*
 *  JSON GET API
 */

/*
 * Embryonic search term JSON response
 */
$app->get('/search/{term}', function ($term) use($app) {

	$raw_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/search';	
	$curl = new Curl();
	$curl->get($raw_url_no_params, array('q' => urlencode($term), 'return' => '_all_fields'));
	$response = $curl->response;
	
	//$something = new CloudSearchCall\CloudSearchCallSearch();
	
	return $app->json($response);
});
$app->get('/api/search/{term}', function ($term) use($app) {

	$raw_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/search';
	$curl = new Curl();
	$curl->get($raw_url_no_params, array(
		'q' => $term, 
		'return' => '_all_fields'));
	$response = $curl->response;

	//$something = new CloudSearchCall\CloudSearchCallSearch();

	return $app->json($response);
});
	
/*
 * Single Release Page
 */
$app->get('/api/release/{release_id}', function ($release_id) use($app) {

	$raw_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/search';
	$curl = new Curl();
	$curl->get($raw_url_no_params, array(
		'q' => "(and (term field=release_id '$release_id'))",
		'q.parser' => 'structured'
	));
	$response = $curl->response;

	return $app->json($response);
});

$app->run();