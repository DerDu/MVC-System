<?php
/**
 * MVC:Model
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
use \AIOSystem\Api\Database;
use \AIOSystem\Api\Event;
/**
 * @package MVCSystem
 */
abstract class MVCModel {
	const DEBUG = false;
	/**
	 * @var null|string $ViewMethod
	 */
	private $ModelViewMethod = null;
	/**
	 * @param null|string $ModelViewMethod
	 * @return null|string
	 */
	protected function ModelViewMethod( $ModelViewMethod = null ) {
		if(self::DEBUG){Event::Message(__METHOD__,__FILE__,__LINE__);}
		if( $ModelViewMethod !== null ) {
			$this->ModelViewMethod = $ModelViewMethod;
		} return $this->ModelViewMethod;
	}
	/**
	 * @var null|int $ModelId
	 */
	private $ModelId = null;
	/**
	 * @param null|int $ModelId
	 * @return null|int
	 */
	protected function ModelId( $ModelId = null ) {
		if(self::DEBUG){Event::Message(__METHOD__,__FILE__,__LINE__);}
		if( $ModelId !== null ) {
			$this->ModelId = $ModelId;
		} return $this->ModelId;
	}
	/**
	 * @param string $Table
	 * @param array $Fieldset
	 * @param null|array() $Where
	 * @param bool $Delete
	 * @return bool
	 */
	protected function DatabaseEdit( $Table, $Fieldset = array(), $Where = null, $Delete = false ) {
		if(self::DEBUG){Event::Message(__METHOD__,__FILE__,__LINE__);}
		return Database::Record( $Table, $Fieldset, $Where, $Delete );
	}
	/**
	 * @param string $Table
	 * @param string $Where
	 * @param null|string $OrderBy
	 * @param bool $ResultSetAsArray
	 * @return array
	 */
	protected function DatabaseGet( $Table, $Where, $OrderBy = null, $ResultSetAsArray = false ) {
		if(self::DEBUG){Event::Message(__METHOD__,__FILE__,__LINE__);}
		return Database::RecordSet( $Table, $Where.(($OrderBy === null)?'':' ORDER BY '.$OrderBy), $ResultSetAsArray );
	}
	/**
	 * Map database result content to UTF8 encoding
	 *
	 * @param array $DataArray
	 * @return array
	 */
	protected function DatabaseUtf8( $DataArray ) {
		if(self::DEBUG){Event::Message(__METHOD__,__FILE__,__LINE__);}
		foreach( (array)$DataArray as $Index => $Content ) {
			$DataArray[$Index] = array_map( '\AIOSystem\Api\Font::MixedToUtf8', $Content );
		}
		return $DataArray;
	}
	/**
	 * Load model data from store
	 * Select
	 *
	 * @abstract
	 * @return void
	 */
	abstract public function _oLoad();
	/**
	 * Save model data to store
	 * Insert/Update
	 *
	 * @abstract
	 * @return void
	 */
	abstract public function _oSave();
	/**
	 * Load model data from store
	 * Delete
	 *
	 * @abstract
	 * @return void
	 */
	abstract public function _oKill();
	/**
	 * Get current model data
	 * Data (array)
	 *
	 * @abstract
	 * @return void
	 */
	abstract public function _oData();
}
