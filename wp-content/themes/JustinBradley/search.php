<?php

/* Template Name: Search */

get_header(); 
//include (TEMPLATEPATH . '/header2.php');

$currentpage = 'search';

$mySearch =& new WP_Query("s=$s & showposts=-1");
$numResults = $mySearch->post_count;
?>
<div id="heroContainer" class="boxShadow clearfix articles news <?php echo $currentpage; ?>">
	<div class="hero clearfix">
		
    	<h2>Search Results</h2>
      <div class="page" id="post-<?php the_ID(); ?>">
        <div class="entry" <?php if (!empty($page_image_padding)) : echo ' style="padding-bottom:' . $page_image_padding .'px;"'; endif; ?>
        <?php if ($page_image_padding == 0) : echo ' style="padding-bottom:' . $page_image_padding .'px;"'; endif; ?>>  
        	<div class="intro">
              <p><em><?php echo $numResults; ?> results found with "<?php the_search_query(); ?>"</em></p>
          </div>
        </div>
      </div>
  </div>
</div><!-- end #heroContainer -->

<div id="mainContainer" class="boxShadow clearfix articles news <?php echo $currentpage; ?>"">
  <div id="main" class="clearfix">
  	<div class="leftCol">

<?php while (have_posts()) : the_post(); ?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      	<span class="post-date"><?php the_date(); ?></span>
        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
        <div class="entry">
        	<?php
           	the_excerpt(); 
          ?>
        </div>
	
      <div class="clear">&nbsp;</div>
	</div><!-- end post -->
<?php endwhile; ?>
</div><!-- end leftCol -->

<div style="clear:both;">&nbsp;</div>
      <div class="navigation">
        <?php previous_posts_link(' &lsaquo; Previous '); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php next_posts_link(' Next &rsaquo; '); ?>
    </div>
		</div>
  </div>
</div> 
<?php get_footer(); ?>