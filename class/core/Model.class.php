<?php

	/*
	 * core.Model
	 *
	 * This file is a part of Jimbo package. http://www.nixar.org/jimbo
	 * 
	 * Copyright (c) 2007, Cesar Kastli <cesar@nixar.org>
	 *
	 * This library is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU Lesser General Public License as
	 * published by the Free Software Foundation; either version 2.1 of the
	 * License, or (at your option) any later version
	 *
	 * See http://www.gnu.org/copyleft/lesser.html for details
	 *
	 */

	import('core.Paginator');
	
	load_function('string/underscore');
	
	class Model
	{
		var $_table		= null;
		var $_pkey		= null;
		var $_id		= null;
		var $_fields	= array();
		var $_select	= '*';
		var $_where		= array();
		var $_orderby	= array();
		var $_limit		= null;
		var $_offset	= 0;
		var $_record	= array();

		// Constructor

		function __construct ($table = null)
		{
			$this->_table = is_null($table) ? $this->_guess_table_name() : $table;
			$this->_load_fields();
		}

		// Public Methods
		
		function get_table ()
		{
			return $this->_table;
		}
		
		function get_pkey ()
		{
			return $this->_pkey;
		}
		
		function is_new ()
		{
			return is_null($this->_id);
		}

		function reset_record ()
		{
			$this->_id = null;
			$this->_record	= array();
		}
		
		function get_id ()
		{
			return $this->_id;
		}
		
		function save ()
		{
			method_exists($this, 'before_save')
				and $this->before_save();

			$this->is_new() ? $this->insert() : $this->update(); 

			method_exists($this, 'after_save')
				and $this->after_save();
		}
		
		function insert ()
		{
			global $db;
			$db->insert($this->get_table(), $this->_record);
			$this->_id = $db->get_insert_id();
		}
		
		function update ()
		{
			global $db;
			$where	= sprintf('`%s` = "%s"', $this->get_pkey(), $this->get_id());
			$db->update($this->get_table(), $this->_record, $where);
		}
		
		function delete ()
		{
			global $db;
			$where	= sprintf('`%s` = "%s"', $this->get_pkey(), $this->get_id());
			$db->delete($this->get_table(), $where);
		}

		function begin ()
		{
			$this->query('BEGIN');
		}

		function commit ()
		{
			$this->query('COMMIT');
		}

		function rollback ()
		{
			$this->query('ROLLBACK');
		}
		
		function select ($fields)
		{
			$this->_select = $fields;
		}

		function where ()
		{
			if (func_num_args() == 0)
				throw new Exception('where: Wrong parameter count');

			$args = func_get_args();
			$cond = call_user_func_array('sprintf', $args);				

			$this->_where[] = $cond;
		}
		
		function where_field_equals ($field, $value)
		{
			global $db;
			
			$this->where
			(
				'`%s` %s %s',
					$field,
					is_null($value) ? 'is' : '=',
					$db->escape($value)
			);
		}

		function where_pkey_equals ($value)
		{
			$this->where_field_equals($this->get_pkey(), $value);
		}

		function orderby ($field, $dir = 'ASC', $prepend = false)
		{
			$dir = strtoupper($dir);
			in_array($dir, array('ASC', 'DESC'))
				or die('Direction must be ASC or DESC');
			
			$function = ($prepend) ? 'array_unshift' : 'array_push';
			$function($this->_orderby, sprintf('`%s` %s', $field, $dir));
		}
		
		function limit ($value, $offset = null)
		{
			if (!is_numeric($value))
				throw new Exception('Limit must be numeric');
				
			$this->_limit = $value;
			
			is_null($offset)
				or $this->offset($offset);
		}

		function offset ($value)
		{
			$this->_offset = $value;
		}
		
		function get_max_id ()
		{
			global $db;
			
			$result = $db->query('SELECT MAX(id) FROM `%s`', $this->_get_from());
	
			return $result->field(0);
		}
		
		function get_keys ()
		{
			$keys = array();
			foreach ($this->_fields as $field)
				$keys[] = $field['Field'];
			return $keys;
		}
		
		function get ()
		{
			return $this->_record;
		}
		
		function set ($record_or_key, $value = null)
		{
			$record = is_array($record_or_key) ? $record_or_key : array($record_or_key => $value);
			foreach ($record as $key => $value)
			{
				if (!in_array($key, $this->get_keys()))
					continue;

				if ($key == $this->get_pkey())
					$this->_id = $value;
				else
					$this->_record[$key] = $value;
			}
		}
		
		function query ()
		{
			if (func_num_args() == 0)
				throw new Exception('query: Wrong parameter count');

			global $db;
			$args = func_get_args();
			return call_user_func_array(array($db, 'query'), $args);		
		}
		
		function count_by_sql ($sql)
		{
			$result = $this->query($this->_get_count_query($sql));
			return $result->field(0);
		}
		
		function count ()
		{
			return $this->count_by_sql($this->_get_query());
		}
		
		function find_by_sql ($sql)
		{
			$result = $this->query($this->_get_find_query($sql));
			$this->_reset();
			return $result->to_array();
		}
		
		function find_first_by_sql ($sql)
		{
			$this->reset_record();
			$this->limit(1);
			$record = array_shift($this->find_by_sql($sql));
			$this->set($record);
			return $record;
		}

		function find_all ()
		{
			return $this->find_by_sql($this->_get_query());
		}
		
		function find_first ()
		{
			return $this->find_first_by_sql($this->_get_query());
		}
		
		function find_by_field ($field, $value)
		{
			$this->where_field_equals($field, $value);
			return $this->find_all();
		}
		
		function find_first_by_field ($field, $value)
		{
			$this->where_field_equals($field, $value);
			return $this->find_first();
		}
		
		function find_by_id ($id)
		{
			return $this->find_first_by_field($this->get_pkey(), $id);
		}
		
		function find_paginated_by_sql ($sql, $page, $per_page = 10)
		{
			$pag = new Paginator($per_page);
			$pag->set_records($this->count_by_sql($sql));
			$pag->go_to($page);
			
			$this->limit($pag->get_rpp(), $pag->get_offset());
			
			return array($pag, $this->find_by_sql($sql));
		}
		
		function find_paginated ($page, $per_page = 10)
		{
			return $this->find_paginated_by_sql($this->_get_query(), $page, $per_page);
		}

		function get_pairs ($field, $pkey = null)
		{
			is_null($pkey)
				and $pkey = $this->get_pkey();

			$pairs = array();
			foreach ($this->find_all() as $row)
			{
				$key = $row[$pkey];
				$pairs[$key] = $row[$field];
			}

			return $pairs;
		}

		// Private Methods

		function _reset ()
		{
			$this->_select	= '*';
			$this->_where	= array();
			$this->_orderby	= array();
			$this->_limit	= null;
			$this->_offset	= 0;
		}
		
		function _get_query ()
		{
			return sprintf
			(
				'SELECT %s FROM `%s` %s',
					$this->_get_select(),
					$this->_get_from(),
					$this->_get_where()
			);
		}
		
		function _get_count_query ($sql)
		{
			return sprintf
			(
				'SELECT COUNT(*) FROM (%s) AS __result__',
					$sql
			);
		}
		
		function _get_find_query ($sql)
		{
			return sprintf
			(
				'SELECT * FROM (%s) AS __result__ %s %s',
					$sql,
					$this->_get_orderby(),
					$this->_get_limit()
			);
		}
		
		function _get_select ()
		{
			return $this->_select;
		}

		function _get_from ()
		{
			return $this->get_table();	
		}
		
		function _get_where ()
		{
			if (empty($this->_where))
				return '';
			return 'WHERE ' . implode(' AND ', $this->_where);
		}
				
		function _get_orderby ()
		{
			if (empty($this->_orderby))
				return '';
			return 'ORDER BY ' . implode(', ', $this->_orderby);
		}
		
		function _get_limit ()
		{
			if (is_null($this->_limit))
				return '';
			
			return sprintf
			(
				'LIMIT %s OFFSET %s',
				$this->_limit,
				$this->_offset
			);
		}
		
		function _load_fields ()
		{
			global $db;
			$result = $db->describe($this->get_table());
			while ($field = $result->fetch())
			{
				if ($field['Key'] == 'PRI')
					$this->_pkey = $field['Field'];
				$this->_fields[] = $field;
			}
		}
		
		function _guess_table_name ()
		{
			return ereg('(.*)Model$', get_class($this), $regs) ? underscore($regs[1]) : null;
		}
	}
