<?php
/**
 * MVC:Manager
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
 * @subpackage MVCManager
 */
namespace MVCSystem;
use \AIOSystem\Api\Authentication as Auth;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Event;
use \AIOSystem\Api\Database;
/**
 * @package MVCSystem
 * @subpackage MVCManager
 */
class MVCManager {
	private static $BaseDirectoryApplication = '';

	private static $BaseDirectoryController = 'MVCSystem\Controller';
	private static $BaseDirectoryModel = 'MVCSystem\Model';
	private static $BaseDirectoryView = 'MVCSystem\View';

	public static $AuthenticationMainContent = '/Access/Denied/Route';
	public static $AuthenticationPartialContent = '/Access/Denied/Link';
	//public static $AuthenticationLogin = '/Authentication/Login';
	//public static $AuthenticationLogout = '/Authentication/Logout';

	/**
	 * Register MVCLoader
	 *
	 * @static
	 * @return void
	 */
	public static function RegisterLoader( $RelativePathFromMvcToApplication = null, $RelativePathToMvcSystem = null ) {
		require_once( __DIR__.DIRECTORY_SEPARATOR.'MVCLoader.php' );
		MVCLoader::RegisterLoader( $RelativePathToMvcSystem );
		MVCLoader::RegisterApplication( $RelativePathFromMvcToApplication );
	}
	/**
	 * @param string $ConfigurationFile
	 * @return void
	 */
	public static function RegisterRouter( $ConfigurationFile = null ) {
		MVCRouter::Boot( $ConfigurationFile );
	}

	/**
	 * Connect MVCRoute
	 *
	 * @static
	 * @param string $Pattern
	 * @param string $Controller
	 * @param string $Action
	 * @param array $ParameterDefault
	 * @return MVCRoute
	 */
	public static function RegisterRoute( $Pattern, $Controller = '{Controller}', $Action = '{Action}', $ParameterDefault = array(), $RestrictedAccess = false ) {
		return MVCRouter::Register(
			$Pattern,
			System::DirectorySyntax( self::DirectoryController(), true, System::DIRECTORY_SEPARATOR_BACKSLASH ).$Controller,
			$Action,
			$ParameterDefault,
			$RestrictedAccess
		);
	}
	/**
	 * Execute MVCRoute
	 *
	 * @static
	 * @param null|string $UriPath
	 * @return mixed
	 */
	public static function ExecuteRoute( $UriPath = null, $isDenied = false ) {
		/**
		 * Fetch route
		 */
		$MVCRoute = MVCRouter::Route( $UriPath );
		/**
		 * Check route restriction
		 */
		if( $MVCRoute->IsRestricted() && Auth::IsValid() ) {
			//Event::Message('Restricted User');
			$MVCRight = str_replace('\\','-',$MVCRoute->GetController().'-'.$MVCRoute->GetAction());
			/**
			 * Create/Edit access right
			 */
			Auth::EditRight( $MVCRight,'created by MVC-System',$MVCRoute->optionSource() );
			/**
			 * Check access right
			 */
			if( !Auth::Checkpoint( $MVCRight,$MVCRoute->optionSource() ) ) {
				// No access granted and guards alerted.. "security breach from known user, GET HIM OUT!"
				if( ($UriPath === null) || ($UriPath !== null && $isDenied) ) {
					Event::Journal( 'Access to restricted route: '.$MVCRoute->optionRoute(), __CLASS__ );
					//Event::Message( 'Access to restricted route: '.$MVCRoute->optionRoute(), __CLASS__ );
					if( $isDenied ) {
						$MVCRoute = MVCRouter::Route( self::AccessDeniedLink().'/'.str_replace('/','>',$UriPath) );
					} else {
					// = Main content -> Login page
						$MVCRoute = MVCRouter::Route( self::AccessDeniedRoute() );
					}
					//Event::Debug( $MVCRoute );
				// No access granted but no alert.. "somebody just watching the gate"
				} else {
					Event::Journal( 'Passing restricted route: '.$MVCRoute->optionRoute(), __CLASS__ );
					// = Partial content
					//Event::Message( '[User] Passing restricted route (silent): '.$MVCRoute->optionRoute(), __CLASS__ );
					return;
				}
			}
		} else if( $MVCRoute->IsRestricted() ) {
			//Event::Message('Restricted Guest');
			// No access granted and guards alerted.. "security breach from unknown user, GET HIM OUT!"
			if( ($UriPath === null) || ($UriPath !== null && $isDenied) ) {
				Event::Journal( 'Access to restricted route: '.$MVCRoute->optionRoute(), __CLASS__ );
				//Event::Message( 'Access to restricted route: '.$MVCRoute->optionRoute(), __CLASS__ );
				if( $isDenied ) {
					$MVCRoute = MVCRouter::Route( self::AccessDeniedLink().'/'.str_replace('/','>',$UriPath) );
				} else {
					// = Main content -> Login page
					$MVCRoute = MVCRouter::Route( self::AccessDeniedRoute() );
				}
				//Event::Debug( $MVCRoute );
				// No access granted but no alert.. "somebody just watching the gate"
			} else {
				Event::Journal( 'Passing restricted route: '.$MVCRoute->optionRoute(), __CLASS__ );
				// = Partial content
				//Event::Message( '[Guest] Passing restricted route (silent): '.$MVCRoute->optionRoute(), __CLASS__ );
				return;
			}
		} else {
			//Event::Message('Free Access');
		}
		/**
		 * Create controller
		 */
		/** @var string $MVCController */
		$MVCController = $MVCRoute->GetController();
		/** @var MVCController $MVCController */
		$MVCController = new $MVCController;
		/**
		 * Fetch required parameters
		 */
		/** @var \ReflectionClass $RefMVCController */
		$RefMVCController = new \ReflectionClass( $MVCRoute->GetController() );
		/**
		 * No Method available ? -> NoRoute
		 */
		if( ! $RefMVCController->hasMethod( $MVCRoute->GetAction() ) ) {
			$MVCRoute = MVCRouter::NoRoute( $MVCRoute->optionRoute() );
			$MVCController = $MVCRoute->GetController();
			$MVCController = new $MVCController;
			$RefMVCController = new \ReflectionClass( $MVCRoute->GetController() );
		}
		/** @var \ReflectionMethod $RefMVCAction */
		$RefMVCAction = $RefMVCController->getMethod( $MVCRoute->GetAction() );
		/** @var \ReflectionParameter[] $RefMVCParameterDefinition */
		$RefMVCParameterDefinition = $RefMVCAction->getParameters();
		/**
		 * Create combined parameter array
		 */
		$RefMVCParameterList = array();
		/** @var \ReflectionParameter $RefMVCParameter */
		foreach( (array)$RefMVCParameterDefinition as $RefMVCParameter ) {
			//Event::Message('Parameter');
			//Event::Debug($RefMVCParameter);
			if( isset( $_REQUEST[$RefMVCParameter->getName()] ) ) {
				//Event::Message('In Request? -> Request to Parameter');
				array_push( $RefMVCParameterList, $_REQUEST[$RefMVCParameter->getName()] );
			} else if( in_array( $RefMVCParameter->getName(), array_keys( $MVCRoute->GetParameter() ) ) ) {
				//Event::Message('In Default? -> Default to Parameter');
				array_push( $RefMVCParameterList, $MVCRoute->GetParameter( $RefMVCParameter->getName() ) );
			} else {
				//Event::Message('Empty? -> push NULL');
				array_push( $RefMVCParameterList, null );
			}
		}
		//Event::Message('List:');
		//Event::Debug($RefMVCParameterList);
		/**
		 * Execute route
		 */
		return $MVCController->Execute( $MVCRoute->GetAction(), $RefMVCParameterList );
	}

	/**
	 * @static
	 * @return string
	 */
	public static function DirectoryController( $PathName = null ) {
		if( $PathName !== null ) {
			self::$BaseDirectoryController = $PathName;
		}
		return self::$BaseDirectoryController;
	}
	/**
	 * @static
	 * @return string
	 */
	public static function DirectoryApplication( $PathName = null ) {
		if( $PathName !== null ) {
			self::$BaseDirectoryApplication = $PathName;
		}
		return self::$BaseDirectoryApplication;
	}
	/**
	 * @static
	 * @return string
	 */
	public static function DirectoryModel( $PathName = null ) {
		if( $PathName !== null ) {
			self::$BaseDirectoryModel = $PathName;
		}
		return self::$BaseDirectoryModel;
	}
	/**
	 * @static
	 * @return string
	 */
	public static function DirectoryView( $PathName = null ) {
		if( $PathName !== null ) {
			self::$BaseDirectoryView = $PathName;
		}
		return self::$BaseDirectoryView;
	}

	/**
	 * @static
	 * @param null|string $RouteDefinition
	 * @return string
	 */
	public static function AccessDeniedRoute( $RouteDefinition = null ) {
		if( $RouteDefinition !== null ) {
			self::$AuthenticationMainContent = $RouteDefinition;
		}
		return self::$AuthenticationMainContent;
	}
	/**
	 * @static
	 * @param null|string $RouteDefinition
	 * @return string
	 */
	public static function AccessDeniedLink( $RouteDefinition = null ) {
		if( $RouteDefinition !== null ) {
			self::$AuthenticationPartialContent = $RouteDefinition;
		}
		return self::$AuthenticationPartialContent;
	}

	/**
	 * @static
	 * @param string $Namespace
	 * @param string $Class
	 * @param string $Table
	 * @return void
	 */
	public static function BuildModel( $Namespace, $Class, $Table ) {
		return MVCFactory::CreateModel( $Namespace, $Class, $Table );
	}
}
