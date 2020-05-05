<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Blog\Models\Entry;
use Request;
use Config;
use Notify;
use Route;
use Event;
use User;
use Lang;
use Date;
use App;

/**
 * Blog controller class for entries
 */
class Entries extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		parent::execute();
	}

	/**
	 * Display a list of blog entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'scope' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scope',
				'scope',
				''
			),
			'scope_id' => Request::getState(
				$this->_option . '.' . $this->_controller . '.scope_id',
				'scope_id',
				0,
				'int'
			),
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1,
				'int'
			),
			'access' => Request::getState(
				$this->_option . '.' . $this->_controller . '.access',
				'access',
				0,
				'int'
			),
			// Get sorting variables
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'title'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$entries = Entry::all();

		if ($filters['search'])
		{
			$entries->whereLike('title', strtolower((string)$filters['search']));
		}

		if ($filters['scope'])
		{
			$entries->whereEquals('scope', $filters['scope']);
		}

		if ($filters['scope_id'])
		{
			$entries->whereEquals('scope_id', (int)$filters['scope_id']);
		}

		if ($filters['state'] >= 0)
		{
			$entries->whereEquals('state', (int)$filters['state']);
		}

		if ($filters['access'])
		{
			$entries->whereEquals('access', (int)$filters['access']);
		}

		// Get records
		$rows = $entries
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Output the HTML
		$this->view
			->set('rows', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		Request::setVar('hidemainmenu', 1);

		if (!is_object($row))
		{
			// Incoming
			$id = Request::getArray('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$row = Entry::oneOrNew($id);
		}

		if ($row->isNew())
		{
			$row->set('created_by', User::get('id'));
			$row->set('created', Date::toSql());
			$row->set('publish_up', Date::toSql());
		}

		// Output the HTML
		$this->view
			->set('row', $row)
			->setLayout('edit')
			->display();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$fields = Request::getArray('fields', array(), 'post', 'none', 2);

		if (isset($fields['publish_up']) && $fields['publish_up'] != '')
		{
			$fields['publish_up']   = Date::of($fields['publish_up'], Config::get('offset'))->toSql();
		}
		if (isset($fields['publish_down']) && $fields['publish_down'] != '')
		{
			$fields['publish_down'] = Date::of($fields['publish_down'], Config::get('offset'))->toSql();
		}

		// Initiate extended database class
		$row = Entry::oneOrNew($fields['id'])->set($fields);

		// Trigger before save event
		$isNew  = $row->isNew();
		$result = Event::trigger('onBlogBeforeSave', array(&$row, $isNew));

		if (in_array(false, $result, true))
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Store content
		if (!$row->save())
		{
			Notify::error($row->getError());
			return $this->editTask($row);
		}

		// Process tags
		$row->tag(trim(Request::getString('tags', '')));

		// Trigger after save event
		Event::trigger('onBlogAfterSave', array(&$row, $isNew));

		// Notify of success
		Notify::success(Lang::txt('COM_BLOG_ENTRY_SAVED'));

		// Redirect to main listing or go back to edit form
		if ($this->getTask() == 'apply')
		{
			return $this->editTask($row);
		}

		$this->cancelTask();
	}

	/**
	 * Delete one or more entries
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		$removed = 0;

		foreach ($ids as $id)
		{
			$entry = Entry::oneOrFail(intval($id));

			// Delete the entry
			if (!$entry->destroy())
			{
				Notify::error($entry->getError());
				continue;
			}

			// Trigger before delete event
			Event::trigger('onBlogAfterDelete', array($id));

			$removed++;
		}

		if ($removed)
		{
			Notify::success(Lang::txt('COM_BLOG_ENTRIES_DELETED'));
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Sets the state of one or more entries
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		$ids = Request::getArray('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_BLOG_SELECT_ENTRY_TO', $this->_task));
			return $this->cancelTask();
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$row = Entry::oneOrNew(intval($id));
			$row->set('state', $state);

			// Store new content
			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			switch ($this->_task)
			{
				case 'publish':
					$message = Lang::txt('COM_BLOG_ITEMS_PUBLISHED', $success);
				break;
				case 'unpublish':
					$message = Lang::txt('COM_BLOG_ITEMS_UNPUBLISHED', $success);
				break;
				case 'archive':
					$message = Lang::txt('COM_BLOG_ITEMS_ARCHIVED', $success);
				break;
			}

			Notify::success($message);
		}

		// Set the redirect
		$this->cancelTask();
	}

	/**
	 * Turn comments on/off
	 *
	 * @return  void
	 */
	public function setcommentsTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		if (!User::authorise('core.edit.state', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// Incoming
		$ids = Request::getArray('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);
		$state = Request::getInt('state', 0);

		// Check for a resource
		if (count($ids) < 1)
		{
			Notify::warning(Lang::txt('COM_BLOG_SELECT_ENTRY_TO_COMMENTS', $this->_task));
			return $this->cancelTask();
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the article
			$row = Entry::oneOrFail(intval($id));
			$row->set('allow_comments', $state);

			// Store new content
			if (!$row->save())
			{
				Notify::error($row->getError());
				continue;
			}

			$success++;
		}

		if ($success)
		{
			$message = $state
				? Lang::txt('COM_BLOG_ITEMS_COMMENTS_ENABLED', $success)
				: Lang::txt('COM_BLOG_ITEMS_COMMENTS_DISABLED', $success);

			Notify::success($message);
		}

		// Set the redirect
		$this->cancelTask();
	}
}
