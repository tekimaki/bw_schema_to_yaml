#!/usr/bin/php
<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */

global $gShellScript;
$gShellScript = true;
require_once( '../kernel/setup_inc.php' );
require_once( UTIL_PKG_PATH.'spyc/spyc.php' );
require_once( './Schema.php' );

global $gBitSmarty, $gBitInstaller;

// USE THESE TO EITHER LIMIT A LIST OF ALL OR TO DEFINE A SHORT LIST
// @see the foreach loop below to switch which you want to use
// default uses include
$excludePkgs = array('pkgmkr'); // array('kernel','liberty','users' );
$includePkgs = array('liberty');

$gBitInstaller = new Schema();

// load up all our packages
$gBitInstaller->scanPackages( 'bit_setup_inc.php', TRUE, 'all', TRUE, TRUE );
$gBitInstaller->verifyInstalledPackages( 'all' );
// vd( $gBitInstaller->mPackages );
// die;

chdir(BIT_ROOT_PATH);

// render all schema files
foreach( $gBitInstaller->mPackages as $package=>$packageHash ) {
	// CHANGE THIS IF YOU WANT TO EXCLUDE OR INCLUDE
	//if( !in_array( $package, $excludePkgs ) ){
	if( in_array( $package, $includePkgs ) ){

		$filename = (!empty($packageHash['dir'])?$packageHash['dir']:$package).'/admin/schema.yaml';
		$gBitInstaller->cleanPackageHash( $package, $packageHash );
		$content = Spyc::YAMLDump(array( $package => $packageHash ) );

		echo 'writing: '.$package.PHP_EOL;

		if (!empty($content)) {
			if (!$handle = fopen($filename, 'w+')) {
				error("Cannot open file ($filename)");
			}

			// Write $content to our opened file.
			if (fwrite($handle, $content) === FALSE) {
				error("Cannot write to file ($filename)");
			}

			fclose($handle);

		} else {
			error("Error generating file: $filename", FALSE);
		}
	}
}

// utilities
function error($message, $fatal=TRUE) {
	echo $message;
	echo "\n";
	if ($fatal)
		die;
}

