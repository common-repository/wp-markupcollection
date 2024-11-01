<?php
	$locations = array(
		'normal,high'   => wpmc__('normal, high priority'),
		'normal,low'    => wpmc__('normal, low priority'),
		'advanced,high' => wpmc__('advanced, high priority'),
		'advanced,low'  => wpmc__('advanced, low priority'),
		'side,high'     => wpmc__('side, high priority'),
		'side,low'      => wpmc__('side, low priority'),
	);

	$phprunner_methods = array('post', 'exec');
?>
<div class="wrap">
	<h2>Markup Collection</h2>
	<form method="post" action="options.php">
		<?php settings_fields($option_page); ?>

		<h3><?php wpmc_e('General Settings') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php wpmc_e('Widget location') ?></th>
				<td>
					<select name="<?php $attrs->name('widget_location') ?>">
						<option value="none">(<?php wpmc_e('none') ?>)</option>
						<?php foreach($locations as $key => $location) : ?>
						<option
							value="<?php echo esc_attr($key) ?>"
							<?php $attrs->selected('widget_location', $key) ?>
						><?php _e($location) ?></option>
						<?php endforeach ?>
					</select>
				</td>
			</tr>
		</table>

		<h3><?php wpmc_e('Filter Settings') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php wpmc_e('Filters') ?></th>
				<td>
					<textarea class="large-text code" rows="10"
						name="<?php $attrs->name('filters') ?>"
					><?php $attrs->value('filters') ?></textarea>
					<div class="description">
						<?php wpmc_e('Lines beginning with # are comments.') ?>
					</div>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php wpmc_e('Default filter') ?></th>
				<td>
					<select name="<?php $attrs->name('default_filter') ?>">
						<option value="">(<?php wpmc_e('none') ?>)</option>
						<?php foreach($markup_filters as $filter) : ?>
						<option
							value="<?php echo esc_attr($filter['command']) ?>"
							<?php $attrs->selected('default_filter', $filter['command']) ?>
						><?php esc_html_e($filter['name']) ?></option>
						<?php endforeach ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php wpmc_e('About filters') ?></th>
				<td>
					<dl>
						<dt><strong><?php wpmc_e('Available filter commands') ?></strong></dt>
						<?php foreach($available_filters as $filter) : ?>
						<dd>
							<?php echo esc_html($filter['name']) ?>
							<?php if($filter['internal']) : ?>
								<small>[<?php wpmc_e('internal') ?>]</small>
							<?php else: ?>
								<?php if($filter['installed']) : ?>
									<small>[<?php wpmc_e('Installed') ?>]</small>
								<?php else: ?>
									<small><strong>[*<?php wpmc_e('Need to install') ?>]</strong></small>
								<?php endif; ?>
							<?php endif; ?>
							<?php if($filter['require']) : ?>
								<small>(<?php echo esc_html($filter['require']) ?>)</small>
							<?php endif; ?>
						</dd>
						<?php endforeach ?>
					</dl>

					<p><?php esc_html_e(sprintf(wpmc__('Current PHP Version: %s'), phpversion()));?></p>

					<dl>
						<dt><strong><?php wpmc_e('Filter commands search path') ?></strong></dt>
						<?php foreach($filter_paths as $path) : ?>
							<dd><small><?php echo esc_html($path) ?></small></dd>
						<?php endforeach ?>
					</dl>

					<p><small><?php wpmc_e('If you want to use other filter, please refer to "custom.example.php" of plugin directory.') ?></small></p>
				</td>
			</tr>
		</table>

		<h3><?php wpmc_e('Fenced code block Settings') ?></h3>
		<p><?php wpmc_e('Settings for apply syntax highlighter.') ?></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php wpmc_e('Template') ?></th>
				<td>
					<input type="text" class="large-text"
						name="<?php $attrs->name('code_block_template') ?>"
						value="<?php $attrs->value('code_block_template') ?>"
					>
					<div class="description">
						<dl>
							<dt><b><?php wpmc_e('Example') ?>:</b></dt>
							<dd><?php echo esc_html('<pre lang="$LANG">$CODE</pre>') ?></dd>
							<dd><?php echo esc_html('<pre class="brush: $LANG">$CODE</pre>') ?></dd>
							<dd><?php echo esc_html('[$LANG]$CODE[/$LANG]') ?></dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php wpmc_e('Default LANG') ?></th>
				<td>
					<input type="text" class="regular-text"
						name="<?php $attrs->name('default_lang') ?>"
						value="<?php $attrs->value('default_lang') ?>"
					> <span class="description"><b><?php wpmc_e('Default') ?>:</b> text</span>
					<p class="description"><?php wpmc_e('Use when language not specified.') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php wpmc_e('Decode character entity reference') ?></th>
				<td>
					<input type="checkbox"
						name="<?php $attrs->name('decode_character_entity_reference') ?>"
						<?php $attrs->checked('decode_character_entity_reference') ?>
					> <span class="description"><b><?php wpmc_e('Default') ?>:</b> <?php wpmc_e('Off') ?></span>
					<p class="description"><?php wpmc_e('Decode "&amp;lt;", "&amp;amp;" etc.') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php wpmc_e('Decode numeric character reference') ?></th>
				<td>
					<input type="checkbox"
						name="<?php $attrs->name('decode_numeric_character_reference') ?>"
						<?php $attrs->checked('decode_numeric_character_reference') ?>
					> <span class="description"><b><?php wpmc_e('Default') ?>:</b> <?php wpmc_e('Off') ?></span>
					<p class="description"><?php wpmc_e('Decode "&amp;#39;", etc.') ?></p>
				</td>
			</tr>
		</table>

		<h3><?php wpmc_e('Advanced Settings') ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php wpmc_e('Enable cache') ?></th>
				<td>
					<input type="checkbox"
						name="<?php $attrs->name('cache_enabled') ?>"
						<?php $attrs->checked('cache_enabled') ?>
					> <span class="description"><b><?php wpmc_e('Default') ?>:</b> <?php wpmc_e('On') ?></span>
					<p class="description">
						<?php wpmc_e('Cache will be used to avoid execute unnecessary filter.') ?>
					</p>
					<p class="description">
						<?php echo esc_html(wpmc__('Cache will be stored to hidden custom field of posts.')) ?>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php wpmc_e('Delete cache') ?></th>
				<td>
					<input type="checkbox"
						name="<?php $attrs->name('delete_cache') ?>"
					> <span class="description"><b><?php wpmc_e('Default') ?>:</b> <?php wpmc_e('Off') ?></span>
					<p class="description">
						<?php echo esc_html(sprintf(wpmc__('Number of cache: %d'), $cache_count)) ?>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php wpmc_e('Filter priority') ?></th>
				<td>
					<input type="number" class="small-text"
						name="<?php $attrs->name('filter_priority') ?>"
						value="<?php $attrs->value('filter_priority') ?>"
					> <span class="description"><b><?php wpmc_e('Default') ?>:</b> 0</span>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php wpmc_e('PHPRunner execution method') ?></th>
				<td>
					<select name="<?php $attrs->name('phprunner') ?>">
						<?php foreach($phprunner_methods as $method) : ?>
						<option
							value="<?php echo esc_attr($method) ?>"
							<?php $attrs->selected('phprunner', $method) ?>
						><?php esc_html_e($method) ?></option>
						<?php endforeach ?>
					</select > <span class="description"><b><?php wpmc_e('Default') ?>:</b> post</span>
					<p class="description">
						<?php wpmc_e('post: Execute filter via HTTP POST internally.') ?>
					</p>
					<p class="description">
						<?php wpmc_e('exec: Execute filter as an external script. (Require php cli)') ?>
					</p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php wpmc_e('Save Changes') ?>">
		</p>
	</form>
</div>
