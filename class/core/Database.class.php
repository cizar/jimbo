<?php

	/*
	 * core.Database
	 *
	 * This file is a part of Jimbo package. http://www.nixar.org/jimbo
	 * 
	 * Copyright (c) 2006, Cesar Kastli <cesar@nixar.org>
	 *
	 * This library is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU Lesser General Public License as
	 * published by the Free Software Foundation; either version 2.1 of the
	 * License, or (at your option) any later version
	 *
	 * See http://www.gnu.org/copyleft/lesser.html for details
	 *
	 */

	import('core.Result');

	class Database
	{
		// Properties
		
		private static $_instance = null;

		private $_dbh = null;

		// Constructor
		
		private function __construct ()
		{
			// Nothing
		}

		public static function get_instance ()
		{
			if (self::$_instance == null)
			{
				self::$_instance = new Database();
			}
			return self::$_instance;
		}

		// Public methods

		public function connect ($server, $username, $password, $new_link = false)
		{
			if (!$this->_dbh = @mysql_connect($server, $username, $password, $new_link))
				throw new Exception('Unable to connect to database: ' . mysql_error());
		}

		public function close ()
		{
			if (!@mysql_close($this->_dbh))
				throw new Exception('Unable disconnect: ' . mysql_error());
		}

		public function select_db ($db_name)
		{
			if (!@mysql_select_db($db_name, $this->_dbh))
				throw new Exception('Unable to select database "' . $db_name. '": ' . mysql_error());
		}
		
		public function begin ()
		{
			$this->query('BEGIN');
		}

		public function commit ()
		{
			$this->query('COMMIT');
		}

		public function rollback ()
		{
			$this->query('ROLLBACK');
		}

		public function get_affected_rows ()
		{
			return mysql_affected_rows($this->_dbh);
		}

		public function get_insert_id ()
		{
			return mysql_insert_id($this->_dbh);
		}

		public function query ()
		{
			if (func_num_args() == 0)
				die('query: Wrong parameter count');
			
			$args = func_get_args();
			$query = call_user_func_array('sprintf', $args);
			
			if (!$res = @mysql_query($query, $this->_dbh))
				throw new Exception('Error performing query [' . $query . '] ' . mysql_error());
				
			return is_resource($res) ? new Result($res) : null;
		}

		public function insert ($table, $values)
		{
			$this->query('INSERT INTO `%s` SET %s', $table, $this->_implode_values($values));
		}
		
		public function update ($table, $values, $where = '1 = 1')
		{
			$this->query('UPDATE `%s` SET %s WHERE %s', $table, $this->_implode_values($values), $where);
		}

		public function delete ($table, $where = '1 = 1')
		{
			$this->query('DELETE FROM `%s` WHERE %s', $table, $where);
		}
		
		public function truncate ($table)
		{
			$this->query('TRUNCATE TABLE `%s`', $table);
		}

		public function describe ($table)
		{
			return $this->query('DESCRIBE `%s`', $table);
		}

		public function get_tables ()
		{
			return $this->query('SHOW TABLES');
		}

		public function escape ($value)
		{
			switch (gettype($value))
			{
				case 'NULL'		: return 'NULL';
				case 'string'	: return $this->escape_string($value);
				case 'boolean'	: return $value ? 1 : 0;
			}		
	
			return $value;
		}
		
		public function escape_string ($value)
		{
			if (eregi("^[A-Za-z_]*\(\)$", $value))
				return $value;

			return "'" . mysql_real_escape_string($value) . "'";
		}

		// Private methods

		function _implode_values ($values)
		{
			$aux = array();
			foreach ($values as $key => $value)
			{
				$aux[] = sprintf('`%s` = %s', $key, $this->escape($value));
			}
			return implode(',', $aux);			
		}
	}
