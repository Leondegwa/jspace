<?php
/**
 * 
 * @package		JSpace
 * @subpackage	Repository
 * @copyright	Copyright (C) 2012 Wijiti Pty Ltd. All rights reserved.
 * @license     This file is part of the JSpace library for Joomla!.

   The JSpace library for Joomla! is free software: you can redistribute it 
   and/or modify it under the terms of the GNU General Public License as 
   published by the Free Software Foundation, either version 3 of the License, 
   or (at your option) any later version.

   The JSpace library for Joomla! is distributed in the hope that it will be 
   useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with the JSpace library for Joomla!.  If not, see 
   <http://www.gnu.org/licenses/>.

 * Contributors
 * Please feel free to add your name and email (optional) here if you have 
 * contributed any source code changes.
 * Name							Email
 * Michał Kocztorz				<michalkocztorz@wijiti.com> 
 * 
 */
 
defined('JPATH_PLATFORM') or die;


/**
 * @author Michał Kocztorz
 * @package     JSpace
 * @subpackage  OAI
 */
class JSpaceOAIRequestBadVerb extends JSpaceOAIRequest
{
	public function __construct( JInput $input, JSpaceOAIExceptionBadVerb $e ) {
		parent::__construct($input);
		$this->_error = $e;
	}
	

	/**
	 * Set the body in response xml.
	 */
	public function _setResponseBody() {
		//no body for error, method needed though
	}
}




