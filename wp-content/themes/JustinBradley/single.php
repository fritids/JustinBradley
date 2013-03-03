<?php

/* Template Name: Single */

get_header(); 

$currentpage = 'detail';
?>
<div id="mainContainer" class="boxShadow clearfix articles">
  <div id="main" class="clearfix">
  	<div class="leftCol">
      

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
      	
        <h2><?php the_title(); ?></h2>
        <?php $on = ''; ?>
        <div class="by-line">
        <?php 
          if ($author = get_post_meta( get_the_ID(), 'written_by', true ) ) {
            echo '<span class="author"><i>By</i> ' . $author . '</span>' ; 
            $on = 'ON ';
          }
        ?>
        <span class="post-date"><?php echo $on; ?><?php the_date(); ?></span>
      </div>
        <br /><br />
        <?php get_template_part( 'social' ); ?>
        <div class="entry">
        	<?php
          	the_content(); 
					?>
        </div>
      	<div class="clear">&nbsp;</div>
			</div>
      <?php endwhile; endif;  ?>
		</div>
	  <div class="rightCol">
      <?php get_template_part( 'social-follow' ); ?>
  	  <h2><em>Archives</em></h2>
  	  <ul class="archives">
  	    <?php wp_get_archives('type=monthly'); ?>
  	  </ul>
      <?php /* ?>
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
              <?php
              	<span class="button_heading">
                	<?php echo $top_button_heading; ?>        
                </span>
                <span class="button_paragraph"><?php echo $button_intro; ?></span> 
                <a class="button" href="<?php echo $button_url; ?>" name="<?php echo $label; ?>"><?php echo $label; ?></a>     
        <?php } ?> 
        </div>
        <?php */ ?>
    	</div>
  	</div>
  </div>
</div> 
<?php get_footer(); ?>