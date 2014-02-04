<?php
namespace Sop\Lib;
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
class Database
{
	/**
	 * @var \PDO $pdo
	 */
	public $pdo;
	/**
	 * The singleton instance
	 * @var unknown
	 */
	protected static $instance = false;

	protected function __construct() {
		$this->connect();
		$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	}
	
	/**
	 * Gets the singleton instances - if it doesn't exist, instantiates it
	 * @return \Sop\Lib\Database
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new Database();
		}
			
		return self::$instance;
	}

	/**
	 * Connects to the DB
	 */
	protected function connect(){
		$this->pdo = new \PDO("mysql:host=" . DBHOST . ";dbname=" . DBNAME, DBUSER, DBPASS);
	}
	
	/**
	 * Fetch all results from the given query - wraps a PDO prepared statement
	 * @param string $sql
	 * @param array $params
	 * @param int $style as defined by PDO constants
	 * @return multitype: results
	 */
	public function fetchAll($sql, $params = array(), $style = \PDO::FETCH_ASSOC)
	{
		$sth = $this->execute($sql, $params);
		return $sth->fetchAll($style);
	}
	
	/**
	 * Fetch a single result from the given query - wraps a PDO prepared statement
	 * @param string $sql
	 * @param array $params
	 * @param int $style as defined by PDO constants
	 * @return multitype: results
	 */
	public function fetch($sql, $params = array(), $style = \PDO::FETCH_ASSOC)
	{
		$sth = $this->execute($sql, $params);		
		return $sth->fetch($style);
	}
	
	/**
	 * Executes the given query - wraps a PDO prepared statement
	 * @param string $sql
	 * @param array $params
	 */
	public function execute($sql, $params = array()) {
		$sth = $this->pdo->prepare($sql);			
		$sth->execute($params);
		
		return $sth;
	}
	
	/**
	 * Executes the given insert query and returns the last insert id - wraps a PDO prepared statement
	 * @param string $sql
	 * @param array $params
	 */
	public function insert($sql, $params = array())
	{
		$this->execute($sql, $params);
		return $this->pdo->lastInsertId();
	}
}