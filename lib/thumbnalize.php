<?php

	/*
	 * thumbnalize.php
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
	
	function thumbnalize ($source_file, $max_width, $max_height, $thumb_file = null)
	{
		is_null($thumb_file)
			and $thumb_file = $source_file;

		list($w, $h, $t, $a) = getimagesize($source_file);
		
		switch ($t)
		{
			case 1: $source = imagecreatefromgif($source_file); break;
			case 2: $source = imagecreatefromjpeg($source_file);  break;
			case 3: $source = imagecreatefrompng($source_file ); break;
			default: return;
		}
		
		isset($source)
			or die('thumbnalize: Can not load the image');
		
		$thumb = imagecreatetruecolor($max_width, $max_height)
			or die('Can not create the image');
		
		imagecopyresampled($thumb, $source, 0, 0, 0, 0, $max_width, $max_height, $w, $h);
		
		imagejpeg($thumb, $thumb_file);
		
		imagedestroy($thumb);
	}
