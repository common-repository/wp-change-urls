<?
/*
Plugin Name: WP Change URLs
Plugin URI: http://adminofsystem.net/
Description: WP Change URLs. <a href='/wp-admin/options-general.php?page=wp-change-url.php'>Change settings</a>
Version: 1.0
Author: Yahin Ruslan
Author URI: http://adminofsystem.net
*/
/*  Copyright 2010  Yahin Ruslan (email : nessus@adminofsystem.netL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

	$wp_change_url_added = false;
	$wp_change_url_exclude = array();
	$wp_change_url_new = array();
	$wp_change_url_text = '';
	$wp_change_url_url = '';

	function wp_change_url_array_search( $search, $array )
	{
		foreach($array as $value)
		{
			if(strpos($search,$value) === true) return true;
		}
		return false;
	}
	function wp_change_url_in_comments( $comment_text )
	{
	        global $wp_change_url_added;
     	        global $wp_change_url_exclude;
	        global $wp_change_url_new;
                global $wp_change_url_text;
                global $wp_change_url_url;

		$wp_change_url_exclude = explode("\n",get_option('wp_change_url_in_comments_exclude'));
		$wp_change_url_new = explode("\n",get_option('wp_change_url_in_comments_new'));
                $wp_change_url_text = get_option('wp_change_url_in_comments_text');
                $wp_change_url_url =  get_option('wp_change_url_in_comments_url');

		$rep_comment_text=preg_replace_callback('/<a href="(.*?)">(.*?)<\/a>/si','wp_change_url_callback',$comment_text);
                $wp_change_url_added = false;
		update_option('wp_change_url_in_comments_new',implode("\n",$wp_change_url_new));
		return $rep_comment_text;
	}
	function wp_change_url_in_content( $content )
	{
		global $wp_change_url_added;
 	        global $wp_change_url_exclude;
        	global $wp_change_url_new;
	        global $wp_change_url_text;
                global $wp_change_url_url;
	        
		$wp_change_url_exclude = explode("\n",get_option('wp_change_url_in_content_exclude'));
		$wp_change_url_new = explode("\n",get_option('wp_change_url_in_content_new'));
                $wp_change_url_text = get_option('wp_change_url_in_content_text');
                $wp_change_url_url =  get_option('wp_change_url_in_content_url');

		$rep_content=preg_replace_callback('/<a href="(.*?)">(.*?)<\/a>/si','wp_change_url_callback',$content);
		$wp_change_url_added = false;
		update_option('wp_change_url_in_content_new',implode("\n",$wp_change_url_new));
		return $rep_content;	
	}
	function wp_change_url_callback( $param )
	{
		global $wp_change_url_added;
		global $wp_change_url_exclude;
		global $wp_change_url_new;
                global $wp_change_url_text;
                global $wp_change_url_url;

		if(wp_change_url_array_search($param[0],$wp_change_url_exclude) === false) 
		{
			if(array_search($param[1],$wp_change_url_new) === false) {
						array_push($wp_change_url_new,$param[1]); 
			}
			if($wp_change_url_added === false)
			{
				if(!empty($wp_change_url_url)) {
			           	$param[1]=$wp_change_url_url;
				}
				if(!empty($wp_change_url_text)) {
					$param[2]=$wp_change_url_text;
				}
				$wp_change_url_added = true;
				return '<a href="'.$param[1].'">'.$param[2].'</a>';
			} 
			else 
			{
				return '';
			}
		}
	}	
	function wp_change_url_admin_menu()
	{
		 if(isset($_REQUEST['submit']))
		 {
 			update_option('wp_change_url_in_content_url', $_REQUEST['wp_change_url_in_content_url']);
             		update_option('wp_change_url_in_content_text', $_REQUEST['wp_change_url_in_content_text']);
              		update_option('wp_change_url_in_content_exclude', $_REQUEST['wp_change_url_in_content_exclude']);

			update_option('wp_change_url_in_comments_url', $_REQUEST['wp_change_url_in_comments_url']);
	                update_option('wp_change_url_in_comments_text', $_REQUEST['wp_change_url_in_comments_text']);
                        update_option('wp_change_url_in_comments_exclude', $_REQUEST['wp_change_url_in_comments_exclude']);

        		echo "<div class='updated'><p><strong>WP Related Posts options updated</strong></p></div>";
   		 }
		$exclude_content = get_option('wp_change_url_in_content_exclude');
		$url_content = get_option('wp_change_url_in_content_url');
		$text_content = get_option('wp_change_url_in_content_text');
		$new_urls_content = get_option('wp_change_url_in_content_new'); 

		$exclude_comments = get_option('wp_change_url_in_comments_exclude');
                $url_comments = get_option('wp_change_url_in_comments_url');
                $text_comments = get_option('wp_change_url_in_comments_text');
		$new_urls_comments = get_option('wp_change_url_in_comments_new');
?>
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>?page=wp-change-url.php"
		<div class="wrap"><h2>Display options</h2>
		<h3>Urls in content</h3>
                <table class="form-table">
   		<tr valign="top">
         		<th scope="row">Url:</th>
         		<td><input type="text" name="wp_change_url_in_content_url" value="<?=$url_content?>"></td>
   		</tr>
   		<tr valign="top">
         		<th scope="row">Text:</th>
         		<td><input type="text" name="wp_change_url_in_content_text" value="<?=$text_content?>"></td>
   		</tr>
   		<tr valign="top">
         		<th scope="row">Exclude:</th>
         		<td>
				<textarea cols=60 rows=5 name="wp_change_url_in_content_exclude"><?=$exclude_content?></textarea>
  		        </td>
			<th scope="row">New:</th>
			<td>
				<textarea cols=60 rows=5><?=$new_urls_content?></textarea>
			</td>
   		</tr>
   		</table>
		<h3>Urls in comments</h3>
		<table class="form-table">
                <tr valign="top">
                        <th scope="row">Url:</th>
                        <td><input type="text" name="wp_change_url_in_comments_url" value="<?=$url_comments?>"></td>
                </tr>
                <tr valign="top">
                        <th scope="row">Text:</th>
                        <td><input type="text" name="wp_change_url_in_comments_text" value="<?=$text_comments?>"></td>
                </tr>
                <tr valign="top">
                        <th scope="row">Exclude:</th>
                        <td>
                                <textarea cols=60 rows=5 name="wp_change_url_in_comments_exclude"><?=$exclude_comments?></textarea>
                        </td>
			<th scope="row">New:</th>
			<td>
                                <textarea cols=60 rows=5><?=$new_urls_comments?></textarea>
                        </td>
                </tr>
                </table>
   		<p class="submit">
        		<input type="submit" value="Update Options &raquo;" name="submit">
   		</p>
   		</div>
   		</form>
<?php
}	
	function wp_change_url_admin_init()
	{
   		add_options_page('WP Change URLs', 'WP Change URLs', 8, basename(__FILE__), 'wp_change_url_admin_menu');
	}
	function wp_change_url_in_content_activation()
	{
   		add_option('wp_change_url_in_content_exclude',$_SERVER['HTTP_HOST']);
		add_option('wp_change_url_in_comments_exclude',$_SERVER['HTTP_HOST']); 

  		add_option('wp_change_url_in_content_url','');
		add_option('wp_change_url_in_content_text','');

		add_option('wp_change_url_in_comments_url','');
                add_option('wp_change_url_in_comments_text','');

		add_option('wp_change_url_in_content_new_urls','');
		add_option('wp_change_url_in_comments_new_urls','');
	}
	function wp_change_url_in_content_deactivation()
	{
   		delete_option('wp_change_url_in_content_exclude');
		delete_option('wp_change_url_in_comments_exclude');

   		delete_option('wp_change_url_in_content_url');
		delete_option('wp_change_url_in_content_text');

		delete_option('wp_change_url_in_comments_url');
		delete_option('wp_change_url_in_comments_text');
		
		delete_option('wp_change_url_in_content_new_urls');
		delete_option('wp_change_url_in_comments_new_urls');
	}

	add_action('the_content', 'wp_change_url_in_content');
	add_action('comment_text','wp_change_url_in_comments');
	add_action('admin_menu', 'wp_change_url_admin_init');

	register_activation_hook(__FILE__, 'wp_change_url_in_content_activation');
	register_deactivation_hook(__FILE__, 'wp_change_url_in_content_deactivation');
?>
