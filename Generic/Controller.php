<?php
/**
 * MVC:Controller
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
/**
 * Example:
 *
 * <code>
 * namespace Application\Controller
 *
 * class ExampleClass extends \MVCSystem\Generic\Controller {
 *      public function ExampleMethod( $ExampleParameter ) {
 *          $ExampleView = new \Application\View\ExampleView();
 *          $ExampleView->Set( $ExampleView::MOUNT_POINT_EXAMPLE, $ExampleParameter );
 *          return $ExampleView;
 *      }
 * }
 *
 * </code>
 * <code>
 * namespace Application\View
 * class ExampleClass extends \MVCSystem\Generic\View {
 *      const MOUNT_POINT_EXAMPLE = 'ExamplePlaceholder';
 *
 *      public function Display() {
 *          $Template = $this->Template( 'ExampleTemplate.tpl' );
 *          $Template->Assign( self::MOUNT_POINT_EXAMPLE, $this->Get( self::MOUNT_POINT_EXAMPLE ) );
 *          return $Template->Parse();
 *      }
 * }
 * </code>
 *
 */
class Controller {
	/**
	 * Generic MVC-Function
	 *
	 * Used to "call" a controller method with arguments
	 *
	 * @param string $Action
	 * @param array $Parameter
	 * @return mixed
	 */
	public function Call( $Action, $Parameter ) {
		return call_user_func_array( array( $this, $Action ), $Parameter );
	}
}
