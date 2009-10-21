<?php

	/*
	 * core.Dispatcher
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
	
	load_function('string/upper_camel_case');	
	
	class Dispatcher
	{
		var $_controller = 'controller';
		var $_controller_package = 'controller';
		var $_action = 'index';
		var $_action_params = array();
		var $_views_path = null;
		var $_controllers = array();
		
		// Constructor

		function __construct ()
		{
			// Nothing
		}

		// Public methods

		function get_controller_class_fqcn ()
		{
			return array_key_exists($this->_controller, $this->_controllers)
				? $this->_controllers[$this->_controller]
				: sprintf('%s.%sController', $this->_controller_package, upper_camel_case($this->_controller));
		}		

		function load_config ($file)
		{
			$ini = @parse_ini_file($file, true)
				or die('Dispatcher::load_config() file not exist');

			if (array_key_exists('config', $ini) && $config = $ini['config'])
			{
				array_key_exists('default_controller', $config)
					and $this->_controller = $config['default_controller'];

				array_key_exists('default_controller_package', $config)
					and $this->_controller_package = $config['default_controller_package'];

				array_key_exists('views_path', $config)
					and $this->_views_path = preg_replace('/[\/\\\]/', DS, $config['views_path']);
			}

			array_key_exists('controllers', $ini)
				and	$this->_controllers = $ini['controllers'];
		}
				
		function dispatch ($url = null)
		{
			is_null($url)
				or $this->_recognize_route($url);

			$controller	= new_instance($this->get_controller_class_fqcn());
				
			$controller->set_controller($this->_controller);
			$controller->set_action($this->_action);
			$controller->set_params($this->_action_params);
			$controller->set_view_path($this->_views_path);
			
			$controller->execute();
		}
		
		// Private methods

		function _recognize_route ($url)
		{
			is_string($url)
				or die('Dispatcher::set_resource: type mismatch');

			ereg('^/', $url)
				and $url = substr($url, 1, strlen($url));

			$r = explode('/', $url, 3);

			$aux = array_shift($r)
				and $this->_controller = $aux;

			$aux = array_shift($r)
				and $this->_action = $aux;

			$aux = array_shift($r)
				and $this->_action_params = explode('/', $aux);
		}
	}
