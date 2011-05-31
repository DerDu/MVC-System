<?php
namespace MVCSystem;
use \AIOSystem\Api\Template;
use \AIOSystem\Api\Stack;
use \AIOSystem\Api\Cache;
use \AIOSystem\Api\Event;
abstract class MVCView {
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
	public function setCache( $Seconds = 3600 ) {
		Cache::Set( $this->idCache(), ($Display = $this->Display()), get_class( $this ), false, $Seconds );
		return $Display;
	}
	/**
	 * Return display cache or false
	 *
	 * @return false|string
	 */
	public function getCache() {
		return Cache::Get( $this->idCache(), get_class($this), false );
	}
	/**
	 * Create cache identifier
	 *
	 * @return string
	 */
	private function idCache() {
		return sha1( get_class( $this )
			.serialize( get_class_methods( get_class( $this ) ) )
			.serialize( get_class_vars( get_class( $this ) ) )
		);
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
		if( file_exists( ($Location = preg_replace('!^\\\\!is','', MVCManager::DirectoryApplication().DIRECTORY_SEPARATOR.$this->BaseDirectory().DIRECTORY_SEPARATOR.$Name, 1) ) ) ) {
			$TemplateFileLocation = $Location;
		// Try generic folder at Application (Template)
		} else if( file_exists( ($Location = preg_replace('!^\\\\!is','', MVCManager::DirectoryApplication().DIRECTORY_SEPARATOR.dirname($this->BaseDirectory()).DIRECTORY_SEPARATOR.$Folder.DIRECTORY_SEPARATOR.$Name, 1) ) ) ) {
			$TemplateFileLocation = $Location;
		// Try specific folder at MVC
		} else if( file_exists( ($Location = __DIR__.DIRECTORY_SEPARATOR.$this->BaseDirectory().DIRECTORY_SEPARATOR.$Name) ) ) {
			$TemplateFileLocation = $Location;
		// Try generic folder at MVC (Template)
		} else if( file_exists( ($Location = __DIR__.DIRECTORY_SEPARATOR.dirname($this->BaseDirectory()).DIRECTORY_SEPARATOR.$Folder.DIRECTORY_SEPARATOR.$Name) ) ) {
			$TemplateFileLocation = $Location;
		// Set location for error information
		} else {
			$TemplateFileLocation = $Name;
		}
		//Event::Message('TPL Load: '.$TemplateFileLocation);
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
