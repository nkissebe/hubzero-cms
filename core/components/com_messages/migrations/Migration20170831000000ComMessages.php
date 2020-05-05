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
 * Migration script for adding component entry for com_messages
 **/
class Migration20170831000000ComMessages extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Create component entry but do NOT create a menu item as
		// com_plugins is purposely separate from the 'components' list.
		//
		// string  $name            Component name
		// string  $option          com_xyz
		// int     $enabled         Whether or not the component should be enabled
		// string  $params          Component params (if already known)
		// bool    $createMenuItem  Create an admin menu item for this component
		$this->addComponentEntry('messages', null, 1, '', false);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('messages');
	}
}
