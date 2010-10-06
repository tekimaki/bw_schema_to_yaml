<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */
/* vim: :set fdm=marker : */

require_once( '../install/BitInstaller.php' );

class Schema extends BitInstaller{

	function __construct(){
		BitSystem::BitSystem();
	}

	function registerSchemaSequences( $pPackage, $pSeqHash ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		if( empty( $this->mPackages[$pPackage]['sequences'] ) ){
			$this->mPackages[$pPackage]['sequences'] = array();
		}
		$this->mPackages[$pPackage]['sequences'] = array_merge( $this->mPackages[$pPackage]['sequences'], $pSeqHash );
	}

	function registerServicePreferences( $pPackage, $pServiceGuid, $pContentTypes ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		if( empty( $this->mServices[$pPackage][$pServiceGuid] ) ){
			$this->mServices[$pPackage][$pServiceGuid] = array();
		}
		$this->mServices[$pPackage][$pServiceGuid] = array_merge( $this->mServices[$pPackage][$pServiceGuid], $pContentTypes );
	}

	function registerSchemaTable( $pPackage, $pTableName, $pDataDict, $pRequired=FALSE, $pTableOptions=NULL ) {
		$pPackage = strtolower( $pPackage ); // lower case for uniformity
		if( !empty( $pTableName ) ) {
			$this->mPackages[$pPackage]['tables'][$pTableName] = $pDataDict;
			if( !empty( $pTableOptions ) ) {
				$this->mPackages[$pPackage]['tables']['options'][$pTableName] = $pTableOptions;
			}
		}
	}

	function registerSchemaConstraints( $pPackage, $pTableName, $pConstraints ) {
		$pPackage = strtolower( $pPackage);
		if( !empty( $pTableName ) ) {
			$this->mPackages[$pPackage]['constraints'][$pTableName] = $pConstraints;
		}
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
					$data['handler_class'] => $data['handler_file']
				);
			}
		}
	}
}
