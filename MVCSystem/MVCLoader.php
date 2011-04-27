<?php
/**
 * MVC:Loader
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
 * @subpackage MVCLoader
 */
namespace MVCSystem;
/**
 * @package MVCSystem
 * @subpackage MVCLoader
 */
class MVCLoader {
	/**
	 * Register MVCLoader with Php-AutoLoad
	 *
	 * @static
	 * @return void
	 */
	public static function RegisterLoader() {
		$AutoLoader = spl_autoload_functions();
		if( $AutoLoader === false || empty( $AutoLoader ) || !in_array( array(__CLASS__,'ExecuteLoader'), $AutoLoader, true ) ) {
			spl_autoload_register( array(__CLASS__,'ExecuteLoader') );
		}
	}
	/**
	 * Requires the given class for MVC
	 * Called from MVCLoader (private)
	 *
	 * @static
	 * @param string $Class
	 * @return void
	 */
	public static function ExecuteLoader( $Class ) {
		$Class = str_replace( 'Class', '', $Class ).'.php';
		if( file_exists( $Class ) ) {
			//var_dump( 'MVC Load: '.$Class );
			require_once( $Class );
		}
	}
}