<?php

	/*
	 * boot.php
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

	 //($_SERVER['REMOTE_ADDR'] == "200.61.185.209")
	 //	or die('Disculpe las molestias, en mantenimiento');

	defined('APP_ROOT')
		or die('No direct execution allowed');

	// Separators aliases
	
	define('DS', DIRECTORY_SEPARATOR);
	define('PS', PATH_SEPARATOR);
	
	// Framework paths
	
	define('SYS_ROOT', dirname(__FILE__));
	define('CLASS_DIR', 'class');
	define('LIB_DIR', 'lib');
	define('TPL_DIR', 'tpl');

	// Include path
		
	set_include_path(APP_ROOT . PS . SYS_ROOT . PS . get_include_path());

	// Application settings
	
	@(include('conf' . DS . 'settings.php'))
		or die('boot: settings file not found');

	// Include the function loader, class loader & common classes
	
	@(include(LIB_DIR . DS . 'load_function.php'))
		or die('boot: load_function not found');

	load_function('import', 'new_instance');

	import('core.Database');

	// Initialize the database
	
	try
	{
		global $db;
		$db = Database::get_instance();
		$db->connect(DB_HOST, DB_USER, DB_PASS);
		$db->select_db(DB_NAME);
	}
	catch (Exception $e)
	{
		die('boot: Can not connect to database');
	}
