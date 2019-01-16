<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing polltitle module
 **/
class Migration20190109000000ModPolltitle extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addModuleEntry('mod_polltitle');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteModuleEntry('mod_polltitle');
	}
}