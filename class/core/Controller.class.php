<?php

	/*
	 * core.Controller
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
	
	load_function('deep/htmlentities_deep', 'tpl', 'tag', 'get_mime_type');

	class Controller
	{
		var $_controller = 'controller';
		var $_action = 'index';
		var $_params = array();
		var $_custom_view = null;
		var $_view_path = null;
		var $_before_filters = array();
		var $_after_filters = array();
		
		public function __construct ()
		{
			// Nothing
		}
		
		public function set_controller ($value)
		{
			is_string($value)
				or die('set_controller: type mismatch');

			$this->_controller = $value;
		}
		
		public function set_action ($value)
		{
			is_string($value)
				or die('set_action: type mismatch');

			$this->_action = $value;
		}
		
		public function set_params ($value)
		{
			is_array($value)
				or die('set_params: type mismatch');

			$this->_params = $value;
		}
		
		public function set_custom_view ($value)
		{
			is_string($value)
				or die('set_custom_view: type mismatch');

			$this->_custom_view = $value;
		}
		
		public function set_view_path ($value)
		{
			is_string($value)
				or die('set_view_path: Type missmatch');
				
			$this->_view_path = $value;
		}
		
		// Before filter
		
		public function prepend_before_filters ()
		{
			if (func_num_args() == 0)
				die('prepend_before_filters: Wrong parameter count');

			foreach(func_get_args() as $filter)
				array_unshift($this->_before_filters, $filter);
		}

		public function append_before_filters ()
		{
			if (func_num_args() == 0)
				die('append_before_filters: Wrong parameter count');

			foreach (func_get_args() as $filter)
				array_push($this->_before_filters, $filter);
		}
		
		public function call_before_filters ()
		{
			foreach ($this->_before_filters as $method)
				$this->$method();
		}

		// After filter
		
		public function prepend_after_filters ()
		{
			if (func_num_args() == 0)
				die('prepend_after_filters: Wrong parameter count');

			foreach (func_get_args() as $filter)
				array_unshift($this->_after_filters, $filter);
		}
		
		public function append_after_filters ()
		{
			if (func_num_args() == 0)
				die('append_after_filters: Wrong parameter count');
			
			foreach (func_get_args() as $filter)
				array_push($this->_after_filters, $filter);
		}

		public function call_after_filters ()
		{
			foreach ($this->_after_filters as $method)
				$this->$method();
		}
		
		public function execute ()
		{
			$this->call_before_filters();

			method_exists($this, $this->_action)
				and call_user_func_array(array(&$this, $this->_action), $this->_params);

			$this->call_after_filters();

			$view = is_null($this->_custom_view) ? $this->_action : $this->_custom_view;

			$this->render_tpl($this->_view_path . DS . $this->_controller . DS . $view);
		}

		// Render
		
		public function render_file ($file, $disposition = 'inline')
		{
			file_exists($file)
				or die('File not exists');
				
			in_array($disposition, array('inline', 'attachment'))
				or die('render_file: Unknown disposition ' . $disposition);
				
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Disposition: ' . $disposition . '; Filename="' . basename($file) . '"');
			header('Content-Length: ' . filesize($file));
			header('Content-Type: ' . get_mime_type($file));

			readfile($file);

			exit;
		}
		
		public function render_tpl ($tpl)
		{
			echo tpl($tpl, $this->_get_public_vars_assoc());

			exit;
		}
		
		public function render_text ()
		{
			if (func_num_args() == 0)
				die('render_text: Wrong parameter count');
			
			$args = func_get_args();
			$text = call_user_func_array('sprintf', $args);

			echo $text;

			exit;
		}
		
		// Redirect & Nothing
		
		public function redirect ($url)
		{
			if (!ereg('^/', $url) and !ereg('^http://', $url))
				$url = PAGE_URL . '/' . $url;

			header("Location: $url");
			
			exit;
		}
		
		public function nothing ()
		{
			exit;
		}
		
		// Private methods

		function _get_public_vars_assoc ()
		{
			$vars = array();
			foreach (get_object_vars($this) as $key => $value)
			{
				ereg('^_', $key)
					or $vars[$key] = is_object($value) ? $value : htmlentities_deep($value);
			}
			return $vars;
		}
	}
