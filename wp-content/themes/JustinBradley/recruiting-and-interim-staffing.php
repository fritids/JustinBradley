<?php

/* Template Name: Recruiting and Interim Staffing */

get_header(); 

$currentpage = 'recruiting-and-interim-staffing';

$page = new Pod('recruiting_and_interim_staffing');
$page->findRecords(array('limit'=>'-1'));
$total_pages = $page->getTotalRows();

if( $total_pages>0 ) {
	$page->fetchRecord();
	$page_overview   = $page->get_field('overview');
	$page_left_column_text   = $page->get_field('left_column_text');
	$page_top_button_heading   = $page->get_field('top_button_heading');
	$page_top_button_label   = $page->get_field('top_button_label');
	$page_top_button_url   = $page->get_field('top_button_url');
	$page_left_column_headline  = $page->get_field('left_column_headline');
	$page_left_column_paragraph   = $page->get_field('left_column_paragraph');
	$page_center_column_text    = $page->get_field('center_column_text');
	$page_right_column_text   = $page->get_field('right_column_text');
	
	$page_image      = $page->get_field('image');
		$page_image      = $page_image[0]['guid'];
	$page_image_padding   = $page->get_field('image_padding');
		$page_image_padding = round($page_image_padding);
}
?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php include(TEMPLATEPATH."/inc/top-two-col.php");?>
	<?php include(TEMPLATEPATH."/inc/btm-three-col.php");?>
<?php endwhile; endif; ?> 
<?php get_footer(); ?>