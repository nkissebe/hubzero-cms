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
 * Migration script for adding Cart - Paypal plugin
 **/
class Migration20170831000000PlgCartPaypal extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('cart', 'paypal', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('cart', 'paypal');
	}
}
