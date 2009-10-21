<?php

	/*
	 * core.Result
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

	class Result
	{
		// Properties
		
		var $_result;
		var $_fields_types;
		
		// Constructor
		
		function __construct (&$result)
		{
			$this->_result =& $result;
			//$this->_load_fields_types();
		}

		// Public methods

		function fetch ()
		{
			return mysql_fetch_assoc($this->_result);
			/*
			$row = array();
			if ($fields = mysql_fetch_assoc($this->_result))
				foreach ($fields as $key => $value)
					$row[$key] = $this->_cast($value, $this->_fields_types[$key]);
			return $row;
			*/
		}
		
		function to_array ()
		{
			$array = array();
			while ($row = $this->fetch())
				$array[] = $row;
			return $array;
		}

		function field ($field = 0, $row = 0)
		{
			return mysql_result($this->_result, $row, $field);
		}
		
		function get_size ()
		{
			return mysql_num_rows($this->_result);
		}

		function reset ()
		{
			return mysql_data_seek($this->_result, 0);
		}

		function free ()
		{
			mysql_free_result($this->_result)
				or die ("Unable to free result: " . mysql_error ());
		}
		
		function ts_to_unix ($ts)
		{
			$year  = substr($ts, 0, 4);
			$month = substr($ts, 4, 2);
			$day   = substr($ts, 6, 2);
			$hour  = substr($ts, 8, 2);
			$min   = substr($ts, 10, 2);
			$sec   = substr($ts, 12, 2);

			return mktime($hour, $min, $sec, $month, $day, $year);			
		}
		
		function dump ()
		{
			echo '<table border="1">';

			echo '<tr>';
			while ($field = mysql_fetch_field($this->_result))
				echo '<th>' . $field->name . '</th>';
			echo '</tr>';
			
			if ($this->get_size() > 0)
			{
				while ($row = $this->fetch())
				{
					echo '<tr>';
					foreach ($row as $field => $value)
						echo '<td>' . (is_null($value) ? 'NULL' : $value) . '</td>';
					echo '</tr>';
				}
				$this->reset();
			}

			echo '</table>';
		}
		
		// Private Methods
		
		function _cast ($value, $type)
		{
			if (is_null($value))
				return null;
		
			switch ($type)
			{
				case 'int': return intval($value);
				case 'string': return strval($value);
				case 'real': return floatval($value);
				//case 'timestamp': return $this->ts_to_unix($value);
			}
			
			return $value;
		}
		
		function _load_fields_types ()
		{
			is_null($this->_result)
				and die('Que?');
				
			for ($i = 0; $i < mysql_num_fields($this->_result); $i++)
			{
				$name = mysql_field_name($this->_result, $i);
				$type = mysql_field_type($this->_result, $i);
				$_fields_types[$name] = $type;
			}			
		}
	}
