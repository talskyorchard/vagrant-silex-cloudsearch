<?php

error_reporting(E_ALL);
ini_set('display_errors', True);


require_once __DIR__.'/../vendor/autoload.php'; 

use Aws\CloudSearch\CloudSearchClient;

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app['debug'] = true;
$app['config'] = parse_ini_file(__DIR__.'/../config/config.ini');


/*
 * Basic Index
 */
$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig', array(
        'name' => 'test',
    ));
});

/*
 * Silex's original example
 */
$app->get('/hello/{name}', function ($name) use($app) {
    return $app['twig']->render('hello.twig', array(
        'name' => $name,
    ));
});


/*
 * Embryonic search term JSON response
 */
$app->get('/search/{term}', function ($term) use($app) {
	
	//The SDK will use the $_SERVER superglobal and/or getenv() function to look for the 
	//AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY environment variables. 
	$_SERVER['AWS_ACCESS_KEY_ID'] = $app['config']['AWS_ACCESS_KEY_ID'];
	$_SERVER['AWS_SECRET_ACCESS_KEY'] = $app['config']['AWS_SECRET_ACCESS_KEY'];	
	$configClient = CloudSearchClient::factory(array('region'  =>  $app['config']['AWS_REGION']));
	$domainClient = $configClient->getDomainClient( $app['config']['AWS_URL']);
	$result = $domainClient->search(array('query' => $term));
	$hits = $result->getPath('hits');
	return $app->json($hits);
});

$app->run();