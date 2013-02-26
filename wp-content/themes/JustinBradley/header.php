<!DOCTYPE html>
<html <?php language_attributes(); ?> xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta charset="<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width" />
	<?php if (is_search()) { ?>
	   <meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>

	<title>
		   <?php
		      if (function_exists('is_tag') && is_tag()) {
		         single_tag_title("Tag Archive for &quot;"); echo '&quot; - '; }
		      elseif (is_archive()) {
		         wp_title(''); echo ' Archive - '; }
		      elseif (is_search()) {
		         echo 'Search for &quot;'.wp_specialchars($s).'&quot; - '; }
		      elseif (!(is_404()) && (is_single()) || (is_page())) {
		         wp_title(''); echo ' - '; }
		      elseif (is_404()) {
		         echo 'Not Found - '; }
		      if (is_home()) {
		         bloginfo('name'); echo ' - ' . 'Home'; /*bloginfo('description');*/ }
		      else {
		          bloginfo('name'); }
		      if ($paged>1) {
		         echo ' - page '. $paged; }
		   ?>
	</title>
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
  <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory');?>/css/jquery.fancybox.css" media="screen" />
  <!--[if IE]>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory');?>/css/ie.css" />
	<![endif]-->
  <!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory');?>/css/ie7.css" />
	<![endif]-->
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
      <script type="text/javascript" src="http://use.typekit.com/zhd5axa.js"></script>
		<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
</head>
<body <?php body_class(); ?> >
	<div id="page-wrap" class="clearfix">
		<div id="header">
        <a href="<?php echo home_url(); ?>/">
        	<img src="<?php bloginfo('template_directory'); ?>/images/JustinBradley_logo.jpg" width="226" height="41" alt="JustinBradley" />
        </a>  
      <ul class="pagenav">
      <?php 
				$args = array(
					'depth'        => 0,
					'show_date'    => '',
					'date_format'  => get_option('date_format'),
					'child_of'     => 0,
					'exclude'      => 5,
					'include'      => '',
					'title_li'     => '',
					'echo'         => 1,
					'authors'      => '',
					'sort_column'  => 'menu_order, post_title',
					'link_before'  => '',
					'link_after'   => '',
					'walker'       => '' ); 
		 		wp_list_pages($args ); 
		 	?>
      </ul>
      <div id="search2box" class="search2box">
        <form class="search2form" action="<?php echo site_url(); ?>/" method="get"> 
          <input id="s2" type="text" placeholder="" value="" name="s" onBlur="">
          <input type="image" id="search2_click_area" src="<?php bloginfo('template_directory'); ?>/images/search_icon.jpg" width="13" height="13" border="0" alt="Submit Form">
        </form>
      </div>
		</div>