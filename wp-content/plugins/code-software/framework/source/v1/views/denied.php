<?php get_header('members');?>
<div id="main">
	<div class="page-content" style="min-height: 600px; padding-top: 100px;">
			<div class="col-sm-4 middle-align">
			<h1 style="text-align: center">
				<?php
switch ($status) {
    case 403:
        echo '403: Unauthorized Access';
        break;
    case 404:
        echo '404: Page Not Found';
        break;
    case 500:
        echo '403: Server Error Occurred';
        $message = 'We apologize about any inconvenience this may have caused. The server admin has been notified.';
        break;
}
?>
			</h1>
			<hr>
			<div class="panel panel-default">
				<div class="panel-body">
					<?php if (strlen($message) > 0): ?>
						<?php echo $message; ?>
					<?php else: ?>
						You are seeing this message because you do not have access to this page.
					<?php endif;?>
				</div>
			</div>
		</div>
</div>
</div>
<?php get_footer();?>