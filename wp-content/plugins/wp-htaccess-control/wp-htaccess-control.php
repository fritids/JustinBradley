<?php
/*
Plugin Name: WP htaccess Control
Plugin URI: http://dardna.com/wp-htaccess-control
Description: Interface to customize the permalinks (author, category, archives and pagination) and htaccess file generated by Wordpress.
Version: 3.2.1
Author: António Andrade
Author URI: http://antonioandra.de
*/
/*  Copyright 2010-2012  António Andrade  (email : antonio@antonioandra.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/*	Removal of the Category Base is based on WP No Category Base by Saurabh Gupta, 2008
	Search redirection is based on Nice Search by Mark Jaquith, 2011  */

if (!class_exists("WPhtc")) {
	class WPhtc {
	
		public $data;
		
		function WPhtc() { 
			$this->data=get_option('WPhtc_data');
			}
		function init(){
			# set locale
			$currentLocale = get_locale();
			if(!empty($currentLocale)) {
				$moFile = dirname(__FILE__) . "/lang/wp-htaccess-control-" . $currentLocale . ".mo";
				if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('wp-htaccess-control', $moFile);
				}
			}
		function search_template_redirect(){
				# redirect "?s=*" to "/search-base/*"
				if($_GET['s']){
					wp_redirect( home_url( $this->data['custom_search_permalink']. "/" .  rawurlencode(get_query_var( 's' )) ) );
					}
					
				# rewrite query
				if(strpos($_SERVER["REQUEST_URI"], $this->data['custom_search_permalink'])){
					global $wp_query;
					if(strpos($_SERVER["REQUEST_URI"], '/feed')){
						preg_match("/feed\/(feed|rdf|rss|rss2|atom)?/",$_SERVER["REQUEST_URI"], $feed);
						if($feed[1]){
							$format="&feed=".$feed[1];
							}
						else{
							$format="&feed=feed";
							}
						}
					$page_base=($this->data['cpp']!='')?$this->data['cpp']:'page';
					
					# in need of better regex					
					if(!strpos($_SERVER["REQUEST_URI"], '/feed')&&!strpos($_SERVER["REQUEST_URI"],$page_base)){
						$pattern="/\/".$this->data['custom_search_permalink']."\/(.+)/";
						}
					else{
						$pattern="/\/".$this->data['custom_search_permalink']."\/(.+)\/feed|".$page_base."?/";
						}
						
					$pattern="/\/".$this->data['custom_search_permalink']."\/(.+)/";
					preg_match($pattern,$_SERVER["REQUEST_URI"], $matches);
					$results=split("/",$matches[1]);
					if($results[1]==$page_base){
						$page="&paged=".$results[2];
						}
					$wp_query=new WP_Query('s='.$results[0].$page.$format );
					}
			}
		# return get_search_query on custom search base
		function get_search_query_filter($query){
			
			if($this->data['custom_search_permalink']!=''&&strpos($_SERVER["REQUEST_URI"], $this->data['custom_search_permalink'])){
				$page_base=($this->data['cpp']!='')?$this->data['cpp']:'page';
				# in need of better regex
				if(!strpos($_SERVER["REQUEST_URI"], '/feed')&&!strpos($_SERVER["REQUEST_URI"],"/".$page_base)){
					$pattern="/\/".$this->data['custom_search_permalink']."\/(.+)/";
					}
				else{
					$pattern="/\/".$this->data['custom_search_permalink']."\/(.+)\/feed|".$page_base."?/";
					}
				preg_match($pattern,$_SERVER["REQUEST_URI"], $matches);
				$results=split("/",$matches[1]);
				return urldecode($results[0]);
				}
			return $query;
			}
		function search_feed_link($link){
			
			$link=str_replace("search",$this->data['custom_search_permalink'],$link);
			return $link;
			}
		function check_first_run(){
			
			
			# MIGRATE OPTIONS
			
			// deprecating old category base removal
			if($this->data['remove_category_base']){
				unset($this->data['remove_category_base']);
				$this->data['remove_taxonomy_base']['category']=true;
				}
			// deprecating old category archive 
			if($this->data['category_archives']){
				unset($this->data['category_archives']);
				$this->data['create_archive']['category']=true;
				}
			// deprecating old tag archive 
			if($this->data['tag_archives']){
				unset($this->data['tag_archives']);
				$this->data['create_archive']['post_tag']=true;
				}
			if($this->data['remove_category_base'] || $this->data['category_archives'] || $this->data['tag_archives']){
				update_option('WPhtc_data',$this->data);
				}
				
			# flush rules to get some data filled on first usage
			if(!isset($this->data['htaccess_original'])){
				$this->refresh_rewrite_rules();
				}
			}
		# Flush rules
		function refresh_rewrite_rules(){
				global $wp_rewrite;
				$wp_rewrite->flush_rules();
				}
		function refresh_rewrite_rules_later(){
				wp_schedule_single_event(time(), 'flush_event');
				}
		# Filter pagination links generated by Wordpress
		function filter_get_pagenum_link($result){
			$result=preg_replace("/".urlencode($this->data['cpp'])."\/[0-9]+[\/]?/","",$result);
			$result=str_replace("page",urlencode($this->data['cpp']),$result);
			return $result;
			}
		# Filter link generated by get_author_posts_url() to use the Custom Author Permalink
		function filter_author_link($link){
			
			if($this->data['remove_author_base']){
				$link=str_replace("author/","",$link);
				}
			else if(isset($this->data['cap']) && $this->data['cap']!=''){
				$link=str_replace("author",urlencode($this->data['cap']),$link);
				}
			return $link;
			}
		
		function filter_redirect_canonical($requested_url){
			global $wp;
			
			# Disable canonical redirection on urls using custom pagination permalink
			if(isset($this->data['cpp'])&&$this->data['cpp']!=''&&get_query_var('paged') > 1&&preg_match("/".urlencode($this->data['cpp'])."/",$wp->request)){
				return false;
				}
			else{
				return $requested_url;
				}
			}
		
		# ancestors nesting method
		function term_ancestors($tax,$id){
				$term=get_term($id,$tax);
				$ancestor=$term->slug;
				if($term->parent!=0){
					$ancestor=$this->term_ancestors($tax,$term->parent)."/".$ancestor;
					}
				return $ancestor;
				}
		# Rewrite Rules: Add Category + Tag + Author Archives; Custom Pagination; Custom Author Base
		function filter_rewrite_rules($rewrite_rules){
			
			
			$page_base=($this->data['cpp']!='')?$this->data['cpp']:'page';
			
			if($this->data['create_archive'] || $this->data['remove_taxonomy_base']){
				foreach (get_taxonomies('','objects') as $taxonomy){
					if(!$taxonomy->rewrite || ( !$this->data['remove_taxonomy_base'][$taxonomy->name] && !$this->data['create_archive'][$taxonomy->name] )){continue;}
					$terms=get_terms($taxonomy->name);
					foreach($terms as $term){
						$base=$this->data['remove_taxonomy_base'][$taxonomy->name]?"":$taxonomy->rewrite->slug."/";
						if($term->parent!=0){
							$ancestors=$this->term_ancestors($taxonomy->name,$term->parent)."/";
							}
						else{
							$ancestors="";
							}
						// category hack
						$tax_name=($taxonomy->name=="category")?"category_name":$taxonomy->name;
						
						# create archives
						if($this->data['create_archive'][$tax_name]){
							#year
							$new_rules[$base.$ancestors.'('.$term->slug.')/([0-9]{4})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&year=$matches[2]';
							$new_rules[$base.$ancestors.'('.$term->slug.')/([0-9]{4})/'.$page_base.'/?([0-9]{1,})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&year=$matches[2]&paged=$matches[3]';
							#year/month
							$new_rules[$base.$ancestors.'('.$term->slug.')/([0-9]{4})/([0-9]{2})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&year=$matches[2]&monthnum=$matches[3]';
							$new_rules[$base.$ancestors.'('.$term->slug.')/([0-9]{4})/([0-9]{2})/'.$page_base.'/?([0-9]{1,})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&year=$matches[2]&monthnum=$matches[3]&paged=$matches[4]';
							#year/month/day
							$new_rules[$base.$ancestors.'('.$term->slug.')/([0-9]{4})/([0-9]{2})/([0-9]{2})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]';
							$new_rules[$base.$ancestors.'('.$term->slug.')/([0-9]{4})/([0-9]{2})/([0-9]{2})/'.$page_base.'/?([0-9]{1,})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&paged=$matches[5]';
							}
						# create rewrite rules with tax base	
						if(!$base){
							$new_rules[$ancestors.'('.$term->slug.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?'.$tax_name.'=$matches[1]&feed=$matches[2]';
							$new_rules[$ancestors.'('.$term->slug.')/page/?([0-9]{1,})/?$'] = 'index.php?'.$tax_name.'=$matches[1]&paged=$matches[2]';
							$new_rules[$ancestors.'('.$term->slug.')/?$'] = 'index.php?'.$tax_name.'=$matches[1]';
							}
						$rewrite_rules =  $new_rules + $rewrite_rules;
						}
					}
				}
			
			# Author Archives
			if($this->data['remove_author_base']){
				$blogusers = get_users('who=authors');
					foreach ($blogusers as $user) {
						
						$new_rules = array(
									'('.$user->user_nicename.')/([^/]+)/([0-9]{4})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]',
									'('.$user->user_nicename.')/([^/]+)/([0-9]{4})/'.$page_base.'/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&paged=$matches[3]',
									
									'('.$user->user_nicename.')/([^/]+)/([0-9]{4})/([0-9]{2})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]',
									'('.$user->user_nicename.')/([^/]+)/([0-9]{4})/([0-9]{2})/'.$page_base.'/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&paged=$matches[4]',
									
									'('.$user->user_nicename.')/([^/]+)/([0-9]{4})/([0-9]{2})/([0-9]{2})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]',
									'('.$user->user_nicename.')/([^/]+)/([0-9]{4})/([0-9]{2})/([0-9]{2})/'.$page_base.'/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&paged=$matches[5]');
									
									$rewrite_rules = $new_rules + $rewrite_rules;
					}
				}
			else if(isset($this->data['author_archives'])){
				$author_base = ($this->data['cap']!='')?$this->data['cap']:'author';
				$new_rules = array(
									$author_base.'/([^/]+)/([0-9]{4})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]',
									$author_base.'/([^/]+)/([0-9]{4})/'.$page_base.'/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&paged=$matches[3]',
									
									$author_base.'/([^/]+)/([0-9]{4})/([0-9]{2})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]',
									$author_base.'/([^/]+)/([0-9]{4})/([0-9]{2})/'.$page_base.'/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&paged=$matches[4]',
									
									$author_base.'/([^/]+)/([0-9]{4})/([0-9]{2})/([0-9]{2})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]',
									$author_base.'/([^/]+)/([0-9]{4})/([0-9]{2})/([0-9]{2})/'.$page_base.'/?([0-9]{1,})/?$' => 'index.php?author_name=$matches[1]&year=$matches[2]&monthnum=$matches[3]&day=$matches[4]&paged=$matches[5]');
									
				$rewrite_rules = $new_rules + $rewrite_rules;
				}
			
			# Custom Pagination; Custom Author Permalink
			if((isset($this->data['cap'])&&$this->data['cap']!='')||(isset($this->data['cpp'])&&$this->data['cpp']!='')){
				$rewrite_rules=serialize($rewrite_rules);
				# Author
				if(isset($this->data['cap'])&&$this->data['cap']!=''){
					$rewrite_rules=str_replace('author/',$this->data['cap'].'/',$rewrite_rules);
					}
				# Pagination
				if(isset($this->data['cpp'])&&$this->data['cpp']!=''){
					$rewrite_rules=str_replace('page/',$this->data['cpp'].'/',$rewrite_rules);
					}
				# Search
				if(isset($this->data['custom_search_permalink'])&&$this->data['custom_search_permalink']!=''){
					$rewrite_rules=str_replace('search/',$this->data['custom_search_permalink'].'/',$rewrite_rules);
					}
						
				$rewrite_rules=unserialize(preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'",$rewrite_rules));
				}
			return $rewrite_rules;
			}
			
		# Adding our Custom Author Base to the sitemap generated by Google XML Sitemaps
		# Adapted from sitemap-core.php (line ~2036)
		function set_sm(){
			
			if(class_exists('GoogleSitemapGenerator')&&$this->data['cap']!=''){
				$generatorObject = &GoogleSitemapGenerator::GetInstance();
				if ($generatorObject != null){
					global $wpdb;
					$sql = "SELECT DISTINCT
							p.ID,
							u.user_nicename,
							MAX(p.post_modified_gmt) AS last_post
						FROM
							{$wpdb->users} u,
							{$wpdb->posts} p
						WHERE
							p.post_author = u.ID
							AND p.post_status = 'publish'
							AND p.post_type = 'post'
							AND p.post_password = ''
							" . (floatval($wp_version) < 2.1?"AND p.post_date_gmt <= '" . gmdate('Y-m-d H:i:59') . "'":"") . "
						GROUP BY
							u.ID,
							u.user_nicename";
					$authors = $wpdb->get_results($sql);	
					if($authors && is_array($authors)) {
						foreach($authors as $author) {
							$url=get_bloginfo('home')."/".$this->data['cap']."/".$author->user_nicename."/";
							$generatorObject->AddUrl($url,$generatorObject->GetTimestampFromMySql($author->last_post),$generatorObject->GetOption("cf_auth"),$generatorObject->GetOption("pr_auth"));
							}
						}
					}
				}
			}
		# Adding rewrite pairs to the .htaccess generated by Wordpress, everytime the rules are flush
		function wp_rewrite_rules($wp_rewrite){
			
			# Keeping a copy of the generated htaccess in our option array for later reviewing
			$this->data['cur_hta']='# BEGIN Wordpress<br/>'.nl2br($wp_rewrite->mod_rewrite_rules())."# END Wordpress";
			update_option('WPhtc_data',$this->data);
			return $wp_rewrite;
			}
		# Adding Custom Rules to htaccess file generated by Wordpress, everytime the rules are flush
		function hta_rules($rules){
			
			# keeping original hta
			if(!isset($this->data['htaccess_original'])){
				$this->data['htaccess_original']=$rules;
				update_option('WPhtc_data',$this->data);
				}
			# Maintenance Mode
			if($this->data['maintenance_active']&&isset($this->data['maintenance_ips'][0])){
				$no_ips=count($this->data['maintenance_ips']);
				$new_rules.="\n# WPhtc: Begin Maintenance Mode\n";
				# redirect
				if($this->data['maintenance_redirection']!=''){
					$new_rules.="RewriteEngine on\n";
					$new_rules.="RewriteCond %{REQUEST_URI} !\.(jpe?g|png|gif) [NC]\n";
					for($i=0;$i<$no_ips;$i++){
						$new_rules.="RewriteCond %{REMOTE_HOST} !^".str_replace(".","\.",trim($this->data['maintenance_ips'][$i]))."\n";
						}
					if(substr($this->data['maintenance_redirection'],0,1)=="/"){
						$new_rules.="RewriteCond %{REQUEST_URI} !".$this->data['maintenance_redirection']."$ [NC]\n";
						}
					$new_rules.="RewriteRule .* ".$this->data['maintenance_redirection']." [R=302,L]\n";
					}
				# no redirection
				else{
					$new_rules.="order deny,allow\n";
					$new_rules.="deny from all\n";
					foreach($this->data['maintenance_ips'] as $ip){
						$new_rules.="allow from ".$ip."\n";
						}
					}
				$new_rules.="# WPhtc: End Maintenance Mode\n";
				}
				
			# Login Control
			if($this->data['login_disabled']){
				$no_ips=count($this->data['login_ips']);
				$new_rules.="\n# WPhtc: Begin Login Control (start deleting here if you're having trouble logging in)\n";
				# redirect
				$new_rules.="RewriteEngine on\n";
				$new_rules.="RewriteCond %{REQUEST_URI} .wp-login\.php* [NC]\n";
				if($this->data['login_half_mode']){
					$new_rules.="RewriteCond %{REQUEST_METHOD} !=POST\n";
					$new_rules.="RewriteCond %{QUERY_STRING} !action=(logout|lostpassword|postpass|retrievepassword|resetpass|rp)*\n";
					}
				if($no_ips>0){
					for($i=0;$i<$no_ips;$i++){
						$new_rules.="RewriteCond %{REMOTE_HOST} !^".str_replace(".","\.",trim($this->data['login_ips'][$i]))."\n";
						}
					}
				$new_rules.="RewriteRule .* ".$this->data['login_redirection']." [R=301,L]\n";
				$new_rules.="# WPhtc: End Login Control Mode (stop deleting here if you're having trouble logging in)\n";
				}
			# Custom htaccess
			if($this->data['hta']){
				$new_rules.="\n# WPhtc: Begin Custom htaccess\n";
				$new_rules.=stripslashes($this->data['hta'])."\n";
				$new_rules.="# WPhtc: End Custom htaccess\n";
				}
			# htaccess suggestions
			if($this->data['disable_serversignature']){
				$new_rules.="\n# WPhtC: Disable ServerSignature on generated error pages\n";
				$new_rules.="ServerSignature Off\n";
				}
			if($this->data['admin_email']){
				$new_rules.="\n# WPhtC: Set admin email\n";
				$new_rules.="SetEnv SERVER_ADMIN ".$this->data['admin_email']."\n\n";
				}
			if($this->data['disable_indexes']){
				$new_rules.="\n# WPhtC: Disable directory browsing\n";
				$new_rules.="Options All -Indexes\n";
				}
			if($this->data['up_limit']){
				$new_rules.="\n# WPhtC: Limit upload size to ".$this->data['up_limit']." MB\n";
				$new_rules.="LimitRequestBody ".($this->data['up_limit']*1024*1024)." \n";
				}
			if($this->data['redirect_500']){
				$new_rules.="\n# WPhtC: Setting 500 Error page\n";
				$new_rules.="ErrorDocument 500 ".$this->data['redirect_500']."\n";
				}
			if($this->data['redirect_403']){
				$new_rules.="\n# WPhtC: Setting 403 Error page\n";
				$new_rules.="ErrorDocument 403 ".$this->data['redirect_403']."\n";
				}
			if($this->data['protect_wp_config']){
				$new_rules.="\n# WPhtC: Protect WP-config.php\n";
				$new_rules.="<files wp-config.php>\n";
				$new_rules.="order allow,deny\n";
				$new_rules.="deny from all\n";
				$new_rules.="</files>\n";
				}
			if($this->data['protect_htaccess']){
				$new_rules.="\n# WPhtC: Protect .htaccess file\n";
				$new_rules.="<files ~ \"^.*\.([Hh][Tt][Aa])\">\n";
				$new_rules.="order allow,deny\n";
				$new_rules.="deny from all\n";
				$new_rules.="</files>\n";
				}
			if($this->data['protect_comments']){
				$new_rules.="\n# WPhtC: Protect comments.php\n";
				$new_rules.="RewriteCond %{REQUEST_METHOD} POST\n";
				$new_rules.="RewriteCond %{REQUEST_URI} .wp-comments-post\.php*\n";
				$new_rules.="RewriteCond %{HTTP_REFERER} !.*".get_bloginfo('home').".* [OR]\n";
				$new_rules.="RewriteCond %{HTTP_USER_AGENT} ^$\n";
				$new_rules.="RewriteRule (.*) ^http://%{REMOTE_ADDR}/$ [R=301,L]\n";
				}
			if($this->data['disable_hotlink']){
				$new_rules.="\n# WPhtC: Disable image hotlinking\n";
				$new_rules.="<IfModule mod_rewrite.c>\n";
				$new_rules.="RewriteEngine on\n";
				$new_rules.="RewriteCond %{HTTP_REFERER} !^$\n";
				$new_rules.="RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?".str_ireplace(array("http://","www."),"",get_bloginfo("url"))."/.*$ [NC]\n";
				$new_rules.="RewriteRule \.(jpg|jpeg|png|gif)$ ".$this->data['disable_hotlink']." [NC,R,L]\n";
				$new_rules.="</IfModule>\n";
				}
			if($this->data['disable_file_hotlink_ext']){
				$redir = $this->data['disable_file_hotlink_redir'] ? $this->data['disable_file_hotlink_redir'] : "_";
				$new_rules.="\n# WPhtC: Disable file hotlinking\n";
				$new_rules.="<IfModule mod_rewrite.c>\n";
				$new_rules.="RewriteEngine on\n";
				$new_rules.="RewriteCond %{HTTP_REFERER} !^$\n";
				$new_rules.="RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?".str_ireplace(array("http://","www."),"",get_bloginfo("url"))."/.*$ [NC]\n";
				$new_rules.="RewriteRule \.(".str_replace(" ","|",$this->data['disable_file_hotlink_ext']).")$ ".$redir." [NC,R,L]\n";
				$new_rules.="</IfModule>\n";
				}
			if($this->data['canon']){
				$url=str_ireplace(array("http://","www."),"",get_bloginfo("url"));
				$domain=explode("/",$url);
				$escaped_domain=str_ireplace(".","\.",$domain[0]);
				if($this->data['canon']=='www'){
					$siteurl=get_option('siteurl');
					if (!preg_match('/^http:\/\/www\./', $siteurl)) {
						$siteurl=str_replace("http://","http://www.",$siteurl);
						update_option('siteurl',$siteurl);
						}
					$homeurl=get_option('home');
					if (!preg_match('/^http:\/\/www\./', $homeurl)) {
						$homeurl=str_replace("http://","http://www.",$homeurl);
						update_option('home',$homeurl);
						}
					}
				else if($this->data['canon']=='simple'){
					$siteurl=get_option('siteurl');
					if (preg_match('/^http:\/\/www\./', $siteurl)) {
						$siteurl=str_replace("http://www.","http://",$siteurl);
						update_option('siteurl',$siteurl);
						}
					$homeurl=get_option('home');
					if (preg_match('/^http:\/\/www\./', $homeurl)) {
						$homeurl=str_replace("http://www.","http://",$homeurl);
						update_option('home',$homeurl);
						}
					}
				}
			if($this->data['gzip']){
					$new_rules.="\n# WPhtC: Setting mod_gzip\n";
					$new_rules.="<ifModule mod_gzip.c>\n";
					$new_rules.="mod_gzip_on Yes\n";
					$new_rules.="mod_gzip_dechunk Yes\n";
					$new_rules.="mod_gzip_item_include file \.(html?|txt|css|js|php|pl)$\n";
					$new_rules.="mod_gzip_item_include handler ^cgi-script$\n";
					$new_rules.="mod_gzip_item_include mime ^text/.*\n";
					$new_rules.="mod_gzip_item_include mime ^application/x-javascript.*\n";
					$new_rules.="mod_gzip_item_exclude mime ^image/.*\n";
					$new_rules.="mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*\n";
					$new_rules.="</ifModule>\n";
				}
			if($this->data['deflate']){
					$new_rules.="\n# WPhtC: Setting mod_deflate\n";
					$new_rules.="<IfModule mod_deflate.c>\n";
					$new_rules.="AddOutputFilterByType DEFLATE text/html text/plain text/xml application/xml application/xhtml+xml text/javascript text/css application/x-javascript\n";	
					$new_rules.="BrowserMatch ^Mozilla/4 gzip-only-text/html\n";
					$new_rules.="BrowserMatch ^Mozilla/4.0[678] no-gzip\n";
					$new_rules.="BrowserMatch bMSIE !no-gzip !gzip-only-text/html\n";
					$new_rules.="Header append Vary User-Agent env=!dont-vary\n";
					$new_rules.="</IfModule>\n";
				}
			if($this->data['wp_hta']&&trim($this->data['wp_hta'])!=''){
				$new_rules.="\n".$this->data['wp_hta'];
				}
			else{
				$new_rules.="\n".$rules;
				}
			return $new_rules;
			}
		
		# Filter Term Link
		function filter_term_link($termlink, $term, $taxonomy){
			$txs=get_taxonomies(array('name'=>$taxonomy),"object");
			foreach($txs as $t){
				//return str_replace($t->rewrite['slug']."/","",$termlink);
				if($term->parent!=0){
					return get_bloginfo('home')."/".$this->term_ancestors($taxonomy,$term->parent)."/".$term->slug;
					}
				else{
					return get_bloginfo('home')."/".$term->slug;
					}
				}
			}
		
		#  Filter Taxonomy Base
		function remove_taxonomy_base_from_rewrite_rules($rules){
			# Let's remove every taxonomy rule here, we'll reacreate them at filter_rewrite_rules()
			return array();
			//return $rules;
			}
		
		# Filter Author Rewrite Rules
		function remove_author_base_from_rewrite_rules($author_rewrite) {
			
			if($this->data['remove_author_base']){
				$author_rewrite=array();
				$blogusers = get_users('who=authors');
				foreach($blogusers as $user) {
					$author_rewrite['('.$user->user_nicename.')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?author_name=$matches[1]&feed=$matches[2]';
					$author_rewrite['('.$user->user_nicename.')/page/?([0-9]{1,})/?$'] = 'index.php?author_name=$matches[1]&paged=$matches[2]';
					$author_rewrite['('.$user->user_nicename.')/?$'] = 'index.php?author_name=$matches[1]';
					}
				}
			return $author_rewrite;
			}
		# WP-htaccess Control options page interface
		function page(){
			include (dirname (__FILE__).'/wp-htaccess-control-ui.php');
			}
		# Adding our options page to the admin menu
		function configure_menu(){
			if(current_user_can("administrator")){
				$page=add_submenu_page("options-general.php","WP htaccess Control", "htaccess Control", 6, __FILE__, array('WPhtc','page'));
				add_action('admin_print_scripts-'.$page, array('WPhtc','page_script'));
				add_action('admin_print_styles-'.$page, array('WPhtc','page_style'));
				}
			}
		# Enqueue Ui Scripts on Plugin page
		function page_script(){
			wp_enqueue_script("htaccess-control-js", WP_PLUGIN_URL . '/wp-htaccess-control/wp-htaccess-control-ui.js');  
			}
		# Enqueue Ui Scripts on Plugin page
		function page_style(){
			wp_enqueue_style("htaccess-control-css", WP_PLUGIN_URL . '/wp-htaccess-control/wp-htaccess-control-ui.css');  
			}
		
		# Filter "View" Link on Administration
		function filter_tax_table_actions( $actions, $tag){
				if($tag->parent!=0){
					$actions['view']='<a href="'.get_bloginfo('home').'/'.$this->term_ancestors($tag->taxonomy,$tag->parent).'/'.$tag->slug.'">View</a>';
					}
				return $actions;
			}
		
		# Options Page Actions
		function page_action(){
			$this->check_first_run();
			$action=$_REQUEST['action'];
			global $echo;
			if(isset($action)){
				
				switch($action){
					# Hide donation message for one month
					case 'hide_donation_message':
						$this->data['donation_hidden_time']=time()+ 90 * 24 * 60 * 60;
						update_option('WPhtc_data',$this->data);
						break;
					# if reseting everything just delete the option array
					case 'reset_rules':
						# nonce
						if(!check_admin_referer( 'WPhtc_reset_settings')){
							die("You have no permission to do this.");
							}
						delete_option('WPhtc_data');
						$echo.=__('All rules reset.', 'wp-htaccess-control');
						$this->refresh_rewrite_rules();
						break;
					# if updating, save new rules in database and flush rewrite rules
					case 'update':	
						# first donation hidding time 'now'
						if(!$this->data['donation_hidden_time']){
							$this->data['donation_hidden_time']=time();
							}
						# nonce
						if(!check_admin_referer( 'WPhtc_settings')){
							die("You have no permission to do this.");
							}
						# get Custom Htaccess
						$this->data['hta']=$_POST['WPhtc_hta'];
						# get Custom Author Permalink
						$this->data['cap']=$_POST['WPhtc_cap'];
						# get Custom Pagination Permalink
						$this->data['cpp']=$_POST['WPhtc_cpp'];
						# get Custom Search Permalink
						$this->data['custom_search_permalink']=$_POST['WPhtc_custom_search_permalink'];
						# wordpress htaccess and jim morgan's htaccess
						$this->data['wp_hta']=$_POST['WPhtc_wp_hta']."\n";
						$this->data['jim_morgan_hta']=$_POST['WPhtc_jim_morgan_hta'];
						if($this->data['jim_morgan_hta']){
							$this->data['wp_hta'] ="\nRewriteEngine on\n";
							$this->data['wp_hta'].="# Unless you have set a different RewriteBase preceding this point,\n";
							$this->data['wp_hta'].="# you may delete or comment-out the following RewriteBase directive:\n";
							$this->data['wp_hta'].="RewriteBase /\n";
							$this->data['wp_hta'].="# if this request is for \"/\" or has already been rewritten to WP\n";
							$this->data['wp_hta'].="RewriteCond $1 ^(index\.php)?$ [OR]\n";
							$this->data['wp_hta'].="# or if request is for image, css, or js file\n";
							$this->data['wp_hta'].="RewriteCond $1 \.(gif|jpg|jpeg|png|css|js|ico)$ [NC,OR]\n";
							$this->data['wp_hta'].="# or if URL resolves to existing file\n";
							$this->data['wp_hta'].="RewriteCond %{REQUEST_FILENAME} -f [OR]\n";
							$this->data['wp_hta'].="# or if URL resolves to existing directory\n";
							$this->data['wp_hta'].="RewriteCond %{REQUEST_FILENAME} -d\n";
							$this->data['wp_hta'].="# then skip the rewrite to WP\n";
							$this->data['wp_hta'].="RewriteRule ^(.*)$ - [S=1]\n";
							$this->data['wp_hta'].="# else rewrite the request to WP\n";
							$this->data['wp_hta'].="RewriteRule . /index.php [L]\n";
							}
						# Remove Author Base
						$this->data['remove_author_base']=$_POST['WPhtc_remove_author_base'];
						# Advanced Archives
						$this->data['category_archives']=$_POST['WPhtc_category_archives'];
						$this->data['author_archives']=$_POST['WPhtc_author_archives'];
						$this->data['tag_archives']=$_POST['WPhtc_tag_archives'];
						# get htaccess Suggestions
						$this->data['disable_serversignature']=$_POST['WPhtc_disable_serversignature'];
						$this->data['disable_indexes']=$_POST['WPhtc_disable_indexes'];
						$this->data['up_limit']=(is_numeric($_POST['WPhtc_up_limit'])&&$_POST['WPhtc_up_limit']>0)?$_POST['WPhtc_up_limit']:'';
						$this->data['protect_wp_config']=$_POST['WPhtc_protect_wp_config'];
						$this->data['protect_htaccess']=$_POST['WPhtc_protect_htaccess'];
						$this->data['protect_comments']=$_POST['WPhtc_protect_comments'];
						$this->data['disable_hotlink']=trim($_POST['WPhtc_disable_hotlink']);
						$this->data['disable_file_hotlink_ext']=trim($_POST['WPhtc_disable_file_hotlink_ext']);
						$this->data['disable_file_hotlink_redir']=trim($_POST['WPhtc_disable_file_hotlink_redir']);
						$this->data['redirect_500']=trim($_POST['WPhtc_redirect_500']);
						$this->data['redirect_403']=trim($_POST['WPhtc_redirect_403']);
						$this->data['canon']=$_POST['WPhtc_canon'];
						$this->data['admin_email']=trim($_POST['WPhtc_admin_email']);
						$this->data['deflate']=trim($_POST['WPhtc_deflate']);
						$this->data['gzip']=trim($_POST['WPhtc_gzip']);
						# get maintenance
						$this->data['maintenance_active']=$_POST['WPhtc_maintenance_active'];
						$lines=preg_split("/\n|,/",$_POST['WPhtc_maintenance_ips']);
						$this->data['maintenance_ips']=array();
						foreach($lines as $line){
							trim($line);
							if(preg_match("/[a-z,0-9,\.]/",$line)){
								$this->data['maintenance_ips'][]=$line;
								}
							}
						$this->data['maintenance_redirection']=trim($_POST['WPhtc_maintenance_redirection']);
						# get login control data
						$this->data['login_disabled']=$_POST['WPhtc_login_disabled'];
						$this->data['login_half_mode']=$_POST['WPhtc_login_half_mode'];
						$this->data['login_redirection']=trim($_POST['WPhtc_login_redirection']);
						$lines=preg_split("/\n|,/",$_POST['WPhtc_login_ips']);
						$this->data['login_ips']=array();
						foreach($lines as $line){
							trim($line);
							if(preg_match("/[a-z,0-9,\.]/",$line)){
								$this->data['login_ips'][]=$line;
								}
							}
						
						# Unsetting inclusion of Author pages on Google XML Sitemap options, we'll add those links to the sitemap later with our Custom Author Permalink
						$this->data['sm_enabled']=$_POST['WPhtc_sm_enabled'];
						if($this->data['sm_enabled']==true){
							$SMoptions=get_option("sm_options");
							if($SMoptions&&is_array($SMoptions)){
								$SMoptions=get_option("sm_options");
								$SMoptions['sm_in_auth']=0;
								update_option('sm_options',$SMoptions);
								# Try to rebuild Sitemap
								do_action("sm_rebuild");
								}
							}
						
						# Taxonomy Base removal options
						unset($this->data['remove_taxonomy_base']);
						if($_POST['WPhtc_remove_base']){
							foreach($_POST['WPhtc_remove_base'] as $tax_key=>$remove){
								$this->data['remove_taxonomy_base'][$tax_key]=$remove;
								}
							}
						
						# Advanced archives
						unset($this->data['create_archive']);
						if($_POST['WPhtc_create_archive']){
							foreach($_POST['WPhtc_create_archive'] as $tax_key=>$create_archive){
								$this->data['create_archive'][$tax_key]=$create_archive;
								}
							}
						
						# Update WP htaccess Control options
						update_option('WPhtc_data',$this->data);
						# Flush Rewrite Rules
						$this->refresh_rewrite_rules();
						$echo.=__('All options updated.', 'wp-htaccess-control');
						break;
					}
				}
			}
		}
	} 
if (class_exists("WPhtc")) {
	$WPhtc = new WPhtc();
	}
if (isset($WPhtc)) {
	
	add_action('init', array($WPhtc,'init'));
	add_filter('mod_rewrite_rules', array($WPhtc,'hta_rules'));
	add_filter('redirect_canonical',array($WPhtc,'filter_redirect_canonical'),10,10);
	add_action('admin_menu', array($WPhtc,'configure_menu'));
	add_action('flush_event',array($WPhtc,'refresh_rewrite_rules'));
	add_filter('generate_rewrite_rules', array($WPhtc,'wp_rewrite_rules'));
	add_filter('rewrite_rules_array',array($WPhtc,'filter_rewrite_rules'));
	
	// AUTHOR improve this
	if($WPhtc->data['cap']!='' || $WPhtc->data['remove_author_base']){
		add_filter('author_link',array($WPhtc,'filter_author_link'));
		add_filter('author_rewrite_rules', array($WPhtc,'remove_author_base_from_rewrite_rules'));
		}
	
	// Filter pagination links
	if($WPhtc->data['cpp']!='') add_filter('get_pagenum_link',array($WPhtc,'filter_get_pagenum_link'));
	
	// Filter search
	if(trim($WPhtc->data['custom_search_permalink'])!=''){
		add_filter('search_feed_link',array($WPhtc,'search_feed_link'),10,10);
		add_filter('get_search_query',array($WPhtc,'get_search_query_filter'),10,10);
		add_action('template_redirect', array($WPhtc,'search_template_redirect') );
		}
	
	
	add_action('sm_buildmap',array($WPhtc,'set_sm'));
	
	/* Taxonomy Base Removal*/
	if($WPhtc->data['remove_taxonomy_base']){
		add_filter('term_link',array($WPhtc,'filter_term_link'),10,3);
		foreach($WPhtc->data['remove_taxonomy_base'] as $tax=>$v){
			if($v) {
				add_filter($tax.'_rewrite_rules', array($WPhtc,'remove_taxonomy_base_from_rewrite_rules'));
				add_filter($tax."_row_actions",array($WPhtc,'filter_tax_table_actions'), 10,2 );
				}
			}
		}
	/* Term management actions*/
	if($WPhtc->data['remove_taxonomy_base'] || $WPhtc->data['create_archive']){
		add_action('created_term',array($WPhtc,'refresh_rewrite_rules_later'));
		add_action('edited_term',array($WPhtc,'refresh_rewrite_rules_later'));
		add_action('delete_term',array($WPhtc,'refresh_rewrite_rules_later'));
		}
		
	/* This flush should maybe be conditional to content authors only, maybe not */
	add_action('user_register',array($WPhtc,'refresh_rewrite_rules_later'));
	add_action('delete_user',array($WPhtc,'refresh_rewrite_rules_later'));
	}
?>