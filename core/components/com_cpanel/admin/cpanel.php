<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cpanel\Admin;

// No access check.
require_once __DIR__ . DS . 'controllers' . DS . 'cpanel.php';

// Instantiate controller
$controller = new Controllers\Cpanel();
$controller->execute();
