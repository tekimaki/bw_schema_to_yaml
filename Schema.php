<?php

require_once( '../install/BitInstaller.php' );

class Schema extends BitInstaller{

	function __construct(){
		BitSystem::BitSystem();
	}

	function registerUserPermissions( $pPackageName, $pUserpermissions ) {
		foreach( $pUserpermissions as $perm ) {
			$this->mPackages[$perm[3]]['permissions'][$perm[0]]=array(
				'description'=>$perm[1],
				'level'=>$perm[2],
				);
		}
	}

	function registerPreferences( $pkg, $pData ) {
		foreach( $pData as $data) {
			$this->mPackages[$data[0]]['preferences'][$data[1]] = $data[2];
		}
	}

	function cleanPackageHash( $pkg, &$pkgHash ){
		global $gBitSystem;

		if( !empty( $gBitSystem->mRequirements[$pkg] ) )
			$pkgHash['requirements'] = $gBitSystem->mRequirements[$pkg];
		if( !empty( $gBitSystem->mPackages[$pkg]['info']['version'] ) )
			$pkgHash['version'] = $gBitSystem->mPackages[$pkg]['info']['version'];
		if( !empty( $gBitSystem->mPackages[$pkg]['info']['description'] ) )
			$pkgHash['description'] = $gBitSystem->mPackages[$pkg]['info']['description'];
		if( !empty( $gBitSystem->mPackages[$pkg]['info']['license'] ) )
			$pkgHash['license']['html'] = $gBitSystem->mPackages[$pkg]['info']['license'];

		if( !empty( $pkgHash['license'] ) ){
			$pkgHash['license'] = array(
				'name'=>'LGPL',
				'description'=>'Licensed under the GNU LESSER GENERAL PUBLIC LICENSE.',
				'url'=>'http://www.gnu.org/copyleft/lesser.html',
			);
		}

		unset( $pkgHash['name'] );	
		unset( $pkgHash['service'] );	
		unset( $pkgHash['status'] );	
		unset( $pkgHash['active_switch'] );	
		unset( $pkgHash['installed'] );	
		unset( $pkgHash['url'] );	
		unset( $pkgHash['path'] );	
		unset( $pkgHash['dir'] );	
		unset( $pkgHash['db_tables_found'] );	

		require_once( LIBERTY_PKG_PATH.'LibertySystem.php' );
		$LSys = new LibertySystem();
		$LSys->loadContentTypes();
		foreach( $LSys->mContentTypes as $ctype=>$data ) {
			if( $data['handler_package'] == $pkg ){
				$pkgHash['contenttypes'] = array(
					$data['handler_class'] => $data['handler_file'],
				);
			}
		}
	}
}
