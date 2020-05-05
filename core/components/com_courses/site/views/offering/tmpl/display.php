<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//get no_html request var
$no_html = Request::getInt('no_html', 0);
$tmpl    = Request::getWord('tmpl', false);
$sparams = new \Hubzero\Config\Registry($this->course->offering()->section()->get('params'));

if (!$no_html && $tmpl != 'component') :
	$this->css('offering.css');

	$src = $this->course->logo('url');
	if ($logo = $this->course->offering()->section()->logo('url'))
	{
		$src = $logo;
	}
	else if ($logo = $this->course->offering()->logo('url'))
	{
		$src = $logo;
	}
	?>
	<header id="content-header"<?php if ($this->course->get('logo')) { echo ' class="with-identity"'; } ?>>
		<h2>
			<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
		</h2>
		<?php if ($src) { ?>
		<p class="course-identity">
			<img src="<?php echo Route::url($src); ?>" alt="<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>" />
		</p>
		<?php } ?>
		<p id="page_identity">
			<a class="prev" href="<?php echo Route::url($this->course->link()); ?>">
				<?php echo Lang::txt('COM_COURSES_COURSE_OVERVIEW'); ?>
			</a>
			<strong>
				<?php echo Lang::txt('COM_COURSES_OFFERING'); ?>:
			</strong>
			<span>
				<?php echo $this->escape(stripslashes($this->course->offering()->get('title'))); ?>
			</span>
			<strong>
				<?php echo Lang::txt('COM_COURSES_SECTION'); ?>:
			</strong>
			<span>
				<?php echo $this->escape(stripslashes($this->course->offering()->section()->get('title'))); ?>
			</span>
		</p>
	</header><!-- / #content-header -->

	<div class="innerwrap">
		<div id="page_container">
<?php endif; ?>

<?php if (!$this->course->offering()->access('view') && !$sparams->get('preview', 0)) {
	$view = new \Hubzero\Plugin\View(array(
		'folder'  => 'courses',
		'element' => 'outline',
		'name'    => 'shared',
		'layout'  => '_not_enrolled'
	));

	$view->set('course', $this->course)
	     ->set('option', 'com_courses')
	     ->set('message', Lang::txt('COM_COURSES_ENROLLMENT_REQUIRED'));

	echo $view;
} else if ($this->course->offering()->section()->expired() && !$sparams->get('preview', 0)) { ?>
			<div id="offering-introduction">
				<div class="instructions">
					<p class="warning"><?php echo Lang::txt('COM_COURSES_SECTION_EXPIRED'); ?></p>
				</div><!-- / .instructions -->
				<div class="questions">
					<p><strong><?php echo Lang::txt('COM_COURSES_WHERE_TO_LEARN_MORE'); ?></strong></p>
					<p><?php echo Lang::txt('COM_COURSES_WHERE_TO_LEARN_MORE_EXPLANATION', Route::url($this->course->link()), Route::url('index.php?option=' . $this->option . '&controller=courses&task=browse')); ?></p>
				</div><!-- / .post-type -->
			</div><!-- / #collection-introduction -->
<?php } else { ?>

	<?php if (!$no_html && $tmpl != 'component') : ?>
			<div id="page_sidebar">

				<ul id="page_menu">
					<?php
					$active = Request::getCmd('active');

					// Loop through each plugin and build menu item
					foreach ($this->plugins as $plugin)
					{
						// Do we want to show in menu?
						if (!$plugin->get('display_menu_tab'))
						{
							continue;
						}

						// Do we have access?
						if (!$this->course->offering()->access('manage', 'section') && $plugin->get('default_access') == 'managers')
						{
							continue;
						}

						// Can we view this tab?
						if (!$this->course->offering()->access('view') && !$sparams->get('preview', 0))
						{
							?>
							<li class="protected members-only course-<?php echo $plugin->get('name'); ?>-tab" data-title="<?php echo Lang::txt('COM_COURSES_RESTRICTED_PAGE'); ?>">
								<span class="<?php echo $plugin->get('name'); ?>" data-icon="&#x<?php echo $plugin->get('icon', 'f0a1'); ?>">
									<?php echo $this->escape($plugin->get('title')); ?>
								</span>
							</li>
							<?php
						}
						else
						{
							$link = Route::url($this->course->offering()->link() . '&active=' . $plugin->get('name'));
							?>
							<li class="<?php echo ($active == $plugin->get('name')) ? 'active' : ''; ?> course-<?php echo $plugin->get('name'); ?>-tab">
								<a class="<?php echo $plugin->get('name'); ?>" data-icon="&#x<?php echo $plugin->get('icon', 'f0a1'); ?>" data-title="<?php echo $this->escape($plugin->get('title')) . '&#xa;' . $this->escape($plugin->get('description')); ?>" href="<?php echo $link; ?>">
									<?php echo $this->escape($plugin->get('title')); ?>
								</a>
								<?php if ($meta_count = $plugin->get('meta_count')) { ?>
									<span class="meta">
										<span class="count"><?php echo $meta_count; ?></span>
									</span>
								<?php } ?>
								<?php echo $plugin->get('meta_alert'); ?>
							</li>
							<?php
						}
					}
					?>
				</ul><!-- /#page_menu -->
			</div><!-- /#page_sidebar -->

			<div id="page_main">
				<div id="page_notifications">
					<?php
						foreach ($this->notifications as $notification)
						{
							echo '<p class="' . $this->escape($notification['type']) . '">' . $this->escape($notification['message']) . '</p>';
						}
					?>
				</div><!-- /#page_notifications -->

				<div id="page_content" class="course_<?php echo $this->escape($active); ?>">
<?php endif; ?>

					<?php
					foreach ($this->plugins as $plugin)
					{
						if ($html = $plugin->get('html'))
						{
							echo $html;
						}
					}
					?>

		<?php if (!$no_html && $tmpl != 'component') : ?>
				</div><!-- /#page_content -->
			</div><!-- /#page_main -->
		<?php endif; ?>
<?php } ?>

	<?php if (!$no_html && $tmpl != 'component') : ?>
		</div><!-- /#page_container -->
	</div><!-- /.innerwrap -->
	<?php endif;
