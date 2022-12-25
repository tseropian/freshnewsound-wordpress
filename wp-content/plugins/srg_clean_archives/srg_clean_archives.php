<?php
/*
Plugin Name: SRG Clean Archives
Plugin URI: http://www.idunzo.com/projects/clean-archives/
Description: This plugin is designed to display your archive listings in a clean, uniform, single-query fashion that's Search Engine friendly on a dedicated page or in your sidebar.
Version: 4.3
Author: Sean R.
Author URI: http://www.idunzo.com
*/

/* Copyright (C) 2007-2008 Sean R. - iDunzo.com

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
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA */

// Version display
define('SRG_VER', '4.3');

// Load plugin textdomain for localization
load_plugin_textdomain('srgca','wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/languages/' );

// Display password protected posts option
if (get_option('srg-clean_ppp-display') == 'true') {
$srg_show_passworded_posts = TRUE;
} else {
$srg_show_passworded_posts = FALSE;
}

// The moo.fx effects
if (get_option('srg-clean_moo-display') == 'true') {
$srg_use_moofx = TRUE;
} else {
$srg_use_moofx = FALSE;
}

// Uncomment these next lines or set these variables to TRUE elsewhere if you are already calling the moo.fx files in your site
//$prototype_loaded = TRUE;
//$moofx_main_loaded = TRUE;
//$moofx_pack_loaded = TRUE;

// This function returns the total number of posts that this plugin will display
function srg_total_posts() {
global $wpdb, $srg_show_passworded_posts;
$totalquery = "SELECT ID FROM $wpdb->posts WHERE post_date <'" . current_time('mysql') . "' AND post_status='publish' AND post_type='post'";
if ($srg_show_passworded_posts != TRUE) $totalquery .= " AND post_password=''";
return $wpdb->query($totalquery);
}

// This function echos the archives is the template that is called
function srg_clean_archives() {
echo srg_get_clean_archives();
}

// Call this function to output the archives list
function srg_get_clean_archives() {
global $wpdb, $srg_show_passworded_posts, $srg_use_moofx, $prototype_loaded, $moofx_main_loaded, $moofx_pack_loaded;

// Double check to make sure the cache is valid
$totalposts = srg_total_posts();

// Get the current cache from the database
$data = get_option('srg_clean_archives');

// If there's no cache or the cache has less posts than there currently should be, regenerate the cache
if ($totalposts != $data['totalposts']) $data = srg_regenerate();
	
// Start output
$result = "<!-- Start SRG Clean Archives Output | http://www.idunzo.com/projects/ -->\n";

// Now output the JavaScript, if it's wanted
if ($srg_use_moofx && !$_GET['showall']) {
$srg_folder = get_bloginfo('wpurl') . '/wp-content/plugins/' . dirname(plugin_basename(__FILE__));

// Only load up the JavaScript files if they haven't been called already. 
if (!$prototype_loaded)		$result .= '<script src="' . $srg_folder . '/moo.fx/prototype.lite.js" type="text/javascript"></script>';
if (!$moofx_main_loaded)	$result .= '<script src="' . $srg_folder . '/moo.fx/moo.fx.js" type="text/javascript"></script>';
if (!$moofx_pack_loaded)	$result .= '<script src="' . $srg_folder . '/moo.fx/moo.fx.pack.js" type="text/javascript"></script>';

$result .= '<script src="' . $srg_folder . '/srg_clean_archives.js" type="text/javascript"></script>';
} // End JavaScript output

// Make the links to switch between moo.fx mode and classic mode
if ($srg_use_moofx) {
if ($_GET['showall']) {
$result .= '<span class="srg_switcher"><a href="' . htmlspecialchars(add_query_arg('showall', '')) . '" title="'. __('Return to the default, collapsed view','srgca') .'">&laquo; '. __('Return To Collapsed View','srgca') .'</a></span>';
} else {
$result .= '<span class="srg_switcher"><a href="' . htmlspecialchars(add_query_arg('showall', '1')) . '" title="'. __('View all posts in a non-collapsed view','srgca') .'">'. __('Expand All Months','srgca') .' &raquo;</a></span>';
}
$result .= "\n\n";
}

// Output the cache
$result .= $data['cache'];
	
// Initiate the JavaScript
if ($srg_use_moofx && !$_GET['showall']) {
$result .= '<script type="text/javascript">cleanArchivesInit();</script>' . "\n";
}
	
$result .= "<!-- End SRG Clean Archives Output -->\n";
$result .= '<p style="font-size:90%; float: right;"><small>'. __('Plugin by','srgca') .' <a href="http://www.idunzo.com/" title="iDunzo.com">iDunzo.com</a></small></p>';
	
return $result;
}

// This function generates the meat of the plugin, then caches it to the database
function srg_regenerate() {
global $month, $wpdb, $srg_show_passworded_posts;
$totalposts = 0;

// Get all of the months that have posts
$monthquery = "SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month, count(ID) as posts FROM " . $wpdb->posts . " WHERE post_date <'" . current_time('mysql') . "' AND post_status='publish' AND post_type='post'";
if ($srg_show_passworded_posts != TRUE) $monthquery .= " AND post_password=''";
$monthquery .= " GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC";
$monthresults = $wpdb->get_results($monthquery);

if ($monthresults) {
// Loop through each month
foreach ($monthresults as $monthresult) {
$thismonth	= zeroise($monthresult->month, 2);
$thisyear	= $monthresult->year;

// Get all of the posts for the current month
$postquery = "SELECT ID, post_date, post_title, comment_status FROM " . $wpdb->posts . " WHERE post_date LIKE '$thisyear-$thismonth-%' AND post_date AND post_status='publish' AND post_type='post'";
if ($srg_show_passworded_posts != TRUE) $postquery .= " AND post_password=''";
$postquery .= " ORDER BY post_date DESC";
$postresults = $wpdb->get_results($postquery);

if ($postresults) {
// The month year title things
$text = sprintf('%s %d', $month[zeroise($monthresult->month,2)], $monthresult->year);
$postcount = count($postresults);
$output .= '<span style="cursor: pointer;" class="monthtitle" title="' . $monthresult->year . '-' . zeroise($monthresult->month,2) . '"><span title="Expand the ' . $postcount . ' post';
if ($postcount != 1) $output .= 's';
$output .= ' from ' . $text . '"><a href="#' . $monthresult->year . '-' . zeroise($monthresult->month,2) . '"><strong>' . $text . '</strong></a> &nbsp;'; 
				
// If set to show  post count
if (get_option('srg-clean_post-count') == 'true') {
$output .= '(' . count($postresults) . ') ';
}

// If set to show link to details 
if (get_option('srg-clean_link-to-details') == 'true') {
$my_url = get_bloginfo('url');
$text = sprintf('%s %d', $month[zeroise($monthresult->month,2)], $monthresult->year);
if (get_option('permalink_structure') == '') {
$output .= '<br /><a href="'. $my_url .'?m=' . $monthresult->year . zeroise($monthresult->month,2) . '" title="'. __('Show detailed results for','srgca') .' ' . $text . '">'. __('Detailed Monthly Archive','srgca') .'</a>';
} else {
$output .= '<br /><a href="'. $my_url .'/' . $monthresult->year . '/' . zeroise($monthresult->month,2) . '" title="'. __('Show detailed results for','srgca') .' ' . $text . '">'. __('Detailed Monthly Archive','srgca') .'</a>';
}
}
$output .= "</span></span>\n";
$output .= "<ul class='postspermonth'>\n";
foreach ($postresults as $postresult) {
if ($postresult->post_date != '0000-00-00 00:00:00') {
$url		= get_permalink($postresult->ID);
$arc_title	= $postresult->post_title;
if ($arc_title) $text = wptexturize(strip_tags($arc_title));
else $text = $postresult->ID;
$title_text = 'View this post, &quot;' . wp_specialchars($text, 1) . '&quot;';
$output .= '	<li>' . mysql2date('d', $postresult->post_date) . ':&nbsp;' . "<a href='$url' title='$title_text'>$text</a>";
$comments_count = $wpdb->get_var("SELECT COUNT(comment_id) FROM " . $wpdb->comments . " WHERE comment_post_ID=" . $postresult->ID . " AND comment_approved='1'");
						
// Show comment count if set
if (get_option('srg-clean_comment-display') == 'true') {
if ($postresult->comment_status == "open" OR $comments_count > 0) $output .= '&nbsp;(' . $comments_count . ')';
}						
$output .= "</li>\n";
$totalposts++;
}
}
$output .= "</ul>\n\n";
}
}
} else {
$output = '<strong>'. __('ERROR:','srgca') .'</strong> '. __('No items were found to be displayed.','srgca') .'';
}
$output = array(
'totalposts' => $totalposts,
'cache' => $output,
);
update_option('srg_clean_archives', $output);
return $output;
}

// Needed for post title/stub changes
add_action('edit_post', 'srg_regenerate');

// Needed to incase a post is deleted and then a future post becomes a past post (which would
// result in the same number of total posts) before the archives page is viewed again.
add_action('delete_post', 'srg_regenerate');

// Make sure the comment counts stay up to date
add_action('comment_post', 'srg_regenerate');
add_action('delete_comment', 'srg_regenerate');

// Filter for posts 
add_action('init', 'do_srg_pages');
function do_srg_pages() {
add_filter('the_content', 'srg_pages', 1);
}
function srg_pages($post) {
if (substr_count($post, '<!--srg_clean_archives-->') > 0) {
$archives = srg_get_clean_archives();
$post = str_replace('<!--srg_clean_archives-->', $archives, $post);
}
return $post;
}

// Plugin admin menu 
add_action('admin_menu', 'srg_add_admin_page');
function srg_add_admin_page() {

if ( !function_exists('wp_nonce_field') ) {
        function my_nonce_field($action = -1) { return; }
        $myplugin_nonce = -1;
} else {
        function myplugin_nonce_field($action = -1) { return wp_nonce_field($action); }
        $myplugin_nonce = 'myplugin-update-key';
}

add_submenu_page('plugins.php', 'SRG Clean Archives Options', 'SRG Clean Archives', 9, 'srg-clean-archives', 'srg_admin_page');
}

function srg_admin_page() { 
if (isset($_POST['srg_submit'])) {

// check_admin_referer( '$myplugin_nonce', $myplugin_nonce ); // commented out to fix options in WP 2.5

if (isset($_POST['srg_show-comments'])) {
update_option('srg-clean_comment-display', 'true');
} else {
update_option('srg-clean_comment-display', 'false');
}

if (isset($_POST['srg_show-pc'])) {
update_option('srg-clean_post-count', 'true');
} else {
update_option('srg-clean_post-count', 'false');
}
	
if (isset($_POST['srg_show-ppp'])) {
update_option('srg-clean_ppp-display', 'true');
} else {
update_option('srg-clean_ppp-display', 'false');
}

if (isset($_POST['srg_show-moo'])) {
update_option('srg-clean_moo-display', 'true');
} else {
update_option('srg-clean_moo-display', 'false');
}

if (isset($_POST['srg_link-to-details'])) {
update_option('srg-clean_link-to-details', 'true');
} else {
update_option('srg-clean_link-to-details', 'false');
}

// Regenerate options	
srg_regenerate();
echo '<div id="message" class="updated fade"><p><strong>'. __('Options saved.','srgca') .'</strong></p></div>';
}

if (get_option('srg-clean_comment-display') == 'true') {
$show_comments = 'checked="checked"';
} else {
$show_comments = '';
}

if (get_option('srg-clean_post-count') == 'true') {
$show_pc = 'checked="checked"';
} else {
$show_pc = '';
}

if (get_option('srg-clean_ppp-display') == 'true') {
$show_ppp = 'checked="checked"';
} else {
$show_ppp = '';
}

if (get_option('srg-clean_moo-display') == 'true') {
$show_moo = 'checked="checked"';
} else {
$show_moo = '';
}

if (get_option('srg-clean_link-to-details') == 'true') {
$link_to_details = 'checked="checked"';
} else {
$link_to_details = '';
}
?>
<div class="wrap">
<h2><?php _e('SRG Clean Archives Settings','srgca'); ?> - v<?php echo SRG_VER; ?></h2>
<p><?php _e('If you find this plugin useful, consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&amp;business=geekwithlaptop%40gmail%2ecom&amp;item_name=Donations%20for%20Geek%20With%20Laptop&amp;no_shipping=1&amp;no_note=1&amp;tax=0&amp;currency_code=USD&amp;bn=PP%2dDonationsBF&amp;charset=UTF%2d8" title="Donations - Support Geek With Laptop" target="_blank">PayPal donation</a> to show your support.','srgca'); ?></p>
<form method="post" action="">
<p>
<?php myplugin_nonce_field('$myplugin_nonce', $myplugin_nonce); ?>
<input type="checkbox" name="srg_show-pc" <?php echo $show_pc; ?> /> <?php _e('Show monthly post count','srgca'); ?><br />
<input type="checkbox" name="srg_show-comments" <?php echo $show_comments; ?> /> <?php _e('Show comment count','srgca'); ?><br />
<input type="checkbox" name="srg_show-ppp" <?php echo $show_ppp; ?> /> <?php _e('Display password protected posts','srgca'); ?><br />
<input type="checkbox" name="srg_show-moo" <?php echo $show_moo; ?> /> <?php _e('Enable moo.fx effects','srgca'); ?><br />
<input type="checkbox" name="srg_link-to-details" <?php echo $link_to_details; ?> /> <?php _e('Show link to detailed monthly archive','srgca'); ?><br />
<br />
<input type="submit" name="srg_submit" value="<?php _e('Update options &raquo;', 'srgca'); ?>" />
</p>
</form>
</div>
<?php 

}

// Set defaults when plugin activated
add_action('activate_srg_clean_archives/srg_clean_archives.php', 'srg_set_defaults');

// Set default settings
function srg_set_defaults() {
update_option('srg-clean_comment-display', 'true');
update_option('srg-clean_post-count', 'true');
update_option('srg-clean_moo-display', 'true');
update_option('srg-clean_link-to-details', 'true');
}
?>
