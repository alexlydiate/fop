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

namespace Sop\Lib;

class Environment
{
	//@todo maybe - move these into some config file
	private $_cli;
	private $_domain;
	
	/**
	 * 
	 * @var \Sop\App\Model\User
	 */
	private $_user = null;
	
	protected static $instance = false;

	protected function __construct() {
		if(php_sapi_name() == 'cli') {
			$this->_cli = true;
			$this->_domain = null;
		} else {
			$this->_cli = false;
			$this->_domain = $_SERVER['SERVER_NAME'];
		}

		$sessionUserId = $this->getVar('userId');
		
		if ($sessionUserId  != null) {
			try {
				$this->loginUser(\Sop\App\Model\User::build_from_id((int)$sessionUserId));
			} catch (\Exception $e) {
				//@todo error
				die('Failed to log in session user: ' . $e->getMessage());
			}
		}
	}
	
	/**
	 * 
	 * @return \Sop\Lib\Environment
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new Environment();
		}
			
		return self::$instance;
	}
	
	/**
	 * Get the domain, as ascertained from the server
	 * @return String domain name
	 */	
	public function getDomain()
	{
		return $this->_domain;
	}
	
	/**
	 * Returns true if the request is via the CLI
	 * @return boolean
	 */
	public function isCLIRequest()
	{
		return $this->_cli;
	}
	
	/**
	 * 
	 * @param \Sop\App\Model\User $user
	 */
	public function loginUser($user) 
	{
		if ($user instanceof \Sop\App\Model\User) {
			$this->_user = $user;
			
			$this->setVar('userId', $this->_user->get_id());
		} else {
			throw new \Exception('loginUser requires an instance on \Sop\App\Model\User');
		}
	}
	
	/**
	 * Logs out current user
	 */
	public function logoutUser()
	{
		$this->destroyVar('userId');
		
		$this->_user = null;
	}
	/**
	 * @return \Sop\App\Model\User
	 */
	public function getUser()
	{
		return $this->_user;
	}
	
	/**
	 * Gets the value of the session variable if it is set, null otherwise
	 * @param unknown $key
	 * @return unknown|NULL
	 */
	public function getVar($key)
	{
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		} else {
			return null;
		}
	}
	
	/**
	 * Sets a session variable
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function setVar($key, $value)
	{
		$_SESSION[$key] = $value;
	}
	
	/**
	 * Unsets a session variable with the provided key
	 * @param unknown $key
	 */
	public function destroyVar($key)
	{
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
		}
	}
	
	/**
	 * Returns true if the environment has a user, false otherwise
	 * @return boolean
	 */
	public function hasUser() {
		if ($this->_user === null) {
			return false;
		} else {
			return true;
		}		
	}
}