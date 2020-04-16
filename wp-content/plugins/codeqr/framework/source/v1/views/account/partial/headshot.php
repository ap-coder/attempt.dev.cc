<?php if( !empty($data['profile_info']['headshot']) ) : ?>
<img src="<?php echo image_url($data['profile_info']['headshot']); ?>" alt="">
<?php else : ?>
<img src="https://placehold.it/490x490" alt="">
<?php endif;?>