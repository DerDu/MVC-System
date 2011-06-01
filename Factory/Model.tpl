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
	 *
	 * @return {ModelName}
	 */
	public static function _oInstanceAdd(){
		return new {ModelName};
	}
	public static function _oInstance( $Selector ){
		$Model = new {ModelName};
		$Model->Selector = $Selector;
		$Model->_oLoad();
		if( count($Model->ADORecord) > 1 ) {
			throw new \Exception('Instance selector is not biunique!');
		} else if( isset($Model->ADORecord[0]) ) {
			$Model->ADORecord = $Model->ADORecord[0];
		} else {
			return false;
		}
		return $Model;
	}
	public static function _oInstanceList( $Selector = '' ){
		$Model = new {ModelName};
		$Model->Selector = $Selector;
		$Model->_oLoad();
		$InstanceList = array();
		foreach((array)$Model->ADORecord as $ADORecord) {
			$ModelInstance = new {ModelName};
			$ModelInstance->ADORecord = $ADORecord;
			array_push( $InstanceList, $ModelInstance );
		}
		return $InstanceList;
	}

	/**
	 * Must not call!
	 * Use: _oInstance, _oInstanceAdd or _oInstanceList
	 */
	private function __construct(){
	}

	/**
	 * Read/Write
	 */
	public function _oLoad(){
		$this->ADORecord = Database::ADOConnection()->GetActiveRecords('{ModelTable}',$this->Selector);
	}
	public function _oKill(){
		$this->ADORecord->Delete();
	}
	public function _oSave(){
		if( is_object($this->ADORecord) ) {
			$this->ADORecord->Save();
		} else {
			$this->ADORecord = $this->_oCreate()->ADORecord;
		}
	}
	public function _oData(){
		return array(
		{ModelPropertyMethod}
			'{PropertyName}'=>$this->{PropertyName}(),
		{/ModelPropertyMethod}
		);
	}

	private function _oCreate(){
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
