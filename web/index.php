<?php
error_reporting(E_ALL);
ini_set('display_errors', True);
//require_once __DIR__.'/cheapAndDirtyProfiler.php';
//profile::start('Autoload');
require_once __DIR__.'/../vendor/autoload.php'; 
//profile::stop();

use Aws\CloudSearch\CloudSearchClient;
use Curl\Curl;

//profile::start('new Silex Application');
$app = new Silex\Application();
//profile::stop();

//profile::start('register twig service provider');
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
//profile::stop();

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


//profile::start('start anonymous function call');
/*
 * Embryonic search term JSON response
 */
$app->get('/search/{term}', function ($term) use($app) {
//profile::stop();
	
	//The SDK will use the $_SERVER superglobal and/or getenv() function to look for the 
	//AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY environment variables. 
	$_SERVER['AWS_ACCESS_KEY_ID'] = $app['config']['AWS_ACCESS_KEY_ID'];
	$_SERVER['AWS_SECRET_ACCESS_KEY'] = $app['config']['AWS_SECRET_ACCESS_KEY'];

	//profile::start('cloudsearch client factory method');
	//$configClient = CloudSearchClient::factory(array('region'  =>  $app['config']['AWS_REGION']));
	//profile::stop();
	
	//profile::start('get domain client');
	//$domainClient = $configClient->getDomainClient( $app['config']['AWS_URL']);
	//profile::stop();
	
	//profile::start('issue search via domainClient->search()');
	//$result = $domainClient->search(array('query' => $term));
	//profile::stop();
	
	//$hits = $result->getPath('hits');
	
	/*
	$raw_url = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/search?q='.urlencode($term).'&return=_all_fields&sort=_score+desc';
	
	profile::start('issue search via raw curl url');
	$ch = curl_init($raw_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$data = curl_exec($ch);
	curl_close($ch);
	profile::stop();
	*/

	$raw_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/search';	
	//profile::start('issue search via php-curl-class');
	$curl = new Curl();
	$curl->get($raw_url_no_params, array('q' => urlencode($term), 'return' => '_all_fields'));
	$response = $curl->response;
	//profile::stop();
	
	//print_r($response);
	
	/*
	print('<pre>');
	profile::print_results(profile::flush()); // print the results
	print('</pre>');
	exit;
	*/
	
	return $app->json($response);
});

$app->run();