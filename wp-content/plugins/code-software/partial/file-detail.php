<style>
	<?php include 'styles.php';?>
</style>
<div class="wrap">
	<h2><?php esc_html_e('File Detail');?></h2>
	<form method="POST" action="<?=get_admin_url();?>admin.php?page=wp-watchman">
		<div class="card">
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label for="input-text">Option</label>
						</th>
						<td>
							Value
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
</div><!-- .wrap -->
