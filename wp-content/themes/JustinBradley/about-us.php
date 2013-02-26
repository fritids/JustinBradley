<?php

/* Template Name: About Us */

get_header(); 

$currentpage = 'about-us';

$page = new Pod('about_us');
$page->findRecords(array('limit'=>'-1'));
$total_pages = $page->getTotalRows();

if( $total_pages>0 ) {
	$page->fetchRecord();
	$page_overview   = $page->get_field('overview');
	$page_image      = $page->get_field('image');
	$page_image_name = 	$page_image[0]['post_name'];
		$page_image      = $page_image[0]['guid'];
}
?>
<?php //if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php /*
<div id="heroContainer" class="boxShadow clearfix about-us">
	<div class="hero clearfix">
		
      <div <?php post_class() ?> id="post-<?php the_ID(); ?>">
        <div class="entry">   
          <div class="rightCol">
            <h2><?php the_title(); ?></h2>
            <div class="intro">
              <?php the_content(); ?>
            </div>
            <?php if (!empty($page_overview)) : ?>
            <div class="overviews">
              <?php echo $page_overview; ?>
            </div>
            <?php endif; ?>
          </div>
          <div class="leftCol">
            <?php if(!empty($page_image)) : ?>
              <img src="<?php echo $page_image; ?>" alt="<?php echo $page_image_name; ?>" />
            <?php endif ?>
          </div>
        </div>
      </div>
    
  </div>
</div><!-- end #heroContainer --> */ ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php include(TEMPLATEPATH."/inc/top-right-aligned.php");?>
<?php endwhile; endif; ?>
<?php get_footer(); ?>