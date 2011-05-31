<?php
namespace MVCSystem;
use \AIOSystem\Api\Database;
use \AIOSystem\Api\Event;

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
	protected function DatabaseUtf8( $DataArray ) {
		if(self::DEBUG){Event::Message(__METHOD__,__FILE__,__LINE__);}
		foreach( (array)$DataArray as $Index => $Content ) {
			$DataArray[$Index] = array_map( '\AIOSystem\Api\Font::MixedToUtf8', $Content );
		}
		return  $DataArray;
	}
	abstract public function Load();
	abstract public function Save();
	abstract public function Kill();
}
