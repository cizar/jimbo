<?php

	/*
	 * cache_headers.php
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
	
	function cache_headers ($timestamp)
	{
		is_int($timestamp)
			or die('cache_headers: $timestamp must be numeric');

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $timestamp)
		{
			if (php_sapi_name() == 'CGI')
			{
				header('Status: 304 Not Modified');
			}
			else
			{
				header('HTTP/1.0 304 Not Modified');
			}
			exit;
		}
		else
		{
			$last_modified = gmdate('D, d M Y H:i:s\G\M\T', $timestamp);
			header('Last-Modified: ' . $last_modified);
			header('ETag: "' . md5($last_modified) . '"');
		}
	}
