<?php

	/*
	 * parse_class.php
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

	function parse_class ($class)
	{
		$aux = explode('.', $class);

		return array
		(
			'class'		=> $class,
			'name'		=> array_pop($aux),
			'package'	=> implode('.', $aux),
			'path'		=> implode(DS, $aux)
		);
	}
