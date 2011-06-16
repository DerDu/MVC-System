<?php
/**
 * This file contains the API-Setup
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
 */
namespace MVCSystem;
use \AIOSystem\Api\System;
use \AIOSystem\Api\Event;
/**
 * @package MVCSystem
 */
class Api {
	const DEBUG = false;
	const API_PREFIX_NAMESPACE = __NAMESPACE__;
	/**
	 * Setup API
	 *
	 * This registers the \MVCSystem class auto load function
	 *
	 * @static
	 * @return void
	 */
	public static function Setup(){
		spl_autoload_register( array(__CLASS__,'AutoLoader') );
		require_once('MVCManager.php');
	}
	/**
	 * Load class files
	 *
	 * @static
	 * @param  string $Class
	 * @return bool
	 */
	public static function AutoLoader( $Class ) {
		$ClassMVC = System::DirectorySyntax( __DIR__.DIRECTORY_SEPARATOR.str_replace( self::API_PREFIX_NAMESPACE, '', $Class ).'.php', false, DIRECTORY_SEPARATOR );
		$ClassApplication = System::DirectorySyntax( MVCManager::DirectoryApplication().DIRECTORY_SEPARATOR.str_replace( self::API_PREFIX_NAMESPACE, '', $Class ).'.php', false, DIRECTORY_SEPARATOR );
		if( file_exists( $ClassMVC ) ) {
			require_once( $ClassMVC );
			if(self::DEBUG){Event::Message('Load['.__METHOD__.'] '.$ClassMVC.' -> OK',__FILE__,__LINE__);}
			return true;
		}
		else if( file_exists( $ClassApplication ) ) {
			require_once( $ClassApplication );
			if(self::DEBUG){Event::Message('Load['.__METHOD__.'] '.$ClassApplication.' -> OK',__FILE__,__LINE__);}
			return true;
		} else {
			if(self::DEBUG){Event::Message('Load['.__METHOD__.'] '.$ClassMVC.' -> FAILED',__FILE__,__LINE__);}
			if(self::DEBUG){Event::Message('Load['.__METHOD__.'] '.$ClassApplication.' -> FAILED',__FILE__,__LINE__);}
			return false;
		}
	}
}
/**
 * Setup API (auto)
 */
Api::Setup();
