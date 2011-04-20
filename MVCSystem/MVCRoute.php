<?php
/**
 * MVC:Route
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
 * @subpackage MVCRoute
 */
namespace MVCSystem;
/**
 * @package MVCSystem
 * @subpackage MVCRoute
 */
class MVCRoute {
	/** @var string $optionUri URI Syntax */
	private $optionDefinition = '/{Controller}/{Action}';
	/** @var string $optionUriPattern URI RexExpr */
	private $optionPattern = '/.*?/.*?';
	/** @var string|null $optionUriRoute URI Request-Path */
	private $optionRoute = null;
	/** @var string $optionController Controller [Pattern/Name] */
	private $optionController = '{Controller}';
	/** @var string $optionAction Action [Pattern/Name] */
	private $optionAction = '{Action}';
	/** @var array $optionDefault Default parameter ($_REQUEST) */
	private $optionDefault = array();
	/**
	 * @param array $ParameterDefault
	 * @param array $ParameterRequirement
	 */
	public function __construct( $ParameterDefault = array() ) {
		$this->optionDefault = $ParameterDefault;
	}
	/**
	 * Check if pattern matches route
	 *
	 * @param string $Route
	 * @return false|MVCRoute
	 */
	public function IsMatch( $Route ) {
		if( preg_match( '!^'.$this->optionPattern.'$!is', $Route ) ) {
			$this->optionRoute( $Route );
			return $this;
		} return false;
	}
	/**
	 * Get controller name
	 *
	 * @return mixed|string
	 */
	public function GetController() {
		if( substr( $this->optionController(), 0, 1 ) == '{' ) {
			$Pattern = explode( '/', substr( $this->optionDefinition(), 0 , strpos( $this->optionDefinition(), $this->optionController() ) -1 ) );
			$Route = explode( '/', $this->optionRoute );
			return current( array_slice( $Route, count( $Pattern ) ) );
		} return $this->optionController();
	}
	/**
	 * Get action name
	 *
	 * @return mixed|string
	 */
	public function GetAction() {
		if( substr( $this->optionAction(), 0, 1 ) == '{' ) {
			$Pattern = explode( '/', substr( $this->optionDefinition(), 0 , strpos( $this->optionDefinition(), $this->optionAction() ) -1 ) );
			$Route = explode( '/', $this->optionRoute );
			return current( array_slice( $Route, count( $Pattern ) ) );
		} return $this->optionAction();
	}
	/**
	 * Get parameter array
	 *
	 * @param null $Name
	 * @return array|string
	 */
	public function GetParameter( $Name = null ) {
		/**
		 * Prepare data
		 */
		$Pattern = explode( '/', str_replace( '?', '', $this->optionDefinition() ) );
		$Route = explode( '/', $this->optionRoute );
		foreach( (array)$Pattern as $Index => $Key ) {
			if( ( strpos( $Key, '{' ) !== 0 || !isset( $Route[$Index] ) ) || $Key == $this->optionAction() || $Key == $this->optionController() ) {
				unset( $Pattern[$Index] );
				if( isset( $Route[$Index] ) ) {
					unset( $Route[$Index] );
				}
			} else {
				$Pattern[$Index] = str_replace( array( '{','}'), '', $Key );
			}
		}
		/**
		 * Add default values
		 */
		foreach( (array)$this->optionDefault as $Key => $Value ) {
			if( !in_array( $Key, $Pattern ) ) {
				array_push( $Pattern, $Key );
				array_push( $Route, $Value );
			}
		}
		/**
		 * Error
		 */
		if( empty( $Pattern ) || empty( $Route ) ) {
			if( $Name !== null ) {
				return $_REQUEST[$Name];
			} else {
				return $_REQUEST;
			}
		}
		/**
		 * Combine route with request
		 */
		$RouteParameter = array_combine( $Pattern, $Route );
		$RouteParameter = array_merge( $RouteParameter, $_REQUEST );
		if( $Name !== null ) {
			return $RouteParameter[$Name];
		} else {
			return $RouteParameter;
		}
	}
	/**
	 * Set/Get Definition
	 *
	 * @param null|string $Definition
	 * @return null|string
	 */
	public function optionDefinition( $Definition = null ) {
		if( $Definition !== null ) {
			$this->optionDefinition = $Definition;
			$this->optionPattern = preg_replace( array( '!{[^\?]*?}/!is', '!{[^\?]*?}!is' ), array( '[^/]*?/', '[^/]*?' ), $Definition );
			$this->optionPattern = preg_replace( array(  '!{\?.*?}/!is', '!{\?.*?}!is' ), array( '?[^/]*?/', '?[^/]*?' ), $this->optionPattern );
			$this->optionPattern .= '(\?|$)';
		} return $this->optionDefinition;
	}
	/**
	 * Set/Get Controller
	 *
	 * @param null|string $Controller
	 * @return null|string
	 */
	public function optionController( $Controller = null ) {
		if( $Controller !== null ) {
			$this->optionController = $Controller;
		} return $this->optionController;
	}
	/**
	 * Set/Get Action
	 *
	 * @param null|string $Action
	 * @return null|string
	 */
	public function optionAction( $Action = null ) {
		if( $Action !== null ) {
			$this->optionAction = $Action;
		} return $this->optionAction;
	}
	/**
	 * Set/Get Route
	 *
	 * @param null|string $Route
	 * @return null|string
	 */
	public function optionRoute( $Route = null ) {
		if( $Route !== null ) {
			$this->optionRoute = $Route;
		} return $this->optionRoute;
	}
}