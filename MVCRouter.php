<?php
/**
 * MVC:Router
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
 * @subpackage MVCRouter
 */
namespace MVCSystem;
use \AIOSystem\Api\Stack;
use \AIOSystem\Api\Seo;
use \AIOSystem\Api\Xml;
use \AIOSystem\Api\System;
/**
 * @package MVCSystem
 * @subpackage MVCRouter
 */
class MVCRouter {
	/** @var \AIOSystem\Core\ClassStackRegister $RouterStack */
	private static $RouterStack = null;
	/** @var string $NoMatchController */
	private static $NoMatchController = 'Controller\MVCError';
	/** @var string $NoMatchAction */
	private static $NoMatchAction = 'Display';

	public static function Boot( $ConfigurationFile = null ) {
		if( $ConfigurationFile === null ) {
			$ConfigurationFile = __DIR__.DIRECTORY_SEPARATOR.'MVCRouter.xml';
		}
		if( file_exists( $ConfigurationFile ) ) {
			$Configuration = System::File( $ConfigurationFile );
			$MVCRouterConfiguration = Xml::Parser( $Configuration->propertyFileLocation() )->groupXmlNode('MVCRoute');
			/** @var \AIOSystem\Core\ClassXmlNode $MVCRoute */
			foreach( (array)$MVCRouterConfiguration as $MVCRoute ) {
				$Definition = trim( $MVCRoute->searchXmlNode('Definition')->propertyContent() );
				$Controller = trim( $MVCRoute->searchXmlNode('Controller')->propertyContent() );
				$Action = trim( $MVCRoute->searchXmlNode('Action')->propertyContent() );
				$ParameterList = $MVCRoute->propertyChildList();
				/** @var \AIOSystem\Core\ClassXmlNode $Parameter */
				$DefaultParameter = array();
				foreach( (array)$ParameterList as $Parameter ) {
					$DefaultParameter[$Parameter->propertyName()] = trim($Parameter->propertyContent());
				}
				$Restricted = ($MVCRoute->propertyAttribute('Restricted')?true:false);
				$Route = MVCManager::RegisterRoute( $Definition, $Controller, $Action, $DefaultParameter, $Restricted );
				$Route->optionSource( basename( $Configuration->propertyFilePath() ) );
			}
		} else {
			throw new \Exception('Router configuration file not available!');
		}
	}

	/**
	 * Fetch associated route
	 *
	 * @static
	 * @param null|string $RoutePath
	 * @return MVCRoute
	 */
	public static function Route( $RoutePath = null ) {
		/**
		 * Route data
		 */
		if( $RoutePath === null ) {
			Seo::Request();
			$RoutePath = $_REQUEST['URI-PATH'];
		}
		/**
		 * Search for route
		 */
		$RouterStack = self::$RouterStack->listRegister();
		/** @var MVCRoute $Route */
		foreach( (array)$RouterStack as $Route ) {
			if( $Route->IsMatch( $RoutePath ) ) {
				return $Route;
			}
		}
		/**
		 * Route not found
		 */
		return self::NoRoute( $RoutePath );
	}
	/**
	 * @param string $RoutePath
	 * @return MVCRoute
	 */
	public static function NoRoute( $RoutePath ) {
		$NoMatchRoute = new MVCRoute( array( 'MVCErrorType'=>404,'MVCErrorInformation'=>$RoutePath ) );
		$NoMatchRoute->optionDefinition('');
		$NoMatchRoute->optionRoute('');
		$NoMatchRoute->optionController( self::$NoMatchController );
		$NoMatchRoute->optionAction( self::$NoMatchAction );
		return $NoMatchRoute;
	}
	/**
	 * Define and connect new route
	 *
	 * @static
	 * @throws \Exception
	 * @param string $Pattern
	 * @param string $Controller
	 * @param string $Action
	 * @param array $ParameterDefault
	 * @return MVCRoute
	 */
	public static function Register( $Pattern, $Controller = '{Controller}', $Action = '{Action}', $ParameterDefault = array(), $RestrictedAccess = false ) {
		/**
		 * Prepare router stack
		 */
		if( self::$RouterStack === null ) {
			self::$RouterStack = Stack::Register();
		}
		/**
		 * Define route
		 */
		$ClassMVCRoute = new MVCRoute(
			$ParameterDefault
		);
		$ClassMVCRoute->optionDefinition( $Pattern );
		$ClassMVCRoute->optionController( $Controller );
		$ClassMVCRoute->optionAction( $Action );
		$ClassMVCRoute->optionRestricted( $RestrictedAccess );
		/**
		 * Connect route
		 */
		/** @var MVCRoute $ClassMVCRouteCheck */
		if( null === ( $ClassMVCRouteCheck = self::$RouterStack->getRegister( $ClassMVCRoute->optionDefinition() ) ) ) {
			self::$RouterStack->setRegister( $ClassMVCRoute->optionDefinition(), $ClassMVCRoute );
		} else {
			throw new \Exception('Route already exists! '.$ClassMVCRouteCheck->optionDefinition().' >> '.$ClassMVCRouteCheck->optionController().' >> '.$ClassMVCRouteCheck->optionAction() );
		}
		return $ClassMVCRoute;
	}
}