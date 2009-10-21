<?php

	/*
	 * core.Paginator
	 *
	 * This file is a part of Jimbo package. http://www.nixar.org/jimbo
	 *
	 * Copyright (c) 2006, Cesar Kastli <cesar@nixar.org>
	 *
	 * This library is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU Lesser General Public License as
	 * published by the Free Software Foundation; either version 2.1 of the
	 * License, or (at your option) any later version
	 *
	 * See http://www.gnu.org/copyleft/lesser.html for details
	 *
	 */

	class Paginator
	{
		// Properties

		var $_records_per_page;
		var $_records;
		var $_current_page;

		// Constructor
		
		function __construct ($records_per_page = 50)
		{
			if ($records_per_page == 0)
				throw new Exception('records_per_page must be greater than zero');
				
			$this->_records_per_page = $records_per_page;
			$this->_records = 0;
			$this->_current_page = 1;
		}

		// Public methods

		function get_records ()
		{
			return $this->_records;
		}

		function set_records ($records)
		{
			$this->_records = $records;
			$this->_update();
		}

		function get_records_per_page ()
		{
			return $this->_records_per_page;
		}

		function get_rpp ()
		{
			return $this->get_records_per_page();
		}

		function set_records_per_page ($records_per_page)
		{
			$this->_records_per_page = $records_per_page;
			$this->_update();
		}

		function set_rpp ($records_per_page)
		{
			$this->set_records_per_page($records_per_page);
		}

		function get_pages ()
		{
			if ($pages = ceil($this->get_records() / $this->get_records_per_page()))
				return $pages;
			return 1;
		}

		function get_current_page ()
		{
			return $this->_current_page;
		}

		function go_to ($page)
		{
			if ($page >= $this->get_first_page() and $page <= $this->get_last_page())
			{
				$this->_current_page = $page;
			}
		}

		function go_prev_page ()
		{
			$this->go_to($this->get_prev_page());
		}

		function go_next_page ()
		{
			$this->go_to($this->get_next_page());
		}

		function go_first_page ()
		{
			$this->go_to($this->get_first_page());
		}

		function go_last_page ()
		{
			$this->go_to($this->get_last_page());
		}

		function get_prev_page()
		{
			if ($this->has_prev_page())
				return $this->get_current_page() - 1;
			return $this->get_first_page();
		}

		function get_next_page()
		{
			if ($this->has_next_page())
				return $this->get_current_page() + 1;
			return $this->get_last_page();
		}

		function get_first_page ()
		{
			return 1;
		}

		function get_last_page ()
		{
			return $this->get_pages();
		}

		function get_offset ()
		{
			return ($this->get_current_page() - $this->get_first_page()) * $this->get_records_per_page();
		}

		function get_first_record ()
		{
			return $this->get_offset() + 1;
		}

		function get_last_record ()
		{
			$aux = $this->get_offset() + $this->get_records_per_page();
			return min($aux, $this->get_records());
		}
		
		function has_pages ()
		{
			return $this->get_last_page() > 1;
		}

		function has_prev_page ()
		{
			return $this->get_current_page () > $this->get_first_page();
		}
		
		function has_next_page ()
		{
			return $this->get_current_page () < $this->get_last_page();
		}
		
		function get_info ($format = 'Results: %s to %s of %s')
		{
			return sprintf
			(
				$format,
				$this->get_first_record(),
				$this->get_last_record(),
				$this->get_records()
			);
		}

		function dump ()
		{
			printf("<table><caption>%s</caption>", $this->get_info());
			printf("<tr><th>Records Per Page</th><td>%s</td></tr>", $this->get_rpp());
			printf("<tr><th>Records</th><td>%s</td></tr>", $this->get_records());
			printf("<tr><th>Pages</th><td>%s</td></tr>", $this->get_pages());
			printf("<tr><th>First Page</th><td>%s</td></tr>", $this->get_first_page());
			printf("<tr><th>Current Page</th><td>%s</td></tr>", $this->get_current_page());
			printf("<tr><th>Last Page</th><td>%s</td></tr>", $this->get_last_page());
			print("</table>");
		}
		
		// Private methods

		function _update ()
		{
			if ($this->get_last_page() < $this->get_current_page())
			{
				$this->go_last_page();
			}
		}
	}
