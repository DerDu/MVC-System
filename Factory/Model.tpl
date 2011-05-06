<?php
namespace {ModelNamespace};
use \AIOSystem\Api\Database;
class {ModelName} extends \MVCSystem\MVCModel {
	/**
	 * Properties
	 */
	/** @var string $Selector Where clause, MUST return exactly ONE row */
	private $Selector = null;
	/** @var \ADODB_Active_Record $ADORecord */
	private $ADORecord = null;
	/** @var array $ADORecordData */
	private $ADORecordData = array();

	/**
	 * Constructor
	 */
	public static function InstanceAdd(){
		return new {ModelName};
	}
	public static function Instance( $Selector ){
		$Model = new {ModelName};
		$Model->Selector = $Selector;
		$Model->Load();
		if( count($Model->ADORecord) > 1 ) {
			throw new \Exception('Instance selector is not biunique!');
		} else if( isset($Model->ADORecord[0]) ) {
			$Model->ADORecord = $Model->ADORecord[0];
		} else {
			return false;
		}
		return $Model;
	}
	public static function InstanceList( $Selector = '' ){
		$Model = new {ModelName};
		$Model->Selector = $Selector;
		$Model->Load();
		$InstanceList = array();
		foreach((array)$Model->ADORecord as $ADORecord) {
			$ModelInstance = new {ModelName};
			$ModelInstance->ADORecord = $ADORecord;
			array_push( $InstanceList, $ModelInstance );
		}
		return $InstanceList;
	}

	private function __construct(){
	}

	/**
	 * Read/Write
	 */
	public function Load(){
		$this->ADORecord = Database::ADOConnection()->GetActiveRecords('{ModelTable}',$this->Selector);
	}
	public function Kill(){
		$this->ADORecord->Delete();
	}
	public function Save(){
		if( is_object($this->ADORecord) ) {
			$this->ADORecord->Save();
		} else {
			$this->ADORecord = $this->_Create()->ADORecord;
		}
	}
	private function _Create(){
		$Where = array();
		foreach((array)$this->ADORecordData as $Name => $Value){
			array_push( $Where, $Name." = '".$Value."'" );
		}
		Database::Record( '{ModelTable}', $this->ADORecordData, $Where );
		return self::Instance( implode( ' AND ', $Where ) );
	}

	/**
	 * Set/Get
	 */{ModelPropertyMethod}
	public function {PropertyName}( $Value = null ){
		if( $Value !== null ){
			if( is_object($this->ADORecord) ) {
				$this->ADORecord->{PropertyName} = $Value;
			} else {
				$this->ADORecordData['{PropertyName}'] = $Value;
			}
		}
		if( is_object($this->ADORecord) ) {
			return $this->ADORecord->{PropertyName};
		} else {
			return $this->ADORecordData['{PropertyName}'];
		}
	}{/ModelPropertyMethod}
}
