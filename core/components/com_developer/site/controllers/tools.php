<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Site\Controllers;

use Hubzero\Component\SiteController;

/**
 * Tool Development Controller
 */
class Tools extends SiteController
{
	/**
	 * Display into page
	 * 
	 * @return void
	 */
	public function displayTask()
	{
		$this->view->display();
	}
}
