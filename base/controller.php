<?php
/**
 * This file is part of the Framework of Paradise (FoP).
 * FOP is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FoP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FOP.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package FoP
 * @author Alex Lydiate <alexlydiate@gmail.com>
 * @copyright  Copyright (c) 2014, Alex Lydiate
 * @license http://www.gnu.org/licenses/gpl.html
 */

namespace Fop\Base;

abstract class Controller {
	
	/**
	 * 
	 * @var \Fop\Lib\Environment
	 */
	protected $_env;
	
	/**
	 * Array of variables to be made available to templates
	 * @var array
	 */
	protected $_templateVars = array();
	
	public function __construct() 
	{
		$this->_env = \Sop\Lib\Environment::getInstance();
	}
	
	/**
	 * Loads the given template
	 * @param string $template
	 */
	protected function _view($template) {
		extract($this->_templateVars);
		include(ROOTDIR . '/app/views/' . $template . '.php');
	}
	
	/**
	 * Logs a message, by default to stout
	 * @todo in fact, only to stout, so others to be added, especially 'file'
	 * $type is left open for the specific app to define - might decide to be 'message', 'warning', 'error', for example
	 * @todo This could be enforced in a config, perhaps...
	 * @param string $message
	 * @param string $type
	 * @param string $stout
	 * @param string $target
	 */
	protected function _log($message, $type, $stout = true, $target = null) {
		$now = new \DateTime();
		
		switch ($target) {
			case 'file':
			break;
			default:
			break;
		}
		
		echo $now->format("Y-m-d H:i:s") . ' - ' . $type . ' - ' . $message . "\n";
	}
	
	/**
	 * Redirect!
	 * @param string $uri
	 * @param string $method
	 * @param number $http_response_code
	 */
	protected function _redirect($uri = '', $method = 'location', $http_response_code = 302)
	{
		$url = 'http://' . $this->_env->getDomain() . '/' . $uri;

		switch($method)
		{
			case 'refresh': header("Refresh:0;url=" . $uri);
				break;
			default: header("Location: " . $url, TRUE, $http_response_code);
				break;
		}
		
		exit;
	}
}