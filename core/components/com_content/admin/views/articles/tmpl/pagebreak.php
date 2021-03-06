<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$script  = 'function insertPagebreak() {'."\n\t";
// Get the pagebreak title
$script .= 'var title = document.getElementById("title").value;'."\n\t";
$script .= 'if (title != \'\') {'."\n\t\t";
$script .= 'title = "title=\""+title+"\" ";'."\n\t";
$script .= '}'."\n\t";
// Get the pagebreak toc alias -- not inserting for now
// don't know which attribute to use...
$script .= 'var alt = document.getElementById("alt").value;'."\n\t";
$script .= 'if (alt != \'\') {'."\n\t\t";
$script .= 'alt = "alt=\""+alt+"\" ";'."\n\t";
$script .= '}'."\n\t";
$script .= 'var tag = "<hr class=\"system-pagebreak\" "+title+" "+alt+"/>";'."\n\t";
$script .= 'window.parent.jInsertEditorText(tag, \''.Request::getCmd('e_name').'\');'."\n\t";
$script .= 'window.parent.$.fancybox.close();'."\n\t";
$script .= 'return false;'."\n";
$script .= '}'."\n";

Document::addScriptDeclaration($script);
?>
	<form>
		<table>
			<tr>
				<th>
					<label for="title">
						<?php echo Lang::txt('COM_CONTENT_PAGEBREAK_TITLE'); ?>
					</label>
				</th>
				<td>
					<input type="text" id="title" name="title" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="alias">
						<?php echo Lang::txt('COM_CONTENT_PAGEBREAK_TOC'); ?>
					</label>
				</th>
				<td>
					<input type="text" id="alt" name="alt" />
				</td>
			</tr>
		</table>
	</form>
	<button onclick="insertPagebreak();"><?php echo Lang::txt('COM_CONTENT_PAGEBREAK_INSERT_BUTTON'); ?></button>
