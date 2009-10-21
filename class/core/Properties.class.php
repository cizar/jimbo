<?php
	
	/*
	 * core.Properties
	 *
	 * This file is a part of Jimbo package. http://www.nixar.org/jimbo
	 *
	 * Copyright (c) 2005, Cesar Kastli <cesar@nixar.org>
	 *
	 * This library is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU Lesser General Public License as
	 * published by the Free Software Foundation; either version 2.1 of the
	 * License, or (at your option) any later version
	 *
	 * See http://www.gnu.org/copyleft/lesser.html for details
	 *
	 */

	class Properties
	{
		var $_p;
		
		function __construct ($p = null)
		{
			$this->reset();
			isset($p) and $this->set_properties($p);
		}
		
		function reset ()
		{
			$this->_p = array();
		}
	
		function get_properties ()
		{
			return $this->_p;
		}
		
		function set_properties ($p)
		{
			foreach (is_object($p) ? $p->get_properties() : $p as $key => $value)
			{
				$this->set($key, $value);
			}
		}
	
		function get ($key)
		{
			if ($this->exists($key))
				return $this->_p[$key];
	
			return null;
		}
	
		function set ($key, $value)
		{
			$this->_p[$key] = $value;
		}
		
		function delete ($key)
		{
			if ($this->exists($key))
				unset($this->_p[$key]);
		}
		
		function exists ($key)
		{
			return array_key_exists($key, $this->_p);
		}
	
		function get_keys ()
		{
			return array_keys($this->_p);
		}
		
		function count ()
		{
			return count($this->_p);
		}
	}
