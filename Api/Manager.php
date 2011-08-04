<?php
/**
 * This file contains the API:Route
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
 * @package MVCSystem\Api
 */
namespace MVCSystem\Api;

class Manager {
	/**
	 * Create new manager instance
	 *
	 * @static
	 * @param string $PathToModel
	 * @param string $PathToView
	 * @param string $PathToController
	 * @return \MVCSystem\Library\Manager
	 */
	public static function Instance( $PathToModel, $PathToView, $PathToController, $PathToApplication ) {
		return \MVCSystem\Library\Manager::Instance( $PathToModel, $PathToView, $PathToController, $PathToApplication );
	}
	/**
	 * Set path to library
	 *
	 * @static
	 * @param \MVCSystem\Library\Manager $Manager
	 * @param null|string $Path
	 * @return string
	 */
	public static function PathToModel( \MVCSystem\Library\Manager $Manager, $Path = null ) {
		return $Manager->PathToModel( $Path );
	}
	/**
	 * Set path to library
	 *
	 * @static
	 * @param \MVCSystem\Library\Manager $Manager
	 * @param null|string $Path
	 * @return string
	 */
	public static function PathToView( \MVCSystem\Library\Manager $Manager, $Path = null ) {
		return $Manager->PathToView( $Path );
	}
	/**
	 * Set path to library
	 *
	 * @static
	 * @param \MVCSystem\Library\Manager $Manager
	 * @param null|string $Path
	 * @return string
	 */
	public static function PathToController( \MVCSystem\Library\Manager $Manager, $Path = null ) {
		return $Manager->PathToController( $Path );
	}
	/**
	 * Set path to application
	 *
	 * @static
	 * @param \MVCSystem\Library\Manager $Manager
	 * @param null|string $Path
	 * @return string
	 */
	public static function PathToApplication( \MVCSystem\Library\Manager $Manager, $Path = null ) {
		return $Manager->PathToApplication( $Path );
	}
	/**
	 * Execute given route
	 *
	 * @static
	 * @param \MVCSystem\Library\Manager $Manager
	 * @param null|string $Route
	 * @return string
	 */
	public static function Execute( \MVCSystem\Library\Manager $Manager, $Route = null ) {
		return $Manager->Execute( $Route );
	}
}
