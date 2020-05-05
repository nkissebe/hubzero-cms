<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding entry for Projects - Feed plugin
 **/
class Migration20170831000000PlgProjectsFeed extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('projects', 'feed');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('projects', 'feed');
	}
}
