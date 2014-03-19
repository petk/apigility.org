#!/usr/bin/env php
<?php
/**
 * Apigility installer
 *
 * @see http://www.apigility.org
 * (C) 2013-2014 Copyright Zend Technologies Ltd.
 */

$localPath    = __DIR__;
$apigilityDir = 'apigility';
$tmpFile      = sys_get_temp_dir() . '/apigility.zip';
$port         = '8888';

checkPlatform();

if (file_exists($apigilityDir)) {
    die("Error: The apigility directory already exists\n");
}
$config = require __DIR__ . '/../config/autoload/global.php';
if (!isset($config['links']['zip'])) {
	die("Error: the Apigility release is not specified, please download it from the apigility.org");
} 

$tmpFile = sys_get_temp_dir() . '/apigility_' . md5($config['links']['zip']) . '.zip';
if (!file_exists($tmpFile)) {
	echo "Download Apigility\n";
	$fr = fopen($config['links']['zip'], 'r');
	$fw = fopen($tmpFile, 'w');
	while (!feof($fr)) {
	    fwrite($fw, fread($fr, 1048576)); // 1 MB buffer
	    echo '.';
	}
	fclose($fw);
	fclose($fr);
} else  {
	echo "Get Apigility from cache [$tmpFile]";
}

echo "\nInstall Apigility\n";

$zip = new ZipArchive;
if (!$zip->open($tmpFile)) {
    die("Error: opening file $tmpFile\n"); 
}
if (!$zip->extractTo($localPath)) {
    die("Error: extract $tmpFile\n");
}

$dirName = $zip->getNameIndex(0);
$zip->close();

// Rename to apigility
rename($dirName, 'apigility');

// Change directory to apigility
chdir($apigilityDir);

echo "Installation complete.\n";
if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
    echo "Running PHP internal web server.\n";
    echo "Open your browser to http://localhost:$port, Ctrl-C to stop it.\n";
    exec("php -S 0:$port -t public public/index.php");
} else {
    echo "I cannot execute the PHP internal web server, because you are running PHP < 5.4.\n";
    echo "You need to configure a web server to point the 'apigility/public' folder\n";
}

/**
 * Check for platform requirements
 *
 * @return void
 */
function checkPlatform()
{
    if (!class_exists('ZipArchive')) {
        die("Error: I cannot install Apigility without the Zip extension of PHP\n");
    }
}
