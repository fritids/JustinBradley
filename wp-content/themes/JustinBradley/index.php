<?php

/* Template Name: Home Page */

get_header(); 

$currentpage = 'home-page';

$page = new Pod('home_page');
$page->findRecords(array('limit'=>'-1'));
$total_pages = $page->getTotalRows();

if( $total_pages>0 ) {
	$page->fetchRecord();
	
	$page_banner   = $page->get_field('banner'); // PICK
		usort($page_banner, 'sortByOrder');
	$page_box1_title   = $page->get_field('box1_title');
	$page_box1_copy   = $page->get_field('box1_copy');
	$page_box1_url   = $page->get_field('box1_url');
	$page_box2_title   = $page->get_field('box2_title');
	$page_box2_copy   = $page->get_field('box2_copy');
	$page_box2_url   = $page->get_field('box2_url');
	$page_box3_title   = $page->get_field('box3_title');
	$page_box3_copy   = $page->get_field('box3_copy');
	$page_box3_url   = $page->get_field('box3_url');

	$page_quick_links_title   = $page->get_field('quick_links_title');
	$page_quick_links  = $page->get_field('quick_links'); // PICK
		usort($page_quick_links, 'sortByOrder');
}
?>
      <div id="heroContainer" class="boxShadow home">
        <div class="viewport">
			<ul class="overview">
<?php
$banner = new Pod('banner');
$banner->findRecords(array('orderby'=>'CAST(t.order as UNSIGNED) asc', 'limit'=>'-1'));
$total_banner = $banner->getTotalRows(); 
if( $total_banner>0 ) :  
	while ( $banner->fetchRecord() ) :  
		$b_id = $banner->get_field('id');
		$b_url = $banner->get_field('url');
		$b_image = $banner->get_field('image');
			$b_image = $b_image[0]['guid'];
		foreach( $page_banner as $k=>$v ) : 
			if(isset($v['id']) && $v['id'] == $b_id) :	
?>
<li>
	<?php if(!empty($b_url)) : ?>
		<a href="<?php echo $b_url; ?>">
  <?php endif; ?>
  		<img src="<?php echo $b_image; ?>" />
	<?php if(!empty($b_url)) : ?>
  	</a>
  <?php endif; ?>
  </li>
<?php 
  endif; endforeach;  endwhile; endif;   
?>              
							
                
		</ul>
	</div>
	<ul class="pager">
		<?php

		if( $total_banner > 0 ) :
			$ii = 0;
			for ( $ii=0;$ii<$total_banner;$ii++) {
				?>
				<li><a rel="<?php echo $ii ?>" href="#" class="pagenum <?php echo $act ?>">&bull;</a></li>
				<?php
			}
			
		endif;  
		?>
		</ul>
      </div><!-- end #heroContainer -->
      <div id="homeMainContainer" class="clearfix">
      	<div>
        	<a href="<?php echo $page_box1_url; ?>" class="box aBox boxShadow"><h2><?php echo $page_box1_title; ?></h2><p><?php echo $page_box1_copy; ?></p><span class="link">Learn More </span></a>
          <a href="<?php echo $page_box2_url; ?>" class="box bBox boxShadow"><h2><?php echo $page_box2_title; ?></h2><p><?php echo $page_box2_copy; ?></p><span class="link">Learn More </span></a>
          <a href="<?php echo $page_box3_url; ?>" class="box cBox boxShadow"><h2><?php echo $page_box3_title; ?></h2><p><?php echo $page_box3_copy; ?></p><span class="link">Learn More </span></a>
            <div class="hotLinks clear">
<?php 
if ( count($page_quick_links) > 0 ) :
?>
            	<h2><?php echo $page_quick_links_title; ?></h2>
                <ul>
<?php
  $quick_links = new Pod('quick_links');
	$quick_links->findRecords(array('orderby'=>'CAST(t.order as UNSIGNED) asc', 'limit'=>'-1'));
	$total_quick_links = $quick_links->getTotalRows();
	if( $total_quick_links>0 ) : 
		while ( $quick_links->fetchRecord() ) : 
			$ql_id = $quick_links->get_field('id');
			foreach( $page_quick_links as $k=>$v ) : 
				if(isset($v['id']) && $v['id'] == $ql_id) :											
					$ql_url   = 	$quick_links->get_field('url');
						$ql_url   = $ql_url[0]['guid'];
?>                
                <li><a href="<?php echo $ql_url; ?>"><?php echo $v['name']; ?></a></li>
   <?php  endif; endforeach; endwhile; endif; ?>
                </ul>
<?php endif; ?>
            </div>
        </div>
        
        
        <div class="newsReel boxShadow">
        	<h2>News</h2>
            <ul>
            <?php
							$x= 0; 
							if (have_posts()) : while (have_posts() && $x<=2) : the_post(); ?>
            
            
            	<li><span class="date"><?php the_date(); ?></span><a href="<?php the_permalink() ?>"><?php the_title(); ?></a><?php my_excerpt(); ?></li>
               <?php $x++; ?>
                
            <?php endwhile; endif; ?>
            </ul>
            <a class="seeAllNews" href="about-us/news">See All News</a>
        </div>
      </div><!-- close homeMainContainer -->
    </div>
<?php get_footer(); ?>