<?php

	/*
	 * core.Template
	 *
	 * This file is a part of Jimbo package. http://www.nixar.org/jimbo
	 *
	 * A Brian's code variation written by Cesar Kastli <cesar@nixar.org>
	 *
	 * http://www.massassi.com/php/articles/template_engines/
	 *
	 * Copyright (c) 2003, Brian E. Lozier <brian@massassi.net>
	 *
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your opinion) any later version.
	 *
	 * See http://www.gnu.org/copyleft/gpl.html for details
	 *
	 */

	class Template
	{
		var $_file;
		var $_vars;

		function __construct ($file)
		{
			$this->_file = $file;
			$this->clear();
		}

		function clear ()
		{
			$_vars = array();
		}

		function set ($name, $value)
		{
			$this->_vars[$name] = is_object($value) ? $value->parse() : $value;
		}

		function parse ()
		{
			is_null($this->_vars) or extract($this->_vars);
			ob_start();
			include($this->_file);
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		}

		function dump ()
		{
			print($this->parse());
		}
	}
