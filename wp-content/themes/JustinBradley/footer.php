<?php

$footer = new Pod('footer');
$footer->findRecords(array('limit'=>'-1'));
$total_footers = $footer->getTotalRows();

if( $total_footers>0 ) {
	$footer->fetchRecord();
	
	$col1_title   = $footer->get_field('col1_title');
	$col1_links   = $footer->get_field('col1_links'); // PICK
		usort($col1_links, 'sortByMenuOrder');
		
	$col2_title   = $footer->get_field('col2_title');
	$col2_links   = $footer->get_field('col2_links'); // PICK
		usort($col2_links, 'sortByMenuOrder');
	
	$col3_title   = $footer->get_field('col3_title');
	$col3_quicklinks   = $footer->get_field('col3_quicklinks'); // PICK
		usort($col3_quicklinks, 'sortByOrder');
		
	$col4_title   = $footer->get_field('col4_title');
	$facebook =  $footer->get_field('facebook');
	$twitter =  $footer->get_field('twitter');
	$linkedin =  $footer->get_field('linkedin');
	$call_title   = $footer->get_field('call_title');
	$call_number   = $footer->get_field('call_number');
	
}


?>
</div>
<div id="footerContainer" class="clearfix">
  <div id="footer" class="clearfix">
    <ul>
      <li><?php echo $col1_title; ?>
        <ul>
<?php 
	foreach( $col1_links as $k=>$v ) : 
?>
<li><a href="<?php echo $v['guid']; ?>"><?php echo $v['post_title']; ?></a></li>
<?php 
	endforeach;   
?>              
        </ul>
      </li>
    </ul>
    <ul>
      <li><?php echo $col2_title; //Services ?>
        <ul id="footer_column_2">
          <?php 
	foreach( $col2_links as $k=>$v ) : 
?>
<li><a href="<?php echo $v['guid']; ?>"><?php echo $v['post_title']; ?></a></li>
<?php 
	endforeach;   
?>  
        </ul>
      </li>
    </ul>
    <ul class="quicklinks">
      <li><?php echo $col3_title; ?>
        <ul>
<?php
  $footer_quick_links = new Pod('quick_links');
	$footer_quick_links->findRecords(array('orderby'=>'CAST(t.order as UNSIGNED) asc', 'limit'=>'-1'));
	$total_footer_quick_links = $footer_quick_links->getTotalRows();
	if( $total_footer_quick_links>0 ) : 
		while ( $footer_quick_links->fetchRecord() ) : 
			$footer_ql_id = $footer_quick_links->get_field('id');
			foreach( $col3_quicklinks as $k=>$v ) : 
				if(isset($v['id']) && $v['id'] == $footer_ql_id) :											
					$footer_ql_url   = 	$footer_quick_links->get_field('url');
						$footer_ql_url   = $footer_ql_url[0]['guid'];
?>                
                <li><a href="<?php echo $footer_ql_url; ?>"><?php echo $v['name']; ?></a></li>
   <?php  endif; endforeach; endwhile; endif; ?>
        </ul>
      </li>
    </ul>
    <ul class="footerLast"><?php // TODO: put in pod, make dynamic ?>
      <li><h4><?php echo $col4_title; ?></h4>
        <ul class="social">
          <li class="linkedin">
            <a href="<?php echo $linkedin; ?>" target="_blank">Find us on LinkedIn</a>
          </li>
          <li class="facebook">
            <a href="<?php echo $facebook; ?>" target="_blank">Find us on Facebook</a>
          </li>
          <li class="twitter">
            <a href="<?php echo $twitter; ?>" target="_blank">Follow us on Twitter</a>
          </li>
        </ul>
      </li>
      <li><h4><?php echo $call_title; ?></h4>
        <ul>
          <li class="white">
            <?php echo $call_number; ?>
          </li>
        </ul>
      </li>
    </ul>
  <span class="copyright clear">Copyright &copy; <?php echo date("Y");?> JustinBradley Inc. All Rights Reserved &bull; <a class="lightbox" href="#terms_of_use">Terms of Use &amp; Legal</a></span> <br /><br />
  </div>		
</div>
<div class="image_loader">
	<img src="<?php bloginfo('template_directory'); ?>/images/btn_grey_dn.png" />
  <img src="<?php bloginfo('template_directory'); ?>/images/btn_grey_ov.png" />
  <img src="<?php bloginfo('template_directory'); ?>/images/btn_blue_dn.png" />
  <img src="<?php bloginfo('template_directory'); ?>/images/btn_blue_ov.png" />
  <img src="<?php bloginfo('template_directory'); ?>/images/btn_contact_dn.png" />
  <img src="<?php bloginfo('template_directory'); ?>/images/btn_contact_ov.png" />
</div>
<div id="terms_of_use" class="image_loader">
<?php
	$tou = new Pod('terms_of_use');
	$tou->findRecords(array('limit'=>'-1'));
	$total_tou = $tou->getTotalRows();
	
	if( $total_tou>0 ) {
		$tou->fetchRecord();
		$tou_content   = $tou->get_field('content');
	}
?>
  <div id="tou_body">
  	<?php echo $tou_content; ?>
  </div>
</div>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory');?>/js/jquery.tinycarousel.min.js"></script>
    <script type="text/javascript" src="<?php bloginfo('template_directory');?>/js/jquery.fancybox.pack.js"></script>
    <script type="text/javascript">			
			$(document).ready(function(){

				setTimeout( function() {
					$('.social-buttons ul, .social-follow ul').animate({ opacity: 1 }, 500 );
				}, 3000 );	
				// Homepage carousel
				$('#heroContainer').tinycarousel({ pager: true, interval: true, intervaltime: 6000 });
				
				// Duplicate parent page menu items as "Overview" links in nav
				// Find if parent menu is active
				var li_class = $('ul.children').parent().attr('class');
				if(li_class.indexOf("current_page_item") != -1) {
					li_class = " current_page_item new_menu_overview ";
				} else {
					li_class = " new_menu_overview ";
				}
				// Clone and edit parent menu href
				var parent_href = $('ul.children').prev().clone();
				$(parent_href).text('Overview');
				// Prepend li with active/inactive class & href 
				$('ul.children').prepend('<li class="' + li_class + ' new_menu_overview"></li>');
				$('li.new_menu_overview').prepend(parent_href);
				
				// Remove About us from footer, change to 'overview' and place at top of list
				var menu_container = $('#footer_column_2');
				var about_menu_item = $('#footer_column_2').find('a[href*="page_id=18"]');
				$(about_menu_item).text('Overview');
				$(about_menu_item).prependTo(menu_container);			
			
			
				// Set up menu items to expand on click only (no hover state).
				$('ul.children').prev().click(function(e){
					e.preventDefault();
					$('ul.children').toggle();
				});
				// Close on click or mouseleave with timeout
				var navTimer=false;
				$('ul.children').mouseenter(function(){
						//mouse enter
						clearTimeout(navTimer);
				});
				$('li.page-item-18').mouseenter(function(){
						//mouse enter
						clearTimeout(navTimer);
				});
				$('ul.children').mouseleave(function(){
						navTimer = setTimeout(function(){
								$('ul.children').hide();
						},500);
				});
				$('li.page-item-18').mouseleave(function(){
						navTimer = setTimeout(function(){
								$('ul.children').hide();
						},500);
				});
				
				
				
				// Custom contact drop down
				var contact_title = $('#contact_title');
				var contact_menu = $('#contact_menu');
				var contactTimer=false;
				
				// when clicked, show menu & change title div to active state (toggle active class)
				$(contact_title).click(function(e){
					e.preventDefault();
					$(contact_menu).toggle();
					$(contact_title).toggleClass("active");
				});
				// close on mouseleave
				$(contact_menu).mouseenter(function(){
					clearTimeout(contactTimer);
				});
				$(contact_title).mouseenter(function(){
					clearTimeout(contactTimer);
				});
				$(contact_menu).mouseleave(function(){
					contactTimer = setTimeout(function(){
						$(contact_menu).hide();
						$(contact_title).removeClass("active");
					},500);
				});
				$(contact_title).mouseleave(function(){
					contactTimer = setTimeout(function(){
						$(contact_menu).hide();
						$(contact_title).removeClass("active");
					},500);
				});
				// when Contact page contact menu item is clicked, close menu, replace email address with address in a href, replace title text with location name
				$(contact_menu).find('li').each(function(){
					$(this).bind('click', function(e){
						e.preventDefault();
						var menu_item = $(this).clone();
						var ahref = $(menu_item).find('a');
						var new_title = $(ahref).attr('href');
						new_title = new_title.replace('mailto:', '');
						new_title = new_title.split('?')[0];
		
						$('#clickable_email').html(ahref);
						$('#clickable_email > a').text(new_title);
		
						$(contact_menu).toggle();
						$(contact_title).toggleClass("active");
					});
				});
				
				// Search Box behaviors
				/*$('#s').mouseenter(function(){
				//console.log($('#s').hasClass('hovered'));
				//console.log('mouseenter');
					var s_value = $(this).val();
					if(s_value == '' && !$(this).is(':focus'))
						$(this).val('Search');			
				});
				$('#s').mouseleave(function(){
				//console.log('mouseleave');
					var s_value = $(this).val();
					//console.log('mousleave');
					if(s_value != '' && !$(this).is(':focus'))
						$(this).val('');
				});
				$('#s').focusout(function(){
				//console.log('focusout');
					var s_value = $(this).val();
					if(s_value != 'Search')
						$(this).val('');
				});
				$('#s').click(function(){
				//console.log('click');
					var s_value = $(this).val();
					if(s_value == 'Search')
						$(this).val('');
					if($.browser.msie && parseInt($.browser.version, 10) == 7) {
						$('#s').css({ 
							//display: 			"block", 
							width: 				"150px",
							height: 			"28px", 
							
							padding: "0 0 0 10px",
							position: 		"relative",
							top: 					"-3px",
							outline:			"none",
							color:				"#898989", 
							border:				"1px solid #ccc"					
						});
					}
					
				});*/
				
				// Search Box behaviors #2 
				$('#search2_click_area').click(function(e){
					e.preventDefault();
					//console.log('clllicked');
					var s_value = $('#s2').val();
					//var is_active = $(this).hasClass('active');
					// If closed, open box
					if( !$('#s2').hasClass('active') ){
						$('#s2').addClass('active');
						$('#search2_click_area').addClass('active');
						$('#search2box').addClass('active');
						$('#s2').focus();
						//console.log('added active class');
					}
					// If open and text is entered, submit search
					else if( $('#s2').hasClass('active') && s_value != '' ){
						//console.log('submitting form');
						$('form.search2form').submit();
					}
				});
				var searchTimer=false;
				$('#search2form').mouseenter(function(){
						//console.log('resetting timer');
						clearTimeout(searchTimer);
				});
				$('#search2box').mouseleave(function(){
					if( !$('#s2').is(":focus") ){
						searchTimer = setTimeout(function(){
							$('#s2').removeClass('active');
							$('#search2_click_area').removeClass('active');
							$('#search2box').removeClass('active');
							$('#s2').val('');
							//console.log('closing due to mouseleave');
						},800);
					}
				});
				$('body').bind('click', function(e) {
					if($(e.target).closest('#s2').length == 0 &&
							$(e.target).closest('#search2form').length == 0 &&
								$(e.target).closest('#search2box').length == 0 &&
									$(e.target).closest('#search2_click_area').length == 0 ) {
						// click happened outside of menu, hide any visible menu items
						//console.log('click outside menu');
						$('#s2').removeClass('active');
						$('#search2_click_area').removeClass('active');
						$('#search2box').removeClass('active');
						$('#s2').val('');
					}
				});
			});
			$(document).ready(function() {
				$(".lightbox").fancybox({
					maxWidth	: 700,
					maxHeight	: 1000,
					fitToView	: true,
					width		: '70%',
					height		: '70%',
					autoSize	: false,
					closeClick	: true,
					openEffect	: 'none',
					closeEffect	: 'none'
				});
			});

</script>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-33125775-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</body>
</html>
