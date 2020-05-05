<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Cli\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Database\Query;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * Developer authorization codes command class
 **/
class Authorizationcodes extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
		return;
	}

	/**
	 * Delete all authorization codes
	 *
	 * @return void
	 **/
	public function revoke()
	{
		// Attempt to delete tokens
		try
		{
			with(new Query)->delete('#__developer_authorization_codes')->execute();
		}
		catch (QueryFailedException $e)
		{
			$this->output->error('Error:' . $e->getMessage());
		}

		// Successfully deleted tokens
		$this->output->addLine('All authorization codes successfully revoked.', 'success');
	}

	/**
	 * Output help documentation
	 *
	 * @return void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Api authorization codes related commands.'
			);
	}
}
