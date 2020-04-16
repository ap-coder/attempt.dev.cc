<div class="tab-menu">
	<div><a <?php echo !isset($_GET['list']) || isset($_GET['list']) && $_GET['list'] != 'categories' ? 'href="'.get_permalink($post->ID).'?list=categories'.'"' : ''?>>List Categories</a></div>
	<div><a <?php echo !isset($_GET['list']) || isset($_GET['list']) && $_GET['list'] != 'features' ? 'href="'.get_permalink($post->ID).'?list=features'.'"' : ''?>>List Features</a></div>
</div>