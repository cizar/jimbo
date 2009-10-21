<?php

	/*
	 * get_mime_type.php
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

	function get_mime_type ($filename)
	{
		$info = pathinfo($filename);
		
		switch (strtolower($info['extension']))
		{
			case 'asf':		return 'video/x-ms-asf';
			case 'avi':		return 'video/x-msvideo';
			case 'exe':		return 'application/octet-stream';
			case 'mov':		return 'video/quicktime';
			case 'mp3':		return 'audio/mpeg';
			case 'mpg':		return 'video/mpeg';
			case 'mpeg':	return 'video/mpeg';
			case 'rar':		return 'encoding/x-compress';
			case 'txt':		return 'text/plain';
			case 'wav':		return 'audio/wav';
			case 'wma':		return 'audio/x-ms-wma';
			case 'doc':		return 'application/msword';
			case 'xls':		return 'application/vnd.ms-excel';
			case 'ppt':		return 'application/vnd.ms-powerpoint';
			case 'wmv':		return 'video/x-ms-wmv';
			case 'zip':		return 'application/zip';
			case 'csv':		return 'application/csv';
		}
		
		return 'unknown/unknown';
	}
