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
 * @subpackage MVCFactory
 */
namespace MVCSystem;
use \AIOSystem\Api\Event;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Database;
use \AIOSystem\Api\Template;
/**
 * @package MVCSystem
 * @subpackage MVCFactory
 */
class MVCFactory {
	/**
	 * @static
	 * @param string $Namespace __NAMESPACE__
	 * @param string $Name
	 * @param string $Table
	 * @return void
	 */
	public static function CreateModel( $Namespace, $Name, $Table ) {
		global $ADODB_ASSOC_CASE;
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
		$Model = System::File( System::CreateDirectory( System::DirectorySyntax( __DIR__.DIRECTORY_SEPARATOR.MVCLoader::BaseDirectoryApplication().DIRECTORY_SEPARATOR.$Namespace ) ).$Name.'.php' );
		$Model->propertyFileContent( $Blueprint->Parse() );
		$Model->writeFile();
	}
}
