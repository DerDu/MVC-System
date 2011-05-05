<?php
namespace MVCSystem;
use \AIOSystem\Api\Template;
use \AIOSystem\Api\Stack;
use \AIOSystem\Api\Event;
abstract class MVCView {
	/** @var \AIOSystem\Core\ClassStackRegister|null */
	private $DataRegister = null;

	function __construct() {
		$this->DataRegister = Stack::Register();
	}

	/**
	 * Set a view memory register
	 */
	public function setData( $Index, $Data ) {
		return $this->DataRegister->setRegister( $Index, $Data );
	}
	/**
	 * Get a view memory register
	 */
	public function getData( $Index ) {
		return $this->DataRegister->getRegister( $Index );
	}

	/**
	 * Load a view template
	 *
	 * @param string $Name Example.tpl
	 */
	public function Template( $Name ) {
		if( file_exists( preg_replace('!^\\\\!is','', MVCManager::DirectoryApplication().DIRECTORY_SEPARATOR.$this->BaseDirectory().DIRECTORY_SEPARATOR.$Name, 1 ) ) ) {
			$TemplateFileLocation = preg_replace('!^\\\\!is','', MVCManager::DirectoryApplication().DIRECTORY_SEPARATOR.$this->BaseDirectory().DIRECTORY_SEPARATOR.$Name, 1 );
		} else {
			$TemplateFileLocation = __DIR__.DIRECTORY_SEPARATOR.$this->BaseDirectory().DIRECTORY_SEPARATOR.$Name;
		}
		//Event::Message('TPL Load: '.$TemplateFileLocation);
		return Template::Load( $TemplateFileLocation );
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