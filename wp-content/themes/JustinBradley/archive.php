<?php 
get_header(); 
//include (TEMPLATEPATH . '/header2.php'); 
?>

<?php if (have_posts()) : ?>

<div id="heroContainer" class="boxShadow clearfix <?php echo $currentpage; ?>">
	<div class="hero clearfix">
    	<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>

			<?php /* If this is a category archive */ if (is_category()) { ?>
				<h2>Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>

			<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
				<h2>Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>

			<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
				<h2>Archive for <?php the_time('F jS, Y'); ?></h2>

			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
				<h2>Archive for <?php the_time('F, Y'); ?></h2>

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
				<h2>Archive for <?php the_time('Y'); ?></h2>

			<?php /* If this is an author archive */ } elseif (is_author()) { ?>
				<h2>Author Archive</h2>

			<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
				<h2>Blog Archives</h2>
			
			<?php } ?>
  </div>
</div><!-- end #heroContainer -->

<div id="mainContainer" class="boxShadow clearfix articles">
  <div id="main" class="clearfix">
  	<div class="leftCol">
			<?php while (have_posts()) : the_post(); ?>
			
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      	<span class="post-date"><?php the_date(); ?></span>
        <h2><a href="<?php the_permalink() ?>"><?php the_title(); ?>
        <?php 
        	if ($author = get_post_meta( get_the_ID(), 'written_by', true ) ) {
        		echo '<span class="author"> By ' . $author . '</span>' ; 
        	}
        	

        ?></a></h2>
        <div class="entry">
        	<?php
          	the_post_thumbnail();
						
           	the_excerpt(); 
          //the_content(); ?>
        </div>
      	<div class="clear">&nbsp;</div>
			</div><!-- end post -->

			<?php endwhile; ?>
		</div><!-- end leftCol -->
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
	<?php else : ?>

		<h2>Nothing found</h2>

	<?php endif; ?>
<?php get_footer(); ?>