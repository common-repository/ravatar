<?php
/*
Plugin Name: Ravatar
Plugin URI: http://grok-code.com/7/ravatar-wordpress-plugin-for-randam-avatars/
Version: 2.0.4
Author: grokcode
Author URI: http://grok-code.com
Description: A plugin for generating random avatars from user-defined source images

Based on Wavatar plugin by Shamus Young 
http://www.shamusyoung.com/twentysidedtale/?p=1462

*/

define("AVATAR_SIZE",           '80');


add_action('get_avatar', 'ravatar_get_avatar', 1, 4);

// Override get_avatar for 2.5
function ravatar_get_avatar($empty, $id_or_email, $size = '96', $default = '') {

	$email = '';
	if ( is_numeric($id_or_email) ) {
		$id = (int) $id_or_email;
		$user = get_userdata($id);
		if ( $user )
			$email = $user->user_email;
	} elseif ( is_object($id_or_email) ) {
		if ( !empty($id_or_email->user_id) ) {
			$id = (int) $id_or_email->user_id;
			$user = get_userdata($id);
			if ( $user)
				$email = $user->user_email;
		} elseif ( !empty($id_or_email->comment_author_email) ) {
			$email = $id_or_email->comment_author_email;
		}
	} else {
		$email = $id_or_email;
	}

		if ( !is_numeric($size) )
		$size = '96';

		ravatar_show($email, $size);

}
	

/*-----------------------------------------------------------------------------
This is used to help build the options page.
-----------------------------------------------------------------------------*/
if (PHP_VERSION < 5) {
	function scandir($dir) {
		$dh = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			$files[] = $filename;
		}
		return $files;
	}
}

function ravatar_row ($title, $field, $help='', $checkbox=false)
{

    echo '<tr valign="top"><th scope="row">';
    _e("$title");
    echo '</th><td>';
    if ($checkbox) {
        echo "<input name='$field' type='checkbox' id='$field' value='1' ";
        checked('1', get_option($field));
        echo '/>';
    } else { //not a checkbox
        echo "<input name='$field' type='text' id='$field' value='";
        form_option("$field");
        echo "'";
        echo ' size="40" /><br/>';
    }
    echo "$help";
    echo '</td></tr>';
    echo "\n\n";

}

/*-----------------------------------------------------------------------------
 This builds the options page where you can administrate the plugin rather
 than mucking about here in the source code.  Which you seem to be doing anyway.
-----------------------------------------------------------------------------*/

function ravatar_options () {

    $hidden_field_name = 'ravatar_update';
    echo '<div class="wrap">';
    echo '<h2>Ravatar Options</h2>';
    echo '<h3>Configuration</h3>';
    
    // See if the user has chosen to purge the cache
    if ($_POST['ravatar_clear_cache'] == 'Y') {
		$localdir = dirname(__FILE__) . "/cache";
        echo '<div class="updated"><p><strong>';
        $dir = opendir ($localdir);
        if ($dir) {
            $file_count = 0;
            $delete_count = 0;
            while (($file = readdir ($dir)) !== false) {
                //only delete .png files.
                if (!strstr ($file, '.png'))
                    continue;
                $file_count++;
                if (unlink ($localdir . '/' . $file))
                    $delete_count++;
            }
            if ($file_count == 0)
                echo 'The cache is already empty.';
            else
                echo $delete_count . ' icons deleted.';
        } else
            echo 'Cannot open directory for reading.';
        echo '</strong></p></div>';
    }

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Save the posted value in the database
		if (!function_exists('add_meta_box')) { // Pre 2.5
			update_option ('ravatar_auto', $_POST['ravatar_auto']);
			update_option ('ravatar_size', intval ($_POST['ravatar_size']));
			update_option ('ravatar_border', intval ($_POST['ravatar_border']));
			update_option ('ravatar_suffix', $_POST['ravatar_suffix']);
			update_option ('ravatar_prefix', $_POST['ravatar_prefix']);
		}
		update_option ('ravatar_noplug', $_POST['ravatar_noplug']);
		update_option ('ravatar_auto', false);  // require themes to support gravatar
		update_option ('ravatar_gravatars', $_POST['ravatar_gravatars']);
        update_option ('ravatar_rating', $_POST['ravatar_rating']);		
        update_option ('ravatar_email_blank', $_POST['ravatar_email_blank']);
        // Put an options updated message on the screen
        ?>
        <div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
        <?php
    }
    $size = get_option ("ravatar_size");
    if (empty ($size))
        add_option ("ravatar_size", AVATAR_SIZE);

    //give a warning if the image functions are not available
    if (!function_exists (imagecreatetruecolor)) {
        echo '<div class="error"><p><strong>NOTE: It appears as though the GD2 Library for PHP  is not available to Wordpress. This plugin will still be able to display Gravatars ';
        if (!get_option ('ravatar_gravatars'))
            echo '(if you enable them below) ';
        echo 'but it can\'t build ravatars.';
        echo '</strong></p></div>';
    }

    ?>
<table class="optiontable">
<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<?php
	 if (function_exists('add_meta_box')) {  // Add options for 2.5.X
?>
     <p>Your theme must be avatar enabled in order to use Ravatars. An avatar enabled theme contains get_avatar in the comments loop.</p>
     <p>The settings on this page will override avatar settings on the Settings > Discussion page.</p><br/>
<?php }
	// Add options for all versions
	ravatar_row ('Gravatar Support', 'ravatar_gravatars', "Use Gravatars if available, and only use ravatars if the user doesn't have a Gravatar.", true);
?>
    <tr valign="top"><th scope="row">Gravatar Rating</th><td>
    <input name="ravatar_rating" type="radio" value="G" class="tog"       <?php checked(get_option ('ravatar_rating'), 'G'); ?> /> G<br>
    <input name="ravatar_rating" type="radio" value="PG" class="tog"       <?php checked(get_option ('ravatar_rating'), 'PG'); ?> /> PG<br>
    <input name="ravatar_rating" type="radio" value="R" class="tog"       <?php checked(get_option ('ravatar_rating'), 'R'); ?> /> R<br>
    <input name="ravatar_rating" type="radio" value="X" class="tog"       <?php checked(get_option ('ravatar_rating'), 'X'); ?> /> X<br>
    </td></tr>
<?php
	 if (!function_exists('add_meta_box')) { // Add options for pre 2.5
		 ravatar_row ('Automatic Placement', 'ravatar_auto', "This will cause wordpress to always display the image directly before the user's name. If you uncheck this, you will have to add avatars to your theme manually by calling <code>ravatar_show(\$comment-&gt;comment_author_email)</code> in your theme comment loop.", true);
		 ravatar_row ('Size', 'ravatar_size', 'The size of the icons.  Note that you should clear the cache if you change this.');
		 ravatar_row ('Prefix', 'ravatar_prefix', 'HTML to come BEFORE the image.  This can be useful for encasing the icon within &lt;div&gt; tags, for example.');
		 ravatar_row ('Suffix', 'ravatar_suffix', 'HTML to come AFTER the image.  This is good for closing any tags you may have opened with the Prefix.');
		 ravatar_row ('Border', 'ravatar_border', 'The size of the border around the icons.');
	 }
?>
<tr valign="top"><th scope="row">When user leaves email field blank:</th><td>
<input name="ravatar_email_blank" type="radio" value="" class="tog"       <?php checked(get_option ('ravatar_email_blank'), ''); ?> /> Generate ravatar anyway<br>
<input name="ravatar_email_blank" type="radio" value="omit" class="tog"   <?php checked(get_option ('ravatar_email_blank'), 'omit'); ?> /> Show no image<br>
<input name="ravatar_email_blank" type="radio" value="blank" class="tog"  <?php checked(get_option ('ravatar_email_blank'), 'blank'); ?> /> Display a blank Image<br>
</td></tr>
<?php
    ravatar_row ('Ravatar Support', 'ravatar_noplug', "Suppress the link back to the Ravatar homepage in the site footer.", true);
    echo '</table>';


?>

</table>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'mt_trans_domain' ) ?>" />
</p>
</table>
</form>
<hr />
<h3>Cache</h3>

<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<p>If you change the image size or alter the source images, you will need to clear the cached images for the changes
to take effect on existing icons.  You can also do this in order to free up disk space.  You can do this manually
by deleting all of the files in the <tt>/wp-content/plugins/ravatar/cache</tt> directory, or you can press the button below.

<?php
    $localdir = dirname(__FILE__) . "/cache";
    $dir = opendir ($localdir);
    if ($dir) {
        $file_count = 0;
        $delete_count = 0;
        while (($file = readdir ($dir)) !== false) {
            //only delete .png files.
            if (!strstr ($file, '.png'))
                continue;
            $file_count++;
        }
        if ($file_count == 0)
            echo 'The cache is currently empty.';
        else
            echo "There are $file_count icons currently in cache.";
    } else
        echo 'Cannot open directory for reading.';
?>

<p class="submit"><input type="hidden" name="ravatar_clear_cache" value="Y">
<input type="submit" name="Submit" value="Empty Cache" />
</form>
</div>
<?php

}

/*-----------------------------------------------------------------------------
Add ravatar admin pages to WP interface.
-----------------------------------------------------------------------------*/

function ravatar_add_pages ()
{

    if (function_exists('add_theme_page'))
        add_options_page('Ravatar Options', 'Ravatars', 8, 'ravataroptions', 'ravatar_options');
}

/*-----------------------------------------------------------------------------
This puts a link back to me in the page footer.  You can disable it in the
options or here in the source, but you'll hurt my feelings. :)
-----------------------------------------------------------------------------*/

function ravatar_plug ()
{
    
    if (get_option ('ravatar_noplug'))
        return;
    echo 'This site employs the <a href="http://grok-code.com/7/ravatar-wordpress-plugin-for-randam-avatars/">Ravatars plugin</a>.';

}


/*-----------------------------------------------------------------------------
Builds the avatar.
-----------------------------------------------------------------------------*/
function get_img_num($src_pics, $orig_num_pic, $num_pic) {

	// wrap around
	if ($num_pic >= count($src_pics)) {
		$num_pic = 0;
	}

	// if there weren't any src images, return a new blank img
	if ($orig_num_pic == $num_pic + 1) {
		return 0;
	}

	// Make sure file is supported format
	$pattern_jpg = "/.*(jpg|jpeg)$/i";
	$pattern_png = "/.*(png)$/i";

	$src_pics_dir = dirname(__FILE__).'/parts/';
	$file_name = $src_pics_dir . $src_pics[$num_pic];
	if ($src_pics[$num_pic] === '.' || $src_pics[$num_pic] === '..') {
		return get_img_num($src_pics, $orig_num_pic, $num_pic + 1);
	} else if (0 == preg_match($pattern_jpg, $src_pics[$num_pic]) && 0 == preg_match($pattern_png, $src_pics[$num_pic])) {
		return get_img_num($src_pics, $orig_num_pic, $num_pic + 1);
	} else if (0 != preg_match($pattern_jpg, $src_pics[$num_pic])) {
		return $num_pic;
	} else if (0 != preg_match($pattern_png, $src_pics[$num_pic])) {
		return $num_pic;
	}
}

function get_img($file_name) {

	$pattern_jpg = "/.*(jpg|jpeg)$/i";
	$pattern_png = "/.*(png)$/i";

	if (0 != preg_match($pattern_jpg, $file_name)) {
		return imagecreatefromjpeg($file_name);
	} else if (0 != preg_match($pattern_png, $src_pics[$num_pic])) {
		return imagecreatefrompng($file_name);
	}

}

function ravatar_build ($seed, $filename, $size) {

	$src_pics_dir = dirname(__FILE__).'/parts/';
	$src_pics = scandir($src_pics_dir);
		
	$pic = (hexdec (substr ($seed,  1, 5)) % (count($src_pics) - 2));
	$num = get_img_num($src_pics, $pic, $pic);
	if ($num == 0) {
		$src_img = imagecreatetruecolor (AVATAR_SIZE, AVATAR_SIZE);
	} else {
		$src_img_name = dirname(__FILE__) . '/parts/' . $src_pics[$num];
		$src_img = get_img($src_img_name);
	}
	list($width, $height) = getimagesize($src_img_name);	
		
	$width_off = (hexdec(substr ($seed, 6, 5)) % ($width - AVATAR_SIZE));
	$height_off = (hexdec(substr ($seed, 11, 5)) % ($height - AVATAR_SIZE));

	$avatar = imagecreatetruecolor (AVATAR_SIZE, AVATAR_SIZE);
	//imagecopy($avatar, $src_img, 0, 0, $width_off, $height_off, AVATAR_SIZE, AVATAR_SIZE); 
	imagecopy($avatar, $src_img, 0, 0, $width_off, $height_off, AVATAR_SIZE, AVATAR_SIZE); 
	
    //resize if needed
    if ($size != AVATAR_SIZE) {
        $out = imagecreatetruecolor($size,$size);
        imagecopyresampled ($out,$avatar, 0, 0, 0, 0, $size, $size, AVATAR_SIZE, AVATAR_SIZE);
        imagepng($out, $filename);
        imagedestroy($out);
        imagedestroy($avatar);
    } else {
        imagepng($avatar, $filename);
        imagedestroy($avatar);
    }

}

/*-----------------------------------------------------------------------------
Builds a blank 1x1 avatar
-----------------------------------------------------------------------------*/

function ravatar_build_blank ()
{

    if (file_exists (RAVATAR_BLANK))
        return;
    $avatar = imagecreatetruecolor (1, 1);
    $bg = imagecolorallocate ($avatar, 255, 255, 255);
    imagefill($avatar, 0, 0, $bg);
    imagepng($avatar, RAVATAR_BLANK);
    imagedestroy($avatar);
    
}

/*-----------------------------------------------------------------------------
This makes sure that the image is present (builds it if it isn't) and then
returns the url.
-----------------------------------------------------------------------------*/

function ravatar_get ($email, $size='')
{

    $email = strtolower ($email);
    $email_blank = get_option ('ravatar_email_blank');
    if ($email == '') {
        if (get_option ('ravatar_email_blank') == 'omit')
            return '';
        if (get_option ('ravatar_email_blank') == 'blank') {
            ravatar_build_blank ();
            return RAVATAR_BLANK;
        }
    }
    $md5 = md5($email);
    $seed = substr ($md5, 0, 17);

    $rating = get_option ('ravatar_rating');
	if ($rating != '') {
		$rating = "rating=" . $rating;
	}
	
    if ($size == '')
        $size = get_option ("ravatar_size");
    if ($size == 0)
        $size = AVATAR_SIZE;
    //make sure the image functions are available before trying to make ravatars
    if (function_exists (imagecreatetruecolor)) {
        //make sure the cache directory is available
		$localdir = dirname(__FILE__) . "/cache";
        if (!file_exists ($localdir) && !wp_mkdir_p ($localdir)) {
			echo "cannot make $localdir";
            return;
		}
		$dest = $localdir . "/$seed.png";
        $url = get_bloginfo ('siteurl') . '/wp-content/plugins/ravatar/cache' . "/$seed.png";
        if (!file_exists ($dest)) {
            ravatar_build ($seed, $dest, $size);
		}
    } else //image functions not available
        $url == '';
    if (get_option ('ravatar_gravatars'))
        $url = "http://www.gravatar.com/avatar.php?gravatar_id=$md5&amp;$rating&amp;size=$size&amp;default=$url";
    return $url;

}

/*-----------------------------------------------------------------------------
This makes sure that the image is present (builds it if it isn't) and then
displays it.
-----------------------------------------------------------------------------*/

function ravatar_show ($email, $size='')
{

    $email = strtolower ($email);
    if (get_option ('ravatar_email_blank') == 'omit' && $email == '')
        return '';
    if ($size == '')
        $size = get_option ("ravatar_size");
    if ($size == 0)
        $size = AVATAR_SIZE;
    $email = strtolower ($email);
    $url = ravatar_get ($email, $size);
    echo get_option('ravatar_prefix');
    echo "<img class='ravatar' border='$border' src='$url' width='$size' height='$size' alt=''/>";
    echo get_option('ravatar_suffix');

}

/*-----------------------------------------------------------------------------

-----------------------------------------------------------------------------*/

function ravatar_comment_author ($author)
{

    global $comment;

    if (is_page () || is_single ()) {
        if (get_option ('ravatar_auto'))
            return ravatar_show ($comment->comment_author_email) . " " . $author;
    }
    return $author;

}

if (get_option ('ravatar_auto') == '') {
    add_option ('ravatar_auto', true);
}
add_action('admin_menu', 'ravatar_add_pages');
add_filter('get_comment_author','ravatar_comment_author',1);
if (function_exists('add_options_page')) {
    add_options_page('optionspagetitle', 'optionspagename', 1, basename(__FILE__));
}
?>
