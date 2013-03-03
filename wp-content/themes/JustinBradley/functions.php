<?php
//remove_filter('template_redirect','redirect_canonical'); // Remove 301 redirects/Canonical URL Redirection
//update_option('siteurl','http://www.justinbradley.com/2012');
//update_option('home','http://www.justinbradley.com/2012');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
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



function fbrogmt() {

  if(is_single() ){ // Post
    if (have_posts()) : while (have_posts()) : the_post(); 

    $meta[]=get_the_title($post->post_title);// Gets the title
      $meta[]=get_permalink();// gets the url of the post
      $meta[]=get_option('blogname');//Site name
      $meta[]= the_excerpt_max_charlength(300) . '...'; //Description comes from the excerpt, because by using the_content, it will dish out the [caption id...]
      $meta[]= 'article';
      // $meta[]=get_the_image();//Gets the first image of a post/page if there is one  -- Remove this for now
      foreach (all_images() as $img_meta) { // The loop to dish out all the images meta tags explained lower
  echo $img_meta;
  }
    endwhile; endif; 
  }
  elseif(is_page() ){ // Page
    if (have_posts()) : while (have_posts()) : the_post(); 
      $meta[]=get_the_title($post->post_title);// Gets the title
      $meta[]=get_permalink();// gets the url of the post
      $meta[]=get_option('blogname');//Site name
      $meta[]= the_excerpt_max_charlength(300) . '...' ;  //Description comes from the excerpt, because by using the_content, it will dish out the [caption id...]
      $meta[]= 'article';
      // $meta[]=get_the_image();//Gets the first image of a post/page if there is one  -- Remove this for now
      foreach (all_images() as $img_meta) { // The loop to dish out all the images meta tags explained lower
  echo $img_meta;
  }
    endwhile; endif; 
  }
      elseif(is_category()) {
      global $post, $wp_query;
    $category_id = get_cat_ID(single_cat_title('',false));
    // Get the URL of this category
    $category_link = get_category_link( $category_id );
$term = $wp_query->get_queried_object();

if (is_plugin_active('wordpress-seo/wp-seo.php')) {  //checks for yoast seo plugin for description of category
        $metadesc = wpseo_get_term_meta( $term, $term->taxonomy, 'desc' );
        
        }
        else {
        
        $metadesc = category_description($category_id);
        }
    $meta[]=wp_title('', false);//Title
    $meta[]=$category_link;//URL
    $meta[]=get_option('blogname');//Site name
    $meta[]=$metadesc;//Description
    $meta[]= 'website';
    foreach (all_images() as $img_meta) { // The loop to dish out all the images meta tags explained lower
  echo $img_meta;
  }
  }
  elseif(is_home() || is_front_page()) {
    
    $meta[]=get_option('blogname');//Title
    $meta[]=get_option('siteurl');//URL
    $meta[]=get_option('blogname');//Site name
    $meta[]=get_option('blogdescription');//Description
    $meta[]= 'website';
  }

  else{
    
    $meta[]=get_option('blogname');//Title
    $meta[]=get_option('siteurl');//URL
    $meta[]=get_option('blogname');//Site name
    $meta[]=get_option('blogdescription');//Description
    $meta[]= 'article';
    
  }
  
  
  
  
  
  
  echo tags($meta);
}

/* Output of the meta tags */
function tags($meta){

  $tag.="<meta property='og:title' content='".$meta[0]."'/>\n"; 
  $tag.="<meta property='og:url' content='".$meta[1]."'/>\n";
  $tag.="<meta property='og:site_name' content='".$meta[2]."'/>\n";
  $tag.="<meta property=\"og:description\" content=\"$meta[3]\"/>\n";
  $tag.="<meta property='og:type' content='".$meta[4]."'/>\n";

  $tag.="<meta property='twitter:card' content='summary'/>\n";
  $tag.="<meta property='twitter:site' content='@JustinBradleyCo'/>\n";
  $tag.="<meta property='twitter:title' content='".$meta[0]."'/>\n"; 
  $tag.="<meta property='twitter:url' content='".$meta[1]."'/>\n";
  $tag.="<meta property=\"twitter:description\" content=\"$meta[3]\"/>\n";

  return $tag;
}



function all_images() { // Gets all the images of a post, and put them in the og:image meta tag to have the ability to choose what thumbnail to have on Facebook
  global $post;
  $the_images = array();
  if ( preg_match_all('/<img (.+?)>/', $post->post_content, $matches) ) { // Gets the images in the post content
          foreach ($matches[1] as $match) {
                  foreach ( wp_kses_hair($match, array('http')) as $attr)
                      $img[$attr['name']] = $attr['value'];
                 $the_images[] = "<meta property='og:image' content='".$img['src']."' />\n";
                 $the_images[] = "<meta property='twitter:image' content='".$img['src']."' />\n";
          }
      
  }
  else if (empty($the_images)) {   // Gets the image uploaded in the gallery
  $args = array(  
  'order'          => 'ASC',  
  'orderby'        => 'menu_order', 
  'post_type'      => 'attachment', 
  'post_parent'    => $post->ID,  
  'post_mime_type' => 'image',  
  'post_status'    => null, 
  'numberposts'    => -1, );  

  $attachments = get_posts($args);  
     
  foreach ($attachments as $attachment) {   
    
    $the_images[] = "<meta property='og:image' content='".wp_get_attachment_url($attachment->ID)."' />\n";

          } 
  }
  else {
   $the_images[] = "<meta property='og:image' content='".get_bloginfo('template_directory') . "/images/facebook-default.jpg' />\n"; // Default image if none 

  }
  return $the_images;
}


/* Extracts the content, removes tags, cuts it, removes the caption shortcode */

function the_excerpt_max_charlength($charlength) {
$content = get_the_content(); //get the content
$content = strip_tags($content); // strip all html tags
$regex = "#([[]caption)(.*)([[]/caption[]])#e"; // the regex to remove the caption shortcude tag
$content = preg_replace($regex,'',$content); // remove the caption shortcude tag
$content = preg_replace( '/\r\n/', ' ', trim($content) ); // remove all new lines
   $excerpt = $content;
   $charlength++;
   if(strlen($excerpt)>$charlength) {
       $subex = substr($excerpt,0,$charlength-5);
       $exwords = explode(" ",$subex);
       $excut = -(strlen($exwords[count($exwords)-1]));
       if($excut<0) {
            return substr($subex,0,$excut);
       } else {
            return $subex;
       }
       return "[...]";
   } else {
    return $excerpt;
   }
}
?>