<?php

/* Template Name: Contact */

get_header(); 

$currentpage = 'contact';

$page = new Pod('contact');
$page->findRecords(array('limit'=>'-1'));
$total_pages = $page->getTotalRows();

if( $total_pages>0 ) {
	$page->fetchRecord();
	
	$page_left_column_title   = $page->get_field('left_column_title');
	$page_left_column_text   = $page->get_field('left_column_text');
	$page_left_column_emails   = $page->get_field('left_column_emails'); // PICK!
		usort($page_left_column_emails, 'sortByOrder');
	
	$page_right_column_title   = $page->get_field('right_column_title');
	$page_right_column_text   = $page->get_field('right_column_text');
	$page_right_column_emails   = $page->get_field('right_column_emails'); // PICK!
		usort($page_right_column_emails, 'sortByOrder');
	
	$page_top_content = $page->get_field('top_content');
	
	$page_locations  = $page->get_field('locations'); // PICK!
		usort($page_locations, 'sortByOrder');
	
	$page_left_column_button_heading   = $page->get_field('left_column_button_heading');
	$page_left_column_button_label   = $page->get_field('left_column_button_label');
	$page_left_column_button_url   = $page->get_field('left_column_button_url');
}
?>
<div id="heroContainer" class="boxShadow clearfix contact">
	<div class="contact-left">
		<h1><?php echo $page_left_column_title; ?></h1>
    <p><?php echo $page_left_column_text; ?></p>
    <?php
		foreach( $page_left_column_emails as $k=>$v ) {
			
			echo '<a href="mailto:' . $v['name'] . '?subject=' . $v['subject'] .'">';
			if (!empty($v['industry'])) :
				echo $v['industry'];
			else :
				echo $v['name'];
			endif;
			echo '</a><br />';
		}
		?>
  </div>
  <div class="contact-right">
		<h1><?php echo $page_right_column_title; ?></h1>
    <p><?php echo $page_right_column_text; ?></p>
    <div id='cssmenu'>
      <ul>
         <li class='has-sub '><span id="contact_title">Customize by Industry</span>
            <ul id="contact_menu">
    
    <?php 
		//$perm_email = '';
		foreach( $page_right_column_emails as $k=>$v ) {
			//if( $v['id'] == 4 ) {
				//$perm_email = $v;
			//} else {
				echo '<li><a href="mailto:' . $v['name'] . '?subject=' . $v['subject'] .'"><span>' . $v['industry'] . '</span></a></li>';
			//}
		} 
    
		/*?>

    
    					<li><a href="mailto:<?php //echo $perm_email['name']; ?>?subject=From JustinBradley.com"><span><!--Default--></span></a></li>*/ ?>
            </ul>
         </li>
      </ul>
    </div>
    <?php //echo '<br /><span id="clickable_email"><a href="mailto:' . $perm_email['name'] . '?subject=From JustinBradley.com">' . $perm_email['name'] . '</a></span>'; ?>
    <?php echo '<br /><span id="clickable_email"><a href="#"></a></span>'; ?>
  </div>
  <?php if(!empty($page_top_content)) : ?>
  <div class="contact-bottom">
  	<?php echo $page_top_content; ?>
  </div>
  <?php endif; ?>
</div><!-- end #heroContainer -->


<?php
if ( count($page_locations) > 0 ) :
 	
	$locations = new Pod('locations');
	$locations->findRecords(array('orderby'=>'CAST(t.order as UNSIGNED) asc', 'limit'=>'-1'));
	//$people->findRecords('order ASC',-1);
	$total_locations = $locations->getTotalRows();
	if( $total_locations>0 ) : 
		while ( $locations->fetchRecord() ) : 
			$loc_id = $locations->get_field('id');
			foreach( $page_locations as $k=>$v ) : 
				if(isset($v['id']) && $v['id'] == $loc_id) :											
					$loc_image   = 	$locations->get_field('image');
					$loc_image = 		$loc_image[0]['guid'];
					$loc_address =	$locations->get_field('address');
					$loc_phone = 		$locations->get_field('phone');
					$loc_fax = 			$locations->get_field('fax');
					$loc_url = 			$locations->get_field('map_url');
					$loc_name = 		$v['name']			
?>
<div id="heroContainer" class="boxShadow clearfix contact-map">
	<div class="contact-map-left">
		<a href="<?php echo $loc_url;?>" target="_blank"><img src="<?php echo $loc_image; ?>" /></a>
  </div>
  <div class="contact-map-right">
		<h1><?php echo $loc_name; ?></h1>
    <br />
    <p><?php echo $loc_address; ?></p>
    <br />
    <?php echo (!empty($loc_phone)) ? '<p>Phone: ' . $loc_phone . '</p>' : ''; ?>
		<?php echo (!empty($loc_fax)) ? '<p>Fax: ' . $loc_fax . '</p>' : ''; ?>
			
  </div>
</div><!-- end #heroContainer -->
<?php  endif; endforeach; endwhile; endif; endif; ?>
<?php get_footer(); ?>