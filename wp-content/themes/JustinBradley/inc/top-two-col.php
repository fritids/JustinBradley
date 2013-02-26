<div id="heroContainer" class="boxShadow clearfix <?php echo $currentpage; ?>">
	<div class="hero clearfix"<?php 
	if (!empty($page_image)) :
		echo '	style="background:url(\'' . $page_image . '\') bottom center no-repeat"' ;
	endif;
	?>>
		
    	<h2><?php the_title(); ?></h2>
      <div <?php post_class() ?> id="post-<?php the_ID(); ?>">
        <div class="entry" <?php if (!empty($page_image_padding)) : echo ' style="padding-bottom:' . $page_image_padding .'px;"'; endif; ?>
        <?php if ($page_image_padding == 0) : echo ' style="padding-bottom:' . $page_image_padding .'px;"'; endif; ?>>  
        	<div class="intro">
              <?php the_content(); ?>
          </div>
          <div class="rightCol">
          	<?php if (!empty($page_overview)) : ?>
            <div class="overviews">
              <?php echo $page_overview; ?>
            </div>
            <?php endif; ?>
          </div>
          <?php if (!empty($page_left_column_text)) : ?>
          <div class="leftCol">
          	<div class="pullquote">
	          	<?php echo $page_left_column_text; ?>
            </div>
          </div>
          <?php endif; ?>
          <?php if (!empty($page_top_button_heading) || (!empty($page_top_button_url) && !empty($page_top_button_label))) : ?>
          <div class="leftCol">
            <div class="Info">
            	<?php if (!empty($page_top_button_heading)) : ?>
              <span class="button_heading">
              	<?php echo $page_top_button_heading; ?>
              </span>
              <?php endif; ?>
              <?php
							if(!empty($page_top_button_url) && !empty($page_top_button_label)) {
								echo '<a class="button" href="' . $page_top_button_url . '" name="' . $page_top_button_label . '">' . $page_top_button_label . '</a>';
							}
							?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
  </div>
</div><!-- end #heroContainer -->