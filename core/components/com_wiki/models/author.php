<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models;

use Hubzero\Database\Relational;
use User;

/**
 * Wiki author model
 */
class Author extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wiki';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'user_id' => 'positive|nonzero',
		'page_id' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  object
	 */
	public function page()
	{
		return $this->belongsToOne('Page', 'page_id');
	}

	/**
	 * Saves a string of comma-separated usernames or IDs to authors table
	 *
	 * @param   mixed    $authors  String or array of authors
	 * @param   integer  $page_id  The id of the page
	 * @return  boolean  True if authors successfully saved
	 */
	public static function setForPage($authors, $page_id)
	{
		if (!trim($authors))
		{
			return true;
		}

		// Get the list of existing authors
		$ids = array();

		$existing = self::all()
			->whereEquals('page_id', (int)$page_id)
			->rows();
		foreach ($existing as $ex)
		{
			$ids[] = $ex->get('user_id');
		}

		$err = null;

		$auths = array();

		// Turn the comma-separated string of authors into an array and loop through it
		if ($authors)
		{
			if (is_string($authors))
			{
				$authors = explode(',', $authors);
				$authors = array_map('trim', $authors);
			}

			foreach ($authors as $author)
			{
				// Attempt to load each user
				$targetuser = User::getInstance($author);

				// Ensure we found an account
				if (!is_object($targetuser))
				{
					// No account found for this username/ID
					// Move on to next record
					continue;
				}

				// Check if they're already an existing author
				if (in_array($targetuser->get('id'), $ids))
				{
					// Add them to the existing authors array
					$auths[] = $targetuser->get('id');
					// Move on to next record
					continue;
				}

				// Create a new author object and attempt to save the record
				$wpa = self::blank();
				$wpa->set('page_id', $page_id);
				$wpa->set('user_id', $targetuser->get('id'));

				if (!$wpa->save())
				{
					$err = $wpa->getError();
				}

				// Add them to the existing authors array
				$auths[] = $targetuser->get('id');
			}
		}

		// Loop through the old author list and check for nay entries not in the new list
		// Remove any entries not found in the new list
		foreach ($existing as $ex)
		{
			if (!in_array($ex->get('user_id'), $auths))
			{
				if (!$ex->destroy())
				{
					$err = $ex->getError();
				}
			}
		}

		if ($err)
		{
			return false;
		}

		return true;
	}
}
