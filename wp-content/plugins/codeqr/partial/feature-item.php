<div class="feature client-file">

	<div class="flex-row" style="flex-direction: row;">

		<div>
			<div class="flex-row">
				<div class="option_name">API Option: <code><?php echo isset($template) && isset($template->option_name) ? $template->option_name : ''; ?></code></div>
				<div class="option_value">Option Value: <code><?php echo isset($template) && isset($template->option_value) ? $template->option_value : ''; ?></code></div>
				<div class="option_type">Option Type: <code><?php echo isset($template) && isset($template->option_type) ? $template->option_type : ''; ?></code></div>
				<!-- <div class="max-char">Max Characters: <code><?php echo isset($template) && isset($template->max_char) && strlen($template->max_char) ? $template->max_char : '0'; ?></code></div>
				<div class="min-char">Min Characters: <code><?php echo isset($template) && isset($template->min_char) && strlen($template->min_char) ? $template->min_char : '0'; ?></code></div> -->
				<?php if( isset($template) && isset($template->option_limit) && !empty($template->option_limit) ):?><div class="option_limit">Char. Limit: <code><?php echo $template->option_limit; ?></code></div><?php endif; ?>
			</div>
			<input type="hidden" name="template_part[]" value="<?php echo htmlspecialchars(json_encode($template)); ?>" class="template_part">
		</div>

		<div style="margin-left: auto; align-self: flex-end" >
			<div class="flex-row" style="flex-direction: row-reverse; ">
				<a style="cursor: pointer;" class="btn delete"><i class="fa fa-remove"></i> &nbsp;Remove</a>
			</div>
		</div>

	</div>
</div>