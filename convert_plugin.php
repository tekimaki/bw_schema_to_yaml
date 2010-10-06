#!/usr/bin/php 
<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */
global $gShellScript;
$gShellScript = true;
require_once( '../kernel/setup_inc.php' );
require_once( UTIL_PKG_PATH.'spyc/spyc.php' );
require_once( './Schema.php' );

global $gBitSmarty, $gBitInstaller;

$excludePkgs = array('pkgmkr'); // array('kernel','liberty','users','accounts');

$gBitInstaller = new Schema();

// load up all our plugins schemas
$gBitInstaller->scanPackages( 'bit_setup_inc.php', TRUE, 'all', TRUE, TRUE );
$gBitInstaller->verifyInstalledPackages( 'all' );
// vd( $gBitInstaller->mPackages );
// die;

chdir(BIT_ROOT_PATH);

// render all schema files
// Open a known directory, and proceed to read its contents
foreach( $gBitInstaller->mPackages as $package=>$packageHash ) {
	if( !in_array( $package, $excludePkgs ) ){
		global $gLibertySystem;		
		$paths = $gLibertySystem->getPackagePluginPaths($package);		
		foreach ($paths as $dir) {
			if (is_dir($dir)) {
				if (is_dir($dir)) {
					if ($dh = opendir($dir)) {
						while (($file = readdir($dh)) !== false) {
							if (is_dir($dir.'/'.$file) && $file != '.' && $file != '..') {
								$schema = $dir.'/'.$file.'/schema_inc.php';
								if (is_file($schema)) {
									unset($gBitInstaller->mPackages[$package]);
									unset($gBitInstaller->mServices[$package]);
									$gBitInstaller->mPackages[$package] = array();
									$gBitInstaller->mServices[$package] = array();
									echo "Loading: ".$schema . PHP_EOL;
									require($schema);
									// We don't need the content types. That is a lie.
									$filename = $dir.'/'.$file.'/schema.yaml';
									$gBitInstaller->cleanPackageHash( $package, $gBitInstaller->mPackages[$package] );
									unset($gBitInstaller->mPackages[$package]['contenttypes']);
									$gBitInstaller->mPackages[$package]['plugin'] = $gBitInstaller->mServices[$package];
									$content = Spyc::YAMLDump(array( $file => $gBitInstaller->mPackages[$package] ) );
									
									
									if (!empty($content)) {
										echo 'writing: '.$filename.PHP_EOL;
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
						}
						closedir($dh);
					}
				}
			}
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

