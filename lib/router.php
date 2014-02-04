<?php
/**
 * This file is part of the Framework of Paradise (FoP).
 * FoP is free software: you can redistribute it and/or modify
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

namespace Sop\Lib;

class Router {
	
	private $_env;
	
	private $_route = array();
	
	private $_args = array();
	
	protected $_staticRoutes;
	
	public function __construct() {
		$this->_env = \Sop\Lib\Environment::getInstance();
		
		$this->_staticRoutes = include ROOTDIR . '/app/config/routes.php';

		if ($this->_env->isCLIRequest()) {
			$this->_cliSetRoute();
		} else {
			$this->_httpSetRoute();
		}
	}
	
	/**
	 * Do the do
	 */
	public function route()
	{	
		$controllerName = "Sop\\App\\Controller\\" . ucfirst($this->_route[0]);
		
		if ( ! class_exists($controllerName)) {
			$this->_controllerNotFound($controllerName);
		} else {			
			$controller = new $controllerName;
			
			$methodName = $this->_route[1];
			
			array_shift($this->_route);
			array_shift($this->_route);
			
			if ( ! method_exists($controller, $methodName)) {
				$this->_methodNotFound($controllerName, $methodName);
			}		
			
			call_user_func_array(array($controller, $methodName), array_merge($this->_route, $this->_args));
		}
	}
	
	/**
	 * Handle a request in which the controller doesn't exist
	 * @param string $controllerName
	 * @throws \ErrorException
	 */
	protected function _controllerNotFound($controllerName) 
	{
		if ($this->_env->isCLIRequest()) {
			throw new \ErrorException('Controller ' . $controllerName . ' does not exist');
		}
		
		$this->_fourOhFour();
	}
	
	/**
	 * Handle a request in which the method does not exist in the controller
	 * @param string $methodName
	 * @param string $controllerName
	 * @throws \ErrorException
	 */
	protected function _methodNotFound($methodName, $controllerName) 
	{
		if ($this->_env->isCLIRequest()) {
			throw new \ErrorException('Method ' . $methodName . ' does not exist in ' . $controllerName);
		}
		
		$this->_fourOhFour();
	}
	
	/**
	 * Chuck a 404
	 */
	protected function _fourOhFour() {
		header("HTTP/1.0 404 Not Found");
		
		if (isset($this->_staticRoutes['404']) && ! empty($this->_staticRoutes['404'])) {
			$this->_route = explode('/', $this->_staticRoutes['404']);
			array_shift($this->_route);
			$this->route();
		} else {
			//@todo This, properly
			exit('Nothing here - 404');
		}
		
	}
	
	/**
	 * Set route from a CLI request
	 */
	protected function _cliSetRoute()
	{
		$this->_route = $_SERVER['argv'];
		
		array_shift($this->_route);
	}
	
	/**
	 * Set route from a HTTP request
	 */
	protected function _httpSetRoute()
	{
		$routeArray = null;
		
		foreach ($this->_staticRoutes as $uri => $route) {
			
			$baseUri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
			
			$escapedUriPat = '/^' . str_replace('/', '\/', $uri) . '/';
			if ($uri === '/') {
				if ($_SERVER['REQUEST_URI'] === '/') {
					//You cant pass arguments without a resource
					$routeArray = explode('/', $route);
					break;
				} else {
					continue;
				}
			} elseif (preg_match('/^' . str_replace('/', '\/', $uri) . '/', $_SERVER['REQUEST_URI'])) {
				$routeArray = explode('/', preg_replace('/^' . str_replace('/', '\/', $uri) . '/', $route, $_SERVER['REQUEST_URI']));
				break;
			}
		}
		
		if ( ! is_array($routeArray)) {
			$routeArray = explode('/', $_SERVER['REQUEST_URI']);
			
			$end = end($routeArray);
			
			if (preg_match("/\?/", $end)) { //get them params into this->_args
				parse_str(substr($end, strpos($end,"?") + 1, strlen($end)), $this->_args);
				$method = substr($end, 0, strpos($end,"?"));
				
				array_pop($routeArray);
				$routeArray[] = $method;				
			}			
		}
		
		array_shift($routeArray);
		
		$this->_route = $routeArray;		
	}
}