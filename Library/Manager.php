<?php
namespace MVCSystem\Library;
use \AIOSystem\Api\Event;
use \AIOSystem\Api\System;

class Manager {

	private $PathToModel = '';
	private $PathToView = '';
	private $PathToController = '';
	private $PathToApplication = '';

	/** @var null|Router $Router */
	private $Router = null;

	public static function Instance( $PathToModel, $PathToView, $PathToController, $PathToApplication ) {
		return new Manager( $PathToModel, $PathToView, $PathToController, $PathToApplication );
	}
	private function __construct( $PathToModel, $PathToView, $PathToController, $PathToApplication ) {
		$this->PathToModel( $PathToModel );
		$this->PathToView( $PathToView );
		$this->PathToController( $PathToController );
		$this->PathToApplication( $PathToApplication );
		spl_autoload_register( array($this,'AutoLoader') );
	}

	public function PathToModel( $Path = null ) {
		if( $Path !== null ) {
			$this->PathToModel = $Path;
		} return $this->PathToModel;
	}
	public function PathToView( $Path = null ) {
		if( $Path !== null ) {
			$this->PathToView = $Path;
		} return $this->PathToView;
	}
	public function PathToController( $Path = null ) {
		if( $Path !== null ) {
			$this->PathToController = $Path;
		} return $this->PathToController;
	}
	public function PathToApplication( $Path = null ) {
		if( $Path !== null ) {
			$this->PathToApplication = $Path;
		} return $this->PathToApplication;
	}

	public function BootRouter( $RouterTableFile ) {
		$this->Router = Router::Instance( $RouterTableFile );
	}

	public function Execute( $Route = null ) {
		return $this->Route( $Route );
	}

	/**
	 * @param string $Path
	 * @return bool|Route
	 */
	private function Route( $Path ) {
		$Route = $this->Router->Search( $Path );
		$Controller = $this->PathToController().'\\'.$Route->Controller();
		$Action = $Route->Action();
		$Parameter = $Route->Parameter();
		$Reflection = new \ReflectionClass( $Controller );

		if( ! $Reflection->hasMethod( $Action ) ) {
			$Route = $this->Router->Search( '/Error/NoRoute/'.urlencode($Path) );
			$Controller = $this->PathToController().'\\'.$Route->Controller();
			$Action = $Route->Action();
			$Parameter = $Route->Parameter();
			$Reflection = new \ReflectionClass( $Controller );
		}

		$Method = $Reflection->getMethod( $Action );
		$ArgumentDefinition = $Method->getParameters();
		$ArgumentList = array();
		/** @var \ReflectionParameter $Argument */
		foreach( (array)$ArgumentDefinition as $Argument ) {
			if( isset( $_REQUEST[$Argument->getName()] ) ) {
				//Event::Message('In Request? -> Request to Parameter');
				array_push( $ArgumentList, $_REQUEST[$Argument->getName()] );
			} else if( in_array( $Argument->getName(), array_keys( $Parameter ) ) ) {
				//Event::Message('In Default? -> Default to Parameter');
				array_push( $ArgumentList, $Parameter[$Argument->getName()] );
			} else {
				//Event::Message('Empty? -> push NULL');
				array_push( $ArgumentList, null );
			}
		}
		$Application = new $Controller();
		return $this->Display(
			$Application->Call( $Action, $ArgumentList )
		);
	}
	private function Display( \MVCSystem\Generic\View $View ) {
		if( false === ( $Cache = $View->CacheGet() ) ) {
			return $View->Display();
		}
		return $Cache->ContentCache;
	}
	/**
	 * AutoLoader: Application (MVC)
	 *
	 * @param string $Class
	 * @return bool
	 */
	private function AutoLoader( $Class ) {
		/**
		 * Check: Class matches loader
		 */
		if( strpos( $Class, Manager::PathToModel(), 0 ) === 0 ) {
			return $this->_loadModelClass( $Class );
		}
		if( strpos( $Class, Manager::PathToView(), 0 ) === 0 ) {
			return $this->_loadModelClass( $Class );
		}
		if( strpos( $Class, Manager::PathToController(), 0 ) === 0 ) {
			return $this->_loadModelClass( $Class );
		}
		return false;
	}
	private function _loadModelClass( $Class ) {
		/**
		 * Replace: __NAMESPACE__ -> Application directory
		 * Build: Real path to class file
		 */
		$ClassPath = realpath( System::DirectoryRoot() . DIRECTORY_SEPARATOR . Manager::PathToApplication() . DIRECTORY_SEPARATOR . $Class . '.php' );
		/**
		 * Check: File exists
		 */
		if( file_exists( $ClassPath ) ) {
			/**
			 * Load: Class file
			 */
			//Event::Message( 'Load: '.$Class.' -> '.$ClassPath, __METHOD__,__LINE__ );
			require_once( $ClassPath );
			return true;
		}
		return false;
	}
	private function _loadViewClass( $Class ) {
		/**
		 * Replace: __NAMESPACE__ -> Application directory
		 * Build: Real path to class file
		 */
		$ClassPath = realpath( System::DirectoryRoot() . DIRECTORY_SEPARATOR . Manager::PathToApplication() . DIRECTORY_SEPARATOR . $Class . '.php' );
		/**
		 * Check: File exists
		 */
		if( file_exists( $ClassPath ) ) {
			/**
			 * Load: Class file
			 */
			//Event::Message( 'Load: '.$Class.' -> '.$ClassPath, __METHOD__,__LINE__ );
			require_once( $ClassPath );
			return true;
		}
		return false;
	}
	private function _loadControllerClass( $Class ) {
		/**
		 * Replace: __NAMESPACE__ -> Application directory
		 * Build: Real path to class file
		 */
		$ClassPath = realpath( System::DirectoryRoot() . DIRECTORY_SEPARATOR . Manager::PathToApplication() . DIRECTORY_SEPARATOR . $Class . '.php' );
		/**
		 * Check: File exists
		 */
		if( file_exists( $ClassPath ) ) {
			/**
			 * Load: Class file
			 */
			//Event::Message( 'Load: '.$Class.' -> '.$ClassPath, __METHOD__,__LINE__ );
			require_once( $ClassPath );
			return true;
		}
		return false;
	}
}
