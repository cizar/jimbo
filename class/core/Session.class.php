<?php

	/*
	 * core.Session
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

	class Session
	{
		// Properties
		
		var $_prefix = 'session';
		var $_admin = null;

		// Constructor
		
		function __construct ($prefix = null)
		{
			isset($_SESSION)
				or session_start();

			is_null($prefix)
				or $this->set_prefix($prefix);
		}

		// Public methods
		
		function destroy ()
		{
			session_destroy();
		}

		function get_session_id ()
		{
			return session_id();
		}

		function login ($who)
		{
			$this->set('user', $who);
		}

		function logout ()
		{
			$this->delete('user');
		}

		function is_anonymous ()
		{
			return !$this->is_logged();
		}

		function is_logged ()
		{
			return $this->exists('user');
		}

		function who_is ()
		{
			return $this->is_logged() ? $this->get('user') : null;
		}

		function is_admin ()
		{
			return $this->who_is() == $this->get_admin();
		}

		function get_prefix ()
		{
			return $this->_prefix;
		}
		
		function set_prefix ($value)
		{
			$this->_prefix = $value;
		}
		
		function get_admin ()
		{
			return $this->_admin;
		}
		
		function set_admin ($value)
		{
			$this->_admin = $value;
		}

		function get ($key)
		{
			if (!$this->exists($key))
				return null;

			return unserialize($_SESSION[$this->_k($key)]);
		}

		function set ($key, $value)
		{
			$_SESSION[$this->_k($key)] = serialize($value);
		}

		function delete ($key)
		{
			unset($_SESSION[$this->_k($key)]);
		}

		function exists ($key)
		{
			return array_key_exists($this->_k($key), $_SESSION);
		}

		function get_keys ()
		{
			$keys = array();
			foreach (array_keys($_SESSION) as $key)
				ereg('^' . $this->get_prefix() . '-.*', $key)
					and $keys[] = $key;
			return $keys;
		}

		function dump ()
		{
			printf
			(
				"<table><caption>Session %s</caption>",
				$this->get_session_id()
			);
			foreach($_SESSION as $key => $value)
			{
				printf
				(
					"<tr><th>%s</th><td>%s</td></tr>",
					$key,
					$value
				);
			}
			print("</table>");
		}
		
		// Private methods

		function _k ($key)
		{
			return sprintf('%s-%s', $this->get_prefix(), $key);
		}
	}

