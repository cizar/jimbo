<?php

	/*
	 * resize_scaled.php
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
	
	function resize_scaled ($img, $width, $height)
	{
		$src_width = imagesx($img);
		$src_height = imagesy($img);

		$ratio = $src_width / $src_height;

		if ($ratio > $width / $height)
			$height = $width / $ratio;
		else
			$width = $height * $ratio;

		$output = imagecreatetruecolor($width, $height);
		imagecopyresampled($output, $img, 0, 0, 0, 0, $width, $height, $src_width, $src_height);

		return $output;
	}