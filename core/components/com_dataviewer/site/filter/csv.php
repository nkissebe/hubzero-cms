<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();


function filter($res, &$dd, $ob_mode = false)
{
	$data = $res['data'];
	$w = '"';
	$s = ",";
	$nl = "\r\n";

	$csv = '';


	if (!$ob_mode) {
		$file_name = preg_replace('/\W/', '_', $dd['title']) . '.csv';

		header('Content-Description: File Transfer');
		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment;filename=' . $file_name);

	} else {
		ob_clean();
	}

	//Header
	$h_arr = mysqli_fetch_assoc($data);
	if (!$h_arr) {
		print 'No data available';
		return;
	}

	foreach ($h_arr as $key => $val) {
		if (!isset($dd['cols'][$key]['hide'])) {
			$label = isset($dd['cols'][$key]['label']) ? $dd['cols'][$key]['label'] : $key;
			if (isset($dd['cols'][$key]['unit']) && $dd['cols'][$key]['unit'] != '') {
				$label = $label . " [" . $dd['cols'][$key]['unit'] . "]";
			} elseif (isset($dd['cols'][$key]['units']) && $dd['cols'][$key]['units'] != '') {
				$label = $label . " [" . $dd['cols'][$key]['units'] . "]";
			}

			$label = str_replace('<br />', "  ", $label);
			$label = html_entity_decode(strip_tags($label), ENT_QUOTES, 'UTF-8');
			$label = str_replace('"', '""', $label);
			$csv .= $w . $label . $w . $s;
		}
	}
	$csv .= $nl;
	$csv .= columnsMetadata($dd);
	print $csv;

	mysqli_data_seek($data, 0);

	//Body
	while ($rec = mysqli_fetch_assoc($data)) {
		$row = '';
		foreach ($rec as $key => $val) {
			if (!isset($dd['cols'][$key]['hide'])) {

				if ($val != null) {
					$val = html_entity_decode(strip_tags($val), ENT_QUOTES, 'UTF-8');
				}

				if ($val == null) {
					$val = '';
				}

				$val = str_replace('"', '""', $val);
				$row .= $w . $val . $w . $s;
			}
		}
		print $row . $nl;
	}
	return '';
}

function columnsMetadata($dd)
{
	$w = '"';
	$s = ",";
	$columnsMetadata = "";

	foreach ($dd["cols"] as $column => $metadata)
	{
		$columnsMetadata .= columnMetadata($metadata) . $w . $s;
	}

	$columnsMetadata = rtrim($columnsMetadata, ($w . $s));
	$dataSeparator = "\r\nDATASTART\r\n";

	return $columnsMetadata . '"""'. $dataSeparator;
}

function columnMetadata($metadata)
{
	unset($metadata['label']);
	unset($metadata['idx']);
	unset($metadata['styles']);

	$columnMetadata = str_replace('"', '""', json_encode($metadata));
	$columnMetadata = rtrim(ltrim($columnMetadata, '{'), '}');

	return '"' . $columnMetadata;
}
