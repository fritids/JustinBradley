<?php

/* Template Name: Page */

get_header(); 

$currentpage = 'news';
?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php include(TEMPLATEPATH."/inc/top-two-col.php");?>
<?php endwhile; endif; wp_reset_query();?> 

<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$current_page = 'news';
if( get_the_title() == 'Recognition' ) {
	$current_page = 'recognition';
	$cats = array(
		'cat' => 23,
		'posts_per_page' => 5,
		'paged'=>$paged
	);
} else { 
	$cats = array(
	'cat' => -0,
	'posts_per_page' => 8,
	'paged'=>$paged
	);
};

query_posts( $cats ); 

?>
<div id="mainContainer" class="boxShadow clearfix articles <?php echo $current_page; ?>">
  <div id="main" class="clearfix">
  	<div class="leftCol">




<?php

while (have_posts()) : the_post();
?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      	<?php if ( $current_page == 'recognition' ) : ?>
        	<div class="reco_leftCol">
          	<?php the_post_thumbnail(); ?>
          </div>
        	<div class="reco_rightCol">
        <?php endif; ?>
      
      	<span class="post-date"><?php the_date(); ?></span>
        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
        <div class="entry">
        	<?php
          	if ( $current_page != 'recognition' ) : 
							the_post_thumbnail();
						endif;
						
           	the_excerpt(); 
          //the_content(); ?>
        </div>
      	
        <?php if ( $current_page == 'recognition' ) : echo '</div>'; endif; ?>
        <div class="clear">&nbsp;</div>
			</div><!-- end post -->
<?php endwhile;  ?>
		</div><!-- end leftCol -->
    <?php if ( $current_page != 'recognition' ) : ?>
	  <div class="rightCol">
  	  <h2><em>Archives</em></h2>
  	  <ul class="archives">
  	    <?php wp_get_archives('type=monthly'); ?>
  	  </ul>
      <div class="Info">
<?php
$blog = new Pod('blog_page');
$blog->findRecords(array('limit'=>'-1'));
$total_blogs = $blog->getTotalRows();

if( $total_blogs>0 ) {
	$blog->fetchRecord();
	$button_intro   = $blog->get_field('button_intro');
	$top_button_heading   = $blog->get_field('top_button_heading');
	$label   = $blog->get_field('name');
	$button_url   = $blog->get_field('button_url');
	?>
      
      	<span class="button_heading">
        	<?php echo $top_button_heading; ?>        
        </span>
        <span class="button_paragraph"><?php echo $button_intro; ?></span> 
        <a class="button" href="<?php echo $button_url; ?>" name="<?php echo $label; ?>"><?php echo $label; ?></a>     
<?php } ?>    
      </div>
   </div>
	 <?php endif; ?>
   <div style="clear:both;">&nbsp;</div>
   <div class="navigation">
   		<?php 
				
				$thisPage = $paged; 
				$totalPages = $wp_query->max_num_pages; 
				if( $thisPage == 1 ) {
					// on first page
					if( $thisPage == $totalPages ) {
						// only one page
						echo $thisPage;
					} else {
						// more than one page
						echo $thisPage;
						for ($i = 2; $i <= $totalPages; $i++) {
							// each additional page gets a linked page number
    					echo ' <a href="' . site_url() . '/about-us/' . $currentpage . '/page/' . $i . '">' . $i . '</a> ';
						}
						// Add next link
						next_posts_link(' Next &rsaquo; ');
					}
				} elseif ( $thisPage == $totalPages ) {
				 // this is the last page
					previous_posts_link(' &lsaquo; Previous ');
					for ($i = 1; $i <= $totalPages-1; $i++) {
						// each previous page gets a linked page number
    				echo ' <a href="' . site_url() . '/about-us/' . $currentpage . '/page/' . $i . '">' . $i . '</a> ';
					}
					echo $thisPage;
					
				} else {
					// this is neither the first or last page, there are pages before and after.
					previous_posts_link(' &lsaquo; Previous ');
					for ($i = 1; $i <= $thisPage-1; $i++) {
							// each additional page gets a linked page number
    					echo ' <a href="' . site_url() . '/about-us/' . $currentpage . '/page/' . $i . '">' . $i . '</a> ';
					}
					echo $thisPage;
					for ($i = $thisPage+1; $i <= $totalPages-1; $i++) {
							// each additional page gets a linked page number
    					echo ' <a href="' . site_url() . '/about-us/' . $currentpage . '/page/' . $i . '">' . $i . '</a> ';
					}
					next_posts_link(' Next &rsaquo; ');
				}
				?>
    </div>
 </div>
</div>
<?php get_footer(); ?>