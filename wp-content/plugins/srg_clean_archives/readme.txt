SRG Clean Archives v4.2 & v4.3
Plugin Author: Sean R. - iDunzo.com 
Plugin URI: http://www.idunzo.com/projects/
Version 4.3 works with WordPress series 2.5.x only
Version 4.2 works with WordPress series 2.1.x thru 2.3

Released Under the GPL Licence (http://www.fsf.org/licensing/licenses/gpl.txt)


About:
=============================================
This plugin is designed to display your archive listings in a clean and uniform fashion that's Search Engine friendly 
on a dedicated page or in your sidebar. 

 
Plugin Admin Features:
=============================================
Admin menu to toggle on/off the following options:

Toggle on/off showing total post count next to month (on by default).
Toggle on/off showing total comment count next to post title (on by default). 
Toggle on/off password protected post display (off by default).
Toggle on/off the moo.fx for collapsing months (on by default).
Toggle on/off showing link to detailed monthly archive (on by default).

Note: If moo.fx is not enabled, your full archive display is shown but it's still using the caching function. 

As of SRG Clean Archives version 4.0, there is now a caching function which caches the output so the plugin only generates 1 or 2 
queries each time the page is viewed.


Installation and Usage:
=============================================
Upload the contents of the downloaded ZIP file to your plugins folder located at "/wp-content/plugins/" while making sure to 
keep this plugin's file structure intact.

You should end up with a folder called "srg_clean_archives" in your plugins folder and some files and a folder called "moo.fx" inside of that.

Go to your WordPress dashboard admin area and activate the plugin from the plugins page.


Creating Archive Page:
=============================================
Create a page like you normally would in WordPress. In my example, we'll give it the page title of "Archives". In the Page Content 
section add the following code:

<!--srg_clean_archives-->

For those of you using the new rich-text editor - be sure to click the 'html' button to edit the page source directly. Otherwise WordPress 
will wrap code tags around the line which generates the archives output and it will not work properly.

Also, make sure under 'Page Template' you are using 'Default Template' as some WordPress themes have an archives template.  
Press the publish button and you're done. You'll now have an archives page. If you want, you can also add text above the archives page 
code as seen on my site http://www.geekwithlaptop.com/archives/

Adding Archives To Your Sidebar:
=============================================
If you would like to have your archives in your sidebar, you would use the following function call code:

<?php if (function_exists('srg_clean_archives')) { srg_clean_archives(); } ?>

If you need any help with getting this going, let me know. 


Plugin Admin Menu:
=============================================
Go to Plugins, then submenu SRG Clean Archives in your WordPress dashboard. From there you can adjust the following:

Toggle on/off showing total post count next to month (on by default). 
Toggle on/off showing total comment count next to post title (on by default). 
Toggle on/off password protected post display (off by default).
Toggle on/off the moo.fx for collapsing months (on by default).
Toggle on/off showing link to detailed monthly archive (on by default).

Make your selection(s) by checking the box and pressing the update options button. 


Adding CSS Styling:
=============================================
The list of of articles can be styled in your CSS by setting up a .postspermonth class.

Example:

.postspermonth {
margin: 5px 0 10px 0;
list-style: none;
padding-left: 25px;
}

The output from this plugin is already wrapped in the needed <ul class="postpermonth"> tags. All you need to do is customize the 
class to your taste.

The Month/Year format is surrounded by <strong> </strong> tags. If you would like to change this, just open up the srg_clean_archives.php 
file and look for the following line of code at line 146:

$output .= ' from ' . $text . '"><a href="#' . $monthresult->year . '-' . zeroise($monthresult->month,2) . '"><strong>' . $text . '</strong></a>&nbsp;';

There you can change the current tags to whatever you would like to use. Such as a h2, h3, etc. and resave the file. Just be 
sure not to delete the quote marks.

That's it. Enjoy the plugin!


Plugin Support And Feature Request
==============================================
If you've read over this readme carefully and are still having issues, if you've discovered a bug, or have a feature request, please contact 
me via my contact page located at http://www.idunzo.com/contact/


Show Your Support - Make A PayPal Donation:
==============================================
Many hours have gone into this plugin to make it what you see today. If you find this plugin useful, consider making a PayPal donation to 
show your support. See project page for donation button. Another way to show your support is to write a post about the plugin with a link back 
to my site to help promote the plugin. Thank you in advance for your support and use of my plugin.


Special Thanks To The Following People:
==============================================
Shawn Grimes (http://www.sporadicnonsense.com)
Mark Jaquith (http://txfx.net/)
Jake (http://kolophon.net/)
Rubin J. Kaplan (http://me.mywebsight.ws/)
Viper (http://www.viper007bond.com/)
Owen Kelly (http://www.owenkelly.net)
Joern Kretzschmar (http://diekretzschmars.de)
