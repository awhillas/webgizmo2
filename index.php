<?php
require __DIR__ . '/vendor/autoload.php';  // Composer autoloader

// Read in secrets from environment
if(file_exists(__DIR__.DIRECTORY_SEPARATOR.'.env'))
{
    $dotenv = Dotenv\Dotenv::create(__DIR__);
    $dotenv->load();
}


define('GIZMO_THEME', 'bootstrap4');
define('GIZMO_LANGUAGE', 'en');
define('GIZMO_WEBSITE_TITLE', 'WebGizmo Mark II!');
define('DEBUG', true);

$content = array(
	'/' => [  // Where its mounted
		'type' => 'local',  	// default to local FS
		'config' => [ 'prefix' => '/content' ]  // this is the default 
	],
	// '/somthing' => [
	// 	'type' => 's3',
	// 	'config' => [
	// 		'bucket' => 'gizmo-test',
	// 		'prefix' => '/',
	// 		'credentials' => [
	// 			'key'    => $_ENV['AWS_ACCESS_KEY'],
	// 			'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
	// 		],
	// 		'region' => 'ap-southeast-2',
	// 		'version' => 'latest',
	// 	],
	// ],
	// '/somethingelse' => [
	// 	'type' => 'dropbox',
	// 	'config' => [
	// 		'prefix' => '',
	// 		'authorizationToken' => $_ENV['DROPBOX_API_TOKEN'],
	// 	]
	// ],	
);

$wg = new gizmo\WebGizmo($content);

echo $wg->render();
?>