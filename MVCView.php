<?php
namespace MVCSystem;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Template;
use \AIOSystem\Api\Stack;
use \AIOSystem\Api\Cache;
use \AIOSystem\Api\Event;
abstract class MVCView {
	const DEBUG = false;
	/** @var \AIOSystem\Core\ClassStackRegister|null */
	private $DataRegister = null;

	function __construct() {
		$this->DataRegister = Stack::Register();
	}

	/**
	 * Set a view memory register
	 *
	 * @param mixed $Index
	 * @param mixed $Data
	 * @return mixed
	 */
	public function setData( $Index, $Data ) {
		return $this->DataRegister->setRegister( $Index, $Data );
	}
	/**
	 * Get a view memory register
	 *
	 * @param mixed $Index
	 * @return mixed
	 */
	public function getData( $Index ) {
		return $this->DataRegister->getRegister( $Index );
	}

	/**
	 * Set Cache and return display
	 *
	 * @param int $Seconds
	 * @return string
	 */
	public function setCache( $Seconds = 3600, $InstanceId = null ) {
		Cache::Set( ($InstanceId===null?$this->idCache():$InstanceId), ($Display = $this->Display()), get_class( $this ), false, $Seconds );
		return $Display;
	}
	/**
	 * Return display cache or false
	 *
	 * @param bool $useInstanceId
	 * @return false|string
	 */
	public function getCache( $useInstanceId = false ) {
		return Cache::Get( $this->idCache( $useInstanceId ), get_class($this), false );
	}
	/**
	 * Return cache time
	 *
	 * @param bool $useInstanceId
	 * @return int
	 */
	public function getCacheTime( $useInstanceId = false ) {
		return Cache::GetTime( $this->idCache( $useInstanceId ), get_class($this), false );
	}
	/**
	 * Create cache identifier
	 *
	 * @param bool $useInstanceId
	 * @return string
	 */
	public function idCache( $useInstanceId = false ) {
		if( $useInstanceId ) {
			return sha1( serialize( $this ) );
		} else {
			return sha1( get_class( $this )
				.serialize( get_class_methods( get_class( $this ) ) )
				.serialize( get_class_vars( get_class( $this ) ) )
			);
		}
	}

	/**
	 * Load a view template
	 *
	 * @param string $Name Example.tpl
	 * @param bool $ParsePhp - Default: true
	 * @param bool $ParsePhpAfterContent - Default: false
	 * @return \AIOSystem\Module\Template\ClassTemplate
	 */
	public function Template( $Name, $ParsePhp = true, $ParsePhpAfterContent = false ) {
		$Folder = 'Template';
		// Try specific folder at Application
		if( file_exists( ($LocationA = System::DirectorySyntax( preg_replace('!^\\\\!is','', MVCManager::DirectoryApplication().DIRECTORY_SEPARATOR.$this->BaseDirectory().DIRECTORY_SEPARATOR.$Name, 1), false, DIRECTORY_SEPARATOR ) ) ) ) {
			$TemplateFileLocation = $LocationA;
		// Try generic folder at Application (Template)
		} else if( file_exists( ($LocationB = System::DirectorySyntax( preg_replace('!^\\\\!is','', MVCManager::DirectoryApplication().DIRECTORY_SEPARATOR.dirname($this->BaseDirectory()).DIRECTORY_SEPARATOR.$Folder.DIRECTORY_SEPARATOR.$Name, 1), false, DIRECTORY_SEPARATOR ) ) ) ) {
			$TemplateFileLocation = $LocationB;
		// Try specific folder at MVC
		} else if( file_exists( ($LocationC = System::DirectorySyntax( __DIR__.DIRECTORY_SEPARATOR.$this->BaseDirectory().DIRECTORY_SEPARATOR.$Name, false, DIRECTORY_SEPARATOR ) ) ) ) {
			$TemplateFileLocation = $LocationC;
		// Try generic folder at MVC (Template)
		} else if( file_exists( ($LocationD = System::DirectorySyntax( __DIR__.DIRECTORY_SEPARATOR.dirname($this->BaseDirectory()).DIRECTORY_SEPARATOR.$Folder.DIRECTORY_SEPARATOR.$Name, false, DIRECTORY_SEPARATOR ) ) ) ) {
			$TemplateFileLocation = $LocationD;
		// Set location for error information
		} else {
			$TemplateFileLocation = $Name;
		}
		if(self::DEBUG){
			if(isset($LocationA))Event::Message('TPL Location A: '.$LocationA);
			if(isset($LocationB))Event::Message('TPL Location B: '.$LocationB);
			if(isset($LocationC))Event::Message('TPL Location C: '.$LocationC);
			if(isset($LocationD))Event::Message('TPL Location D: '.$LocationD);
			Event::Message('Load['.__METHOD__.'] '.$TemplateFileLocation.' ... ');
		}
		return Template::Load( $TemplateFileLocation, $ParsePhp, $ParsePhpAfterContent );
	}
	/**
	 * @return string
	 */
	public function BaseDirectory() {
		return str_replace('Class','',get_class( $this ));
	}
	/**
	 * This method is used to return the view content
	 *
	 * @return string
	 */
	abstract public function Display();
}
