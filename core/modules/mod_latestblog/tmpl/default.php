<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$c = 0;
?>
<div class="latest_discussions_module <?php echo $this->params->get('moduleclass_sfx'); ?>">
	<?php if (count($this->posts) > 0) : ?>
		<ul class="blog-entries">
		<?php 
		foreach ($this->posts as $post)
		{
			if ($c < $this->limit)
			{
				?>
					<li>
						<?php if ($this->params->get('details', 1)) { ?>
							<p class="entry-author-photo">
								<img src="<?php echo $post->creator->picture(); ?>" alt="" />
							</p>
						<?php } ?>
						<div class="entry-content">
							<h4>
								<a href="<?php echo Route::url($post->link()); ?>"><?php echo $this->escape(stripslashes($post->get('title'))); ?></a>
							</h4>
							<?php if ($this->params->get('details', 1)) { ?>
								<dl class="entry-meta">
									<dt>
										<span>
											<?php echo Lang::txt('MOD_LATESTBLOG_ENTRY_NUMBER', $post->get('id')); ?>
										</span>
									</dt>
									<dd class="date">
										<time datetime="<?php echo $post->published(); ?>">
											<?php echo $post->published('date'); ?>
										</time>
									</dd>
									<dd class="time">
										<time datetime="<?php echo $post->published(); ?>">
											<?php echo $post->published('time'); ?>
										</time>
									</dd>
									<dd class="author">
										<a href="<?php echo Route::url('index.php?option=com_members&id=' . $post->get('created_by')); ?>">
											<?php echo $this->escape(stripslashes($post->creator->get('name'))); ?>
										</a>
									</dd>
									<dd class="location">
										<a href="<?php echo $post->link('base'); ?>">
											<?php 
											/*switch ($post->get('scope'))
											{
												case 'site':
													echo Lang::txt('MOD_LATESTBLOG_LOCATION_BLOG_SITE');
												break;

												case 'member':
													echo Lang::txt('MOD_LATESTBLOG_LOCATION_BLOG_MEMBER');
												break;

												case 'group':
													echo $this->escape(stripslashes($post->item('title')));
												break;
											}*/
											echo $this->escape(stripslashes($post->item('title')));
											?>
										</a>
									</dd>
								</dl>
							<?php } ?>
							<?php if ($this->params->get('preview', 1)) { ?>
								<div class="entry-body">
									<?php 
									if ($this->pullout && $c == 0)
									{
										echo \Hubzero\Utility\Str::truncate(strip_tags($post->content), $this->params->get('pulloutlimit', 500));
									}
									else
									{
										echo \Hubzero\Utility\Str::truncate(strip_tags($post->content), $this->params->get('charlimit', 100));
									}
									?>
								</div>
							<?php } ?>
						</div>
					</li>
				<?php 
			}
			$c++;
		}
		?>
		</ul>
	<?php else : ?>
		<p><?php echo Lang::txt('MOD_LATESTBLOG_NO_RESULTS'); ?></p>
	<?php endif; ?>
	
	<?php if ($more = $this->params->get('morelink', '')) : ?>
		<p class="more">
			<a href="<?php echo $more; ?>"><?php echo Lang::txt('MOD_LATESTBLOG_MORE_RESULTS'); ?></a>
		</p>
	<?php endif; ?>
</div>