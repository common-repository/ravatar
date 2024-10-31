=== Ravatars ===
Contributors: grokcode
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jess%40grok%2dcode%2ecom&no_shipping=0&no_note=1&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: posts, comments, gravtars, icons, avatars, ravatars
Requires at least: 2.0.2
Tested up to: 2.7
Stable tag: trunk

Ravatars will generate and assign random icons to the visitors leaving comments at your site.  It can optionally show Gravatars as well.

== Description ==

Ravatars is a plugin that will generate and assign icons to the visitors leaving comments at your site. The icons are based on email, 
so a given visitor will get the same icon each time they comment.  It is easy to customize the avatars so that they match your site's topic or theme. It also makes comment threads easier to follow when people have memorable "faces."

This plugin is based on Shamus Young's Wavatars plugin.

And a shout out to Gregory Weir for bugfixes. Thanks!

<h2>Features:</h2>

<ol>
<li>You can customize the avatars your site will display to match your theme. Just upload source images to the 
wp-content/plugins/ravatars/parts directory. Ravatars will create your custom avatars by choosing a random image and then cropping a random
part of the image</li>

<li>The icons are generated on-the-fly.  You can adjust the desired size of the icons.</li>

<li>Easily integrates with avatar enabled themes</li>

<li>For easy deployment in Wordpress pre 2.5, icons will automatically precede the commenter's name.  You can set HTML to come directly before and after the
icon (to put it inside of a &lt;DIV&gt; tag, for example) or you can control the placement of the icons manually if you don't mind adding 
a single line of PHP to your theme.</li>

<li>The same email will result in the same Ravatar every time, assuming that the source images don't change the same.  If you want avatars that are unique to your site, all you need to do is change the source images.</li>

<li>This plugin also supports <a href="http://site.gravatar.com/">Gravatars</a>.  If you like, it can show the Gravatar for a given user 
(if available) and fall back on their Ravatar only if they don't have a Gravatar set up.  This means users can choose to set up a unique 
icon for themselves, and if they don't, they will be assigned a unique Ravatar.  It's a system that lets people personalize if 
they want, yet still provide a decent icon for the lazy or apathetic.</li>
</ol>

== Installation ==

<ol><li><a href="http://grok-code.com/7/ravatar-wordpress-plugin-for-randam-avatars/">Download</a> the plugin.
</li><li>Copy it onto your website in the wordpress <code>/plugins</code> folder.  Then enable the plugin.  That's it. Ravatars will 
instantly appear for all posts (even old ones) on your blog.  If you don't like how the image looks within your theme, read on...
</li></ol>

To change the source images, replace the files within wp-content/plugins/ravatars/parts with your own images. Supported image types are .jpg and .png.  Its hard to say how many images you should upload.  The minimum for a good set of ravatars is probably around 40, but you will want more if you have a lot of people posting and you want avatars to be unique for everybody. Try to get images with a lot of distinctive elements in them.      

If you are using Wordpress 2.5+ you need to have an avatar enabled theme, and ravatars will automatically show up.

If you are using Wordpress pre 2.5, read on for tips on how to position ravatars on your site.

The administration panel is under Options &raquo; Ravatars.  You can adjust the size of the Ravatars, and assign HTML to come before and 
after each image to help nudge it into place.  Each image is also set with the CSS "ravatars" class, so you can fine-tune the avatar position in your CSS file.

If that <i>still</i> doesn't give you enough control over ravatar placement and you don't mind editing your theme, just turn off automatic 
placement and add the line <code>ravatar_show($comment->comment_author_email);</code> to your comment loop wherever you want the image to appear.

Your mileage may vary.  How it will look depends largely on your current theme.

Note that the plugin requires that your install of PHP support the GD library.  If it doesn't, the Ravatars won't show up and you'll get
a warning in the Ravatar admin panel.  You can still use this plugin to display Gravatars, even if the GD library isn't available.

== Screenshots ==

1. A random selection of Ravatars using the default source images. When choosing your own images, you may want to pick only images of certian colors that match your theme. Or you may want to use only nature shots if you have a blog about hiking.  Also take a look at my site <a href="http://grok-code.com">grok-code.com</a> for an example of Ravatars that can be created from a different set of source images.  
  
== Revision History ==

<h3>Version 1.0.1</h3>

* Initial release.

== Advanced Tricks ==

= If you get a memory error =

Image manipulation functions are very memory intensive.  If you get a memory error, try reducing the size of your source images by lowering the resolution or cropping them into smaller parts.  You can also increase PHP's memory_limit.

= Using ravatar_show () =

If you place Ravatars by calling ravatar_show () manually, note that you can also specify an optional "size" argument to override the
default. For example:

<code>ravatar_show($comment->comment_author_email, '160');</code>

This would cause the Ravatar to be 160x160 pixels, even if the default was set to some other value.  You could do this to make admin icons
larger, for example.

= Using ravatar_get () =

If ravatar_show () STILL doesn't give you enough control, you can call:

<code>ravatar_get(email, size);</code>

And it will return the URL to the created image without writing anything to the page.

= Random Ravatar Field =

Put this code in your theme:
<code>
for ($i = 0; $i < 100; $i++)
    ravatar_show ($i);
</code>

It will generate a field of 100 random ravatars, which is cool.  This is how I generated the ravatar screenshot.  It's also a great way to quickly
test your source images.


