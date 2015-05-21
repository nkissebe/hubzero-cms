<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

Html::behavior('tooltip');

// Menu
Toolbar::title(Lang::txt('COM_BILLBOARDS_MANAGER') . ': ' . Lang::txt('COM_BILLBOARDS'), 'addedit.png');
Toolbar::preferences($this->option, '200', '500');
Toolbar::spacer();
Toolbar::publishList();
Toolbar::unpublishList();
Toolbar::spacer();
Toolbar::addNew();
Toolbar::editList();
Toolbar::spacer();
Toolbar::deleteList(Lang::txt('COM_BILLBOARDS_CONFIRM_DELETE'));
Toolbar::spacer();
Toolbar::help('billboards');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo $this->rows->count(); ?>);" /></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_BILLBOARDS_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_BILLBOARDS_COL_NAME'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_BILLBOARDS_COL_COLLECTION'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_BILLBOARDS_COL_ORDERING') . Html::grid('order', $this->rows->toArray()); ?></th>
				<th scope="col" class="priority-1"><?php echo Lang::txt('COM_BILLBOARDS_COL_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
	$i = 0;
	foreach ($this->rows as $row)
	{
		// See if the billboard is being edited by someone else
		if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00')
		{
			$checked = $this->grid('checkedout', $row, User::getInstance($row->checked_out)->get('name'), $row->checked_out_time);
		}
		else
		{
			$checked = $this->grid('id', $i, $row->id, false, 'cid');
		}

		$task  = $row->published ? 'unpublish' : 'publish';
		$class = $row->published ? 'publish' : 'unpublish';
		$alt   = $row->published ? Lang::txt('JPUBLISHED') : Lang::txt('JUNPUBLISHED');
?>
			<tr class="<?php echo "row$i"; ?>">
				<td>
					<?php echo $checked; ?>
				</td>
				<td class="priority-4">
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&cid=' . $row->id); ?>"><?php echo $row->name; ?></a>
				</td>
				<td class="priority-2">
					<?php echo $row->collection->name; ?>
				</td>
				<td class="order priority-3">
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td class="priority-1">
					<a class="state <?php echo $class;?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&cid=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_BILLBOARDS_SET_TO', $task); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				</td>
			</tr>
<?php $i++; } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>