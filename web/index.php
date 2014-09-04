<?php
require_once __DIR__.'/../vendor/autoload.php'; 

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));


/*
 * Basic Index
 */
$app->get('/', function() use ($app) {
    return $app['twig']->render('index.twig', array(
        'name' => $name,
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
	$fakeresults = array(
			'term' => array(
				'original_request' => $term,
				'fake_response' => 'THIS IS A FAKE RESPONSE'
			)
	);
	return $app->json($fakeresults);
});






$app->run();