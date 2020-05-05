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
 * Migration script for adding component entry for com_support
 **/
class Migration20170831000000ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('support');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('support');
	}
}
