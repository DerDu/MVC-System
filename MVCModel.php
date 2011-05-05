<?php
namespace MVCSystem;
use \AIOSystem\Api\Database;
abstract class MVCModel {
	/**
	 * @var null|string $ViewMethod
	 */
	private $ModelViewMethod = null;
	/**
	 * @param null|string $ModelViewMethod
	 * @return null|string
	 */
	public function ModelViewMethod( $ModelViewMethod = null ) {
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
	public function ModelId( $ModelId = null ) {
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
	public function DatabaseEdit( $Table, $Fieldset = array(), $Where = null, $Delete = false ) {
		return Database::Record( $Table, $Fieldset, $Where, $Delete );
	}
	/**
	 * @param string $Table
	 * @param string $Where
	 * @param null|string $OrderBy
	 * @return array
	 */
	public function DatabaseGet( $Table, $Where, $OrderBy = null, $ResultSetAsArray = false ) {
		return Database::RecordSet( $Table, $Where.(($OrderBy === null)?'':' ORDER BY '.$OrderBy), $ResultSetAsArray );
	}
	abstract public function Load();
	abstract public function Save();
	abstract public function Kill();
}