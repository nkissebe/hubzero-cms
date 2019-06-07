<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;

Toolbar::title(Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY') . ': ' . Arr::getValue($this->config, 'name', ''), 'packages');

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();
if ($canDo->get('core.edit'))
{
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

// Determine status & options
$status = '';
$params = Component::params($this->option);   
?>

<script type="text/javascript">

function validURL(str) {
  var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
    '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
    '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
    '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
  return !!pattern.test(str);
}

function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	<?php echo $this->editor()->save('text'); ?>

	// do field validation
	if (document.getElementById('name').value == ''){
		alert('<?php echo Lang::txt('COM_INSTALLER_PACKAGES_ERROR_REPO'); ?>');
	} else if (document.getElementById('alias').value == ''){
		alert('<?php echo Lang::txt('COM_INSTALLER_PACKAGES_ERROR_ALIAS'); ?>');
	} else if (document.getElementById('alias').value == 'hz-installer'){
		alert('<?php echo Lang::txt('COM_INSTALLER_PACKAGES_ERROR_ALIAS'); ?>');
	} else if (document.getElementById('alias').value == 'packagist.org'){
		alert('<?php echo Lang::txt('COM_INSTALLER_PACKAGES_ERROR_ALIAS'); ?>');
	} else if (document.getElementById('description').value == ''){
		alert('<?php echo Lang::txt('COM_INSTALLER_PACKAGES_ERROR_DESCRIPTION'); ?>');
	} else if (document.getElementById('url').value == ''){
		alert('<?php echo Lang::txt('COM_INSTALLER_PACKAGES_ERROR_URL'); ?>');
	} else if (document.getElementById('type').value == ''){
		alert('<?php echo Lang::txt('COM_INSTALLER_PACKAGES_ERROR_TYPE'); ?>');
	} else {
		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=save'); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<?php if ($this->getError()): ?>
			<div class="col span12">
				<p class="error"><?php echo $this->getError(); ?></p>
			</div>
		<?php else: ?>
			<div class="col span7">
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_INSTALLER_PACKAGES_BASIC_INFO'); ?></span></legend>

					<div class="input-wrap">
						<label for="name"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_NAME'); ?></label>
						<input name="name" type="text" value="<?php echo $this->escape(Arr::getValue($this->config, 'name', '')); ?>"></input>
					</div>
					<div class="input-wrap">
						<label for="alias"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_ALIAS'); ?></label>
						<input name="alias" type="text" value="<?php echo isset($this->alias) ? $this->escape($this->alias) : ''; ?>"></input>
					</div>
					<div class="input-wrap">
						<label for="description"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_DESCRIPTION'); ?></label>
						<input name="description" type="text" value="<?php echo $this->escape(Arr::getValue($this->config, 'description', '')); ?>"></input>
					</div>
					<div class="input-wrap">
						<label for="url"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_URL'); ?></label>
						<input name="url" type="text" value="<?php echo $this->escape(Arr::getValue($this->config, 'url', '')); ?>"></input>
					</div>
					<div class="input-wrap">
						<label for="type"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_TYPE'); ?></label>
						<select name="type">
							<option value="github" selected="<?php echo Arr::getValue($this->config, 'type', '') == 'github' ? 'true' : ''; ?>">Github</option>
							<option value="gitlab" selected="<?php echo Arr::getValue($this->config, 'type', '') == 'gitlab' ? 'true' : ''; ?>">Gitlab</option>
						</select>
					</div>

					<div class="input-wrap">
						<input type="submit" value="<?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_UPDATE'); ?>">
					</div>
				</fieldset>
			</div>
			<div class="col span5">
				<?php if (!$this->isNew): ?>
					<p class="warning">
						<a class="button" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&alias=' . $this->alias . '&task=remove'); ?>"><?php echo Lang::txt('Remove Repository'); ?></a>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<input type="hidden" name="oldAlias" value="<?php echo $this->escape($this->alias); ?>" />
	<input type="hidden" name="isNew" value="<?php echo $this->isNew ? "true" : "false" ?>" />
	<input type="hidden" name="task" value="save" autocomplete="off" />

	<?php echo Html::input('token'); ?>
</form>
