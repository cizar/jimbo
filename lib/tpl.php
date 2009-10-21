<?php

	/*
	 * tpl.php
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

	function tpl ($template, $vars = null)
	{
		defined('TPL_DIR')
			or die('TPL_DIR not defined');
			
		ereg('\.tpl$', $template)
			or $template .= '.tpl'; 

	    is_null($vars)
	    	or extract($vars);
    	
	    ob_start();
	    include(TPL_DIR . DS . $template);
	    $contents = ob_get_contents();
	    ob_end_clean();
    
	    return $contents;
	}