<?php

	/*
	 * mime_content_type.php
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

	function_exists('finfo_open')
		or die('Unable to use mime_content_type replacement function');

	if (!function_exists('mime_content_type'))
	{
		function mime_content_type ($filename)
		{
			$finfo = finfo_open(FILEINFO_MIME);
			$mimetype = finfo_file($finfo, $filename);
			finfo_close($finfo);

			return $mimetype;
		}
	}
