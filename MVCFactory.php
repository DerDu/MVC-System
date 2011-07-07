<?php
/**
 * MVC:Factory
 *
 // ---------------------------------------------------------------------------------------
 * LICENSE (BSD)
 *
 * Copyright (c) 2011, Gerd Christian Kunze
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *
 *  * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 *  * Neither the name of Gerd Christian Kunze nor the names of the
 *    contributors may be used to endorse or promote products derived from
 *    this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
// ---------------------------------------------------------------------------------------
 *
 * @package MVCSystem
 */
namespace MVCSystem;
use \AIOSystem\Api\Event;
use \AIOSystem\Api\Stack;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Database;
use \AIOSystem\Api\Template;
/**
 * @package MVCSystem
 */
class MVCFactory {
	const MVCFACTORY_MODELSTORE_PREFIX = 'MVCModelStore';
	/** @var null|\AIOSystem\Core\ClassStackPriority $ModelPropertyStack */
	private static $ModelPropertyStack = null;
	/**
	 * This method is used to create/overwrite a full model
	 *
	 * @static
	 * @param string $ModelNamespace __NAMESPACE__
	 * @param string $ModelName
	 * @param string $TableName
	 * @param false|boolean $doOverwrite
	 * @param null|string $Directory
	 * @param int $Level
	 * @return void
	 */
	public static function BuildModel( $ModelNamespace, $ModelName, $TableName, $doOverwrite = false, $Directory = null, $Level = 0 ) {
		$TableName = str_replace(' ', '', ucwords(
			preg_replace( array('![^\w\d]!is', '!\s{2,}!'), array(' ',''),
				self::MVCFACTORY_MODELSTORE_PREFIX.'\\'.$ModelNamespace.'\\'.$TableName
			)
		));
		// Create Store
		$TableList = Database::ADOConnection()->MetaTables('TABLES');
		$TableList = array_map( 'strtoupper', $TableList );
		if( !in_array( strtoupper($TableName), $TableList ) ) {
			self::CreateStore( $TableName );
		} else if( true === $doOverwrite ) {
			Database::DropTable( $TableName );
			self::CreateStore( $TableName );
		}
		// Create Model
		self::CreateModel( $ModelNamespace, $ModelName, $TableName, $Directory, $Level );
	}

	/**
	 * Create model class
	 *
	 * @static
	 * @param string $Namespace __NAMESPACE__
	 * @param string $Name
	 * @param string $Table
	 * @param null|string $Directory
	 * @param int $Level
	 * @return void
	 */
	private static function CreateModel( $Namespace, $Name, $Table, $Directory = null, $Level = 0 ) {
		global $ADODB_ASSOC_CASE;
		if( $Directory === null ) {
			$Directory = System::DirectoryCurrent();
		}
		/** @var \ADORecordSet $RecordSet */
		$RecordSet = Database::ADOConnection()->SelectLimit("SELECT * FROM ".$Table,1);
		/** @var array $Record */
		$Record = $RecordSet->GetRowAssoc($ADODB_ASSOC_CASE);
		$Blueprint = Template::Load( System::DirectorySyntax( __DIR__.DIRECTORY_SEPARATOR.'Factory'.DIRECTORY_SEPARATOR.'Model.tpl' ), false );
		$Blueprint->Assign('ModelNamespace',$Namespace);
		$Blueprint->Assign('ModelName',$Name);
		$Blueprint->Assign('ModelTable',$Table);
		$PropertyList = array();
		$PropertyCount = 0;
		foreach( (array)$Record as $PropertyName => $Void ) {
			$PropertyList[$PropertyCount]['PropertyName'] = $PropertyName;
			$PropertyList[$PropertyCount]['PropertyValue'] = 'null';
			$PropertyCount++;
		}
		$Blueprint->Repeat('ModelProperty', $PropertyList );
		$Blueprint->Repeat('ModelPropertyMethod', $PropertyList );
		//$Model = System::File( System::CreateDirectory( System::DirectorySyntax( __DIR__.DIRECTORY_SEPARATOR.MVCLoader::BaseDirectoryApplication().DIRECTORY_SEPARATOR.$Namespace ) ).$Name.'.php' );
		//Event::Message( $Directory, __METHOD__ );
		if( $Level > 0 ) {
			$Namespace = explode( '\\',$Namespace );
			for( $WalkLevel = $Level; $WalkLevel > 0; $WalkLevel-- ) {
				array_shift( $Namespace );
			}
			$Namespace = implode( '\\',$Namespace );
		}
		$Model = System::File( System::CreateDirectory( System::DirectorySyntax( $Directory.DIRECTORY_SEPARATOR.$Namespace ) ).$Name.'.php' );
		$Model->propertyFileContent( $Blueprint->Parse() );
		$Model->writeFile();
		//Event::Debug( $Model->propertyFileLocation(), __FILE__,__LINE__ );
	}
	/**
	 * Create model table
	 *
	 * @static
	 * @param string $Table
	 * @return void
	 */
	private static function CreateStore( $Table ) {
		$FieldSet = self::$ModelPropertyStack->listData();
		self::$ModelPropertyStack = Stack::Priority('\\'.__CLASS__.'::SortProperty');
		Database::CreateTable( $Table, $FieldSet );
	}
	/**
	 * This method is used to add properties to the model
	 *
	 * @static
	 * @param string $Name
	 * @param string $Type
	 * @param null $Length
	 * @return void
	 */
	public static function AddProperty( $Name, $Type, $Length = null ) {
		$Property = array( trim($Name), trim($Type), ($Length===null?null:trim($Length)) );
		// Add options
		$ParameterCount = func_num_args();
		for( $Run = 3; $Run < $ParameterCount; $Run++ ) {
			array_push( $Property, trim(func_get_arg($Run)) );
		}
		if( self::$ModelPropertyStack === null ) {
			self::$ModelPropertyStack = Stack::Priority('\\'.__CLASS__.'::SortProperty');
		}
		self::$ModelPropertyStack->pushData( $Property );
	}
	/**
	 * This method is used to remove properties from the model
	 *
	 * @static
	 * @param string $Name
	 * @return bool
	 */
	public static function RemoveProperty( $Name ) {
		$DataList = self::$ModelPropertyStack->listData();
		foreach( (array)$DataList as $Index => $Data ) {
			if( $Data[0] == trim($Name) ) {
				self::$ModelPropertyStack->removeData( $Index );
				return true;
			}
		}
		return false;
	}

	/**
	 * This method is used to sort model properties
	 *
	 * should not be called
	 *
	 * @static
	 * @param array $A
	 * @param array $B
	 * @return int
	 */
	public static function SortProperty( $A, $B ) {
		$A = $A[0]; $B = $B[0];
		// "id" is always first column
		if( strtoupper($A) == 'ID' ) {
			return -1;
		}
		if( strtoupper($B) == 'ID' ) {
			return 1;
		}
		// Sort other columns
		$S = array( $A, $B );
		sort( $S );
		if( $S[0] != $A ) {
			return 1;
		} else {
			return -1;
		}
	}
/*
	public static function DEBUG() {
		Event::Debug( self::$ModelPropertyStack );
	}
*/
}
