<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_PROJECTS') . ': ' . Lang::txt('COM_PROJECTS_CUSTOM_DESCRIPTION'));
if (User::authorise('core.edit', $this->option) || User::authorise('core.create', $this->option))
{
	Toolbar::apply('applyDescription');
	Toolbar::save('saveDescription');
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::divider();
//Toolbar::help('user');

$elements = array();

foreach ($this->fields as $field)
{
	$element = new stdClass;
	$element->label      = (string)$field->get('label');
	$element->name       = (string)$field->get('name');
	$element->field_type = (string)$field->get('type');
	if ($element->field_type == 'select')
	{
		$element->field_type = 'dropdown';
	}
	if ($element->field_type == 'textarea' || $element->field_type == 'editor')
	{
		$element->field_type = 'paragraph';
	}

	// NOT implemented yet
	/*$element->required = (bool)$field->get('required');
	$element->readonly = (bool)$field->get('readonly');
	$element->disabled = (bool)$field->get('disabled');*/
	//$element->create   = (int)$field->get('action_create');
	//$element->update   = (int)$field->get('action_update');
	//$element->edit     = (int)$field->get('action_edit');
	//$element->access   = (int)$field->get('access');
	$element->field_id = (int)$field->get('id');

	$element->field_options = new stdClass;
	$element->field_options->description          = (string)$field->get('description');
	$element->field_options->include_other_option = (bool)$field->get('option_other');
	$element->field_options->include_blank_option = (bool)$field->get('option_blank');

	$options = $field->options;

	if ($options->count())
	{
		$element->field_options->options = array();
		foreach ($options as $option)
		{
			$opt = new stdClass;
			$opt->field_id = (int)$option->get('id');
			$opt->label    = (string)$option->get('label');
			$opt->value    = (string)$option->get('value', $option->get('label'));
			$opt->checked  = (bool)$option->get('checked');
			$dependents = $option->get('dependents', '[]');
			$dependents = $dependents ? $dependents : '[]';
			$dependents = json_decode($dependents);
			$opt->dependents = implode(', ', $dependents);

			$element->field_options->options[] = $opt;
		}
	}

	$elements[] = $element;
}

$json = new stdClass;
$json->fields = $elements;
$json = json_encode($json);

$this->css('formbuilder.css')
     ->js('vendor.js')
     ->js('formbuilder.js');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="fb-main"><?php echo Lang::txt('COM_MEMBERS_PROFILE'); ?></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="saveDescription" />
	<input type="hidden" name="descriptionFields" id="description-schema" value="<?php echo $this->escape($json); ?>" />

	<?php echo Html::input('token'); ?>
</form>

<script type="text/javascript">
	var fb = null;

	jQuery(document).ready(function($){
		fb = new Formbuilder({
			selector: '.fb-main',
			bootstrapData: <?php echo json_encode($elements); ?>
		});

		fb.on('save', function(payload){
			//console.log(payload);
			$('#description-schema').val(payload);
		});
	});

	function submitbutton(pressbutton)
	{
		var form = document.getElementById('adminForm');

		if (pressbutton == 'cancel') {
			submitform(pressbutton);
			return;
		}

		fb.mainView.saveForm();

		submitform(pressbutton);
	}
</script>
