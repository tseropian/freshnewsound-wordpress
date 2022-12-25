<?php
/*
Plugin Name: No Excerpt-P
Description: Removes the paragraph tags from the output returned by the_excerpt() function
Version: 1.0
Author: Amit Gupta
Author URI: http://blog.igeek.info/
*/

remove_filter('the_excerpt', 'wpautop');

?>