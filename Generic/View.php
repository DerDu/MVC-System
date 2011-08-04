<?php
/**
 * MVC:View
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
 * @package MVCSystem\Generic
 */
namespace MVCSystem\Generic;

use \AIOSystem\Api\System;
use \AIOSystem\Api\Template;
use \AIOSystem\Api\Event;
use \AIOSystem\Api\Stack;
use \AIOSystem\Api\Cache;

abstract class View {
	/**
	 * Generic MVC-Function
	 *
	 * Used to return the view content
	 *
	 * @return string
	 */
	abstract public function Display();
	/** @var \AIOSystem\Core\ClassStackRegister $ContentRegister */
	private $ContentRegister = null;
	/** @var bool $ContentCache */
	public $ContentCache = null;

	public function __construct() {
		$this->ContentRegister = Stack::Register();
	}

	public function Set( $Mount, $Content ) {
		return $this->ContentRegister->setRegister( $Mount, $Content );
	}
	protected function Get( $Mount ) {
		return $this->ContentRegister->getRegister( $Mount );
	}

	public function CacheSet( $Timeout = 3600 ) {
		if( false === $this->CacheGet() ) {
			$CacheId = $this->CacheId();
			$this->ContentCache = $this->Display();
			Cache::Set( $CacheId, $this , get_class( $this ), false, $Timeout );
		}
	}
	/**
	 * @return bool|View
	 */
	public function CacheGet() {
		return Cache::Get( $this->CacheId(), get_class( $this ), false );
	}
	/**
	 * @return int
	 */
	public function CacheTime() {
		$Clear = clone $this;
		$Clear->ContentCache = null;
		return Cache::GetTime( $Clear->CacheId(), get_class( $this ), false );
	}
	/**
	 * @return string
	 */
	private function CacheId() {
		return sha1( serialize( $this ) );
	}

	protected function Template( $File, $ParsePhp = true, $ParsePhpAfterContent = false, $isTemplateContent = false ) {
		$File = $this->Location( $File );
		return Template::Load( $File, $ParsePhp, $ParsePhpAfterContent, $isTemplateContent );
	}
	private function Location( $File ) {
		$Location = explode( '\\', get_class( $this ) );
		array_pop( $Location );
		array_push( $Location, 'Template' );
		return realpath( System::DirectoryCurrent().DIRECTORY_SEPARATOR.implode( DIRECTORY_SEPARATOR, $Location ).DIRECTORY_SEPARATOR.$File );
	}
}
