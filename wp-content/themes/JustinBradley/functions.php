<?php
//remove_filter('template_redirect','redirect_canonical'); // Remove 301 redirects/Canonical URL Redirection
//update_option('siteurl','http://www.justinbradley.com/2012');
//update_option('home','http://www.justinbradley.com/2012');

add_theme_support( 'post-thumbnails' );
	
	// Add RSS links to <head> section
	automatic_feed_links();
	
	// Load jQuery
	if ( !is_admin() ) {
	   wp_deregister_script('jquery');
	   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"), false);
	   wp_enqueue_script('jquery');
	}
	
	// Clean up the <head>
	function removeHeadLinks() {
    	remove_action('wp_head', 'rsd_link');
    	remove_action('wp_head', 'wlwmanifest_link');
    }
    add_action('init', 'removeHeadLinks');
    remove_action('wp_head', 'wp_generator');
    
	// Declare sidebar widget zone
    if (function_exists('register_sidebar')) {
    	register_sidebar(array(
    		'name' => 'Sidebar Widgets',
    		'id'   => 'sidebar-widgets',
    		'description'   => 'These are widgets for the sidebar.',
    		'before_widget' => '<div id="%1$s" class="widget %2$s">',
    		'after_widget'  => '</div>',
    		'before_title'  => '<h2>',
    		'after_title'   => '</h2>'
    	));
    }

/**
* Trim a string to a given number of words
*
* @param $string
*   the original string
* @param $count
*   the word count
* @param $ellipsis
*   FALSE to not add "..."
*   or use a string to define other character
* @param $node
*   provide the node and we'll set the $node->
*  
* @return
*   trimmed string with ellipsis added if it was truncated
*/
function word_trim($string, $count, $ellipsis = TRUE){
  $words = explode(' ', $string);
  if (count($words) > $count){
    array_splice($words, $count);
    $string = implode(' ', $words);
    if (is_string($ellipsis)){
      $string .= $ellipsis;
    }
    elseif ($ellipsis){
      $string .= '&hellip;';
    }
  }
  return $string;
}


function new_excerpt_more($more) {
       global $post;
	return '&nbsp;&nbsp;<a href="'. get_permalink($post->ID) . '">Read&nbsp;the&nbsp;Full&nbsp;Story&nbsp;&nbsp;&rsaquo;</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');

function my_excerpt($excerpt_length = 20, $id = false, $echo = true) {
             
        $text = '';
       
              if($id) {
                    $the_post = & get_post( $my_id = $id );
                    $text = ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_post->post_content;
              } else {
                    global $post;
                    $text = ($post->post_excerpt) ? $post->post_excerpt : get_the_content('');
        }
             
                    $text = strip_shortcodes( $text );
                    $text = apply_filters('the_content', $text);
                    $text = str_replace(']]>', ']]&gt;', $text);
              $text = strip_tags($text);
           
                    $excerpt_more = ' ' . '...';
                    $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
                    if ( count($words) > $excerpt_length ) {
                            array_pop($words);
                            $text = implode(' ', $words);
                            $text = $text . $excerpt_more;
                    } else {
                            $text = implode(' ', $words);
                    }
            if($echo)
      echo apply_filters('the_content', $text);
            else
            return $text;
    }
     
    function get_my_excerpt($excerpt_length = 55, $id = false, $echo = false) {
     return my_excerpt($excerpt_length, $id, $echo);
    }



function get_search_results(){

	$search_count = 0;
	
	$search = new WP_Query("s=$s & showposts=-1");
	if($search->have_posts()) : while($search->have_posts()) : $search->the_post();
		$search_count++;
	endwhile; endif;
	
	return $search_count;
}

//Sort by function used to sort pick fields by order field. Usage syntax: usort($array_to_be_sorted, 'sortByOrder');
function sortByOrder($a, $b) {
	return $a['order'] - $b['order'];
}	
function sortByMenuOrder($a, $b) {
	return $a['menu_order'] - $b['menu_order'];
}	

?>