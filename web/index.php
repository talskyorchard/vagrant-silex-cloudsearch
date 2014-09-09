<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

use Curl\Curl;
use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views'
));

$app['debug'] = true;
$app['config'] = parse_ini_file(__DIR__ . '/../config/config.ini');

/*
 * PUBLIC PAGES
 */
$app->get('/', function  () use( $app)
{
    return $app['twig']->render('index.twig', array(
        'name' => 'test'
    ));
});

/*
 * Single Release Page
 */
$app->get('/release/{release_id}', function  ($release_id) use( $app)
{
    return $app['twig']->render('release.twig', array(
        'release_id' => $release_id
    ));
})
    ->assert('release_id', '\d+');

/*
 * Artist Details and Releases for Artist
 */
$app->get('/artist/{artist_id}', function  ($artist_id) use( $app)
{
    return $app['twig']->render('artist.twig', array(
        'artist_id' => $artist_id
    ));
})
    ->assert('artist_id', '\d+');

/*
 * JSON GET API
 */

/*
 * Basic Search Term
 */
$app->get('/api/search/{term}', function  ($term) use( $app)
{
    
    $raw_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/search';
    $curl = new Curl();
    $curl->get($raw_url_no_params, array(
        'q' => $term,
        'facet.genre' => '{}',
        'return' => '_all_fields'
    ));
    $response = $curl->response;

    return $app->json($response);
});

/*
 * Search with facet selection
 * /api/search/faceted/{term}?facet.genre=Jazz&facet.format=CD
 */
$app->get('/api/search/faceted/{term}', function  (Request $request, $term) use( $app)
{
    $facet_whitelist = array('genre');
    $facet_array = false;
    $get_params = $request->query->all();
    $facet_expressions = '';
    foreach ($get_params as $k => $v) {
        if (strpos($k, 'facet.') !== FALSE) {
            list($discard, $facet) = explode('.', $k);
            if (in_array($facet, $facet_whitelist, TRUE)) {
                $facet_expressions .= " (and $facet_name:'${$terms_array[$k]}')";
            }
        }
    } 
    $facet_container = "(or %s)";
    $full_facet_expression = sprintf($facet_container, $facet_expressions);    

    $raw_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/search';
    $curl = new Curl();
    $params_array = array(
        'q' => $term,
        'fq' => $full_facet_expression,
        'facet.genre' => '{}',
        'return' => '_all_fields'
    );
    $curl->get($raw_url_no_params, $params_array);
    $response = $curl->response;

    return $app->json($response);
});

/*
 * Basic Suggest
*/
$app->get('/api/suggest/release_name/{prefix}', function  ($prefix) use($app)
{
    $raw_suggest_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/suggest';
    $curl = new Curl();
    $curl->get($raw_suggest_url_no_params, array(
        'q' => $prefix,
        'suggester' => 'release_name_suggester'
    ));
    $response = $curl->response;

    return $app->json($response);
});
/*
 * Artist Name Suggest
 */
$app->get('/api/suggest/release_name/{prefix}', function  ($prefix) use($app)
{
    $raw_suggest_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/suggest';
    $curl = new Curl();
    $curl->get($raw_suggest_url_no_params, array(
        'q' => $prefix,
        'suggester' => 'artist_name_suggester'
    ));
    $response = $curl->response;

    return $app->json($response);
});

/*
 * Release
 */
$app->get('/api/release/{release_id}', function  ($release_id) use( $app)
{
    
    $raw_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/search';
    $curl = new Curl();
    $curl->get($raw_url_no_params, array(
        'q' => "(and (term field=release_id '$release_id'))",
        'q.parser' => 'structured'
    ));
    $response = $curl->response;
    
    return $app->json($response);
    
})->assert('release_id', '\d+');

/*
 * Artist (List of releases for artist)
 */
$app->get('/api/artist/{artist_id}', function  ($artist_id) use( $app)
{
    
    $raw_url_no_params = 'http://search-dev-releases-v3-po53rzkarns7qzscfw5tlpk4k4.us-east-1.cloudsearch.amazonaws.com/2013-01-01/search';
    $curl = new Curl();
    $curl->get($raw_url_no_params, array(
        'q' => "(and (term field=artist_id '$artist_id'))",
        'q.parser' => 'structured'
    ));
    $response = $curl->response;
    
    return $app->json($response);
    
})->assert('artist_id', '\d+');

$app->run();