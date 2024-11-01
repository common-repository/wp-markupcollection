<label>
	<?php wpmc_e('Filter') ?><br>
	<select name="<?php echo WPMC_META_FILTER ?>">
		<option value="">(<?php wpmc_e('none') ?>)</option>
		<?php foreach($filters as $command => $name) : ?>
		<option
			value="<?php echo esc_attr($command) ?>"
			<?php selected($selected[$command]) ?>
		><?php esc_html_e($name) ?></option>
		<?php endforeach ?>
		<option value="(custom)" <?php selected($selected['(custom)']) ?>>(<?php wpmc_e('custom') ?>)</option>
	</select>
</label>
<ul class="howto">
	<li>(<?php wpmc_e('none') ?>) - <?php wpmc_e("Don't use WP-MarkupCollection.") ?></li>
	<li>(<?php wpmc_e('custom') ?>) - <?php wpmc_e("See Custom Fields.") ?></li>
</ul>
<label>
	<input type="checkbox"
		name="<?php echo WPMC_CONVERT_HTML ?>"
	> <?php wpmc_e("Convert HTML on Save") ?>
</label>
