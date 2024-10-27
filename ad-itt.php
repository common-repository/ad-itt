<?php
/*
Plugin Name: Ad Itt
Plugin URI: http://wordpress.org/extend/plugins/ad-itt/
Description: Adds advertisements to your website.

Installation:

1) Install WordPress 3.9 or higher

2) Download the following file:

http://downloads.wordpress.org/plugin/ad-itt.zip

3) Login to WordPress admin, click on Plugins / Add New / Upload, then upload the zip file you just downloaded.

4) Activate the plugin.

Version: 2.0
Author: TheOnlineHero - Tom Skroza
License: GPL2
*/

if (!class_exists("AdIttTomM8")) {
  require_once("lib/tom-m8te.php");
}

add_action( 'admin_init', 'register_ad_itt_search_settings' );
function register_ad_itt_search_settings() {
  $filter_image_name = $_POST["filter_image_name"];
  if ($filter_image_name != "") {
    $images = AdIttTomM8::get_results("posts", "*", "post_type='attachment' AND post_title LIKE '%$filter_image_name%' AND post_mime_type IN ('image/png', 'image/jpg', 'image/jpeg', 'image/gif')", array("post_date DESC"), "7");
    echo "<ul id='images'>";
    foreach ($images as $image) { 
        ?>
        <li>
          <img style='width: 100px; min-height: 100px' src='<?php echo($image->guid); ?>' />
        </li>

    <?php }
    echo "</ul>";
    exit();
  }
} 

add_action( 'admin_init', 'register_ad_itt_upload_settings' );
function register_ad_itt_upload_settings() {
  $uploadfiles = $_FILES['uploadfiles'];

  if (is_array($uploadfiles)) {

    foreach ($uploadfiles['name'] as $key => $value) {

      // look only for uploded files
      if ($uploadfiles['error'][$key] == 0) {

        $filetmp = $uploadfiles['tmp_name'][$key];

        //clean filename and extract extension
        $filename = $uploadfiles['name'][$key];

        // get file info
        // @fixme: wp checks the file extension....
        $filetype = wp_check_filetype( basename( $filename ), null );
        $filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
        $filename = $filetitle . '.' . $filetype['ext'];
        $upload_dir = wp_upload_dir();

        /**
         * Check if the filename already exist in the directory and rename the
         * file if necessary
         */
        $i = 0;
        while ( file_exists( $upload_dir['path'] .'/' . $filename ) ) {
          $filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
          $i++;
        }
        $filedest = $upload_dir['path'] . '/' . $filename;

        /**
         * Check write permissions
         */
        if ( !is_writeable( $upload_dir['path'] ) ) {
          $this->msg_e('Unable to write to directory %s. Is this directory writable by the server?');
          return;
        }

        /**
         * Save temporary file to uploads dir
         */
        if ( !@move_uploaded_file($filetmp, $filedest) ){
          $this->msg_e("Error, the file $filetmp could not moved to : $filedest ");
          continue;
        }

        $attachment = array(
          'post_mime_type' => $filetype['type'],
          'post_title' => $filetitle,
          'post_content' => '',
          'post_status' => 'inherit',
        );

        $attach_id = wp_insert_attachment( $attachment, $filedest );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filedest );
        wp_update_attachment_metadata( $attach_id,  $attach_data );
        preg_match("/\/wp-content(.+)$/", $filedest, $matches, PREG_OFFSET_CAPTURE);
        AdIttTomM8::update_record_by_id("posts", array("guid" => get_option("siteurl").$matches[0][0]), "ID", $attach_id);
        echo $filedest;
      }
    }   
  }
}

function ad_itt_activate() {
  add_option( "css_content_wrapper_selector", "", "", "yes" );
  add_option( "enable_left_ad_link", "", "", "yes" );
  add_option( "left_ad_link", "", "", "yes" );
  add_option( "left_ad_img", "", "", "yes" );
  add_option( "left_ad_position", "", "", "yes" );
  add_option( "enable_right_ad_link", "", "", "yes" );
  add_option( "right_ad_link", "", "", "yes" );
  add_option( "right_ad_img", "", "", "yes" );
  add_option( "right_ad_position", "", "", "yes" );

  add_option( "enable_top_ad_link", "", "", "yes" );
  add_option( "top_ad_link", "", "", "yes" );
  add_option( "top_ad_img", "", "", "yes" );
  add_option( "top_ad_retract_time", "", "", "yes" );
  add_option( "top_ad_close_img", "", "", "yes" );
  add_option( "top_ad_position", "", "", "yes" );
  add_option( "enable_bottom_ad_link", "", "", "yes" );
  add_option( "bottom_ad_link", "", "", "yes" );
  add_option( "bottom_ad_img", "", "", "yes" );
  add_option( "bottom_ad_position", "", "", "yes" );

}
register_activation_hook( __FILE__, 'ad_itt_activate' );

add_action('admin_menu', 'register_ad_itt_page');

function register_ad_itt_page() {
   add_menu_page('Ad Itt', 'Ad Itt', 'manage_options', 'ad-itt/ad-itt.php', 'ad_itt_settings_page');
}

//call register settings function
add_action( 'admin_init', 'register_ad_itt_settings' );
function register_ad_itt_settings() {
  //register our settings
  register_setting( 'ad-itt-group', 'css_content_wrapper_selector' );
  register_setting( 'ad-itt-group', 'enable_left_ad_link' );
  register_setting( 'ad-itt-group', 'left_ad_link' );
  register_setting( 'ad-itt-group', 'left_ad_img' );
  register_setting( 'ad-itt-group', 'left_ad_position' );
  register_setting( 'ad-itt-group', 'enable_right_ad_link' );
  register_setting( 'ad-itt-group', 'right_ad_link' );
  register_setting( 'ad-itt-group', 'right_ad_img' );
  register_setting( 'ad-itt-group', 'right_ad_position' );

  register_setting( 'ad-itt-group', 'enable_top_ad_link' );
  register_setting( 'ad-itt-group', 'top_ad_link' );
  register_setting( 'ad-itt-group', 'top_ad_img' );
  register_setting( 'ad-itt-group', 'top_ad_retract_time' );
  register_setting( 'ad-itt-group', 'top_ad_close_img' );
  register_setting( 'ad-itt-group', 'top_ad_position' );
  register_setting( 'ad-itt-group', 'enable_bottom_ad_link' );
  register_setting( 'ad-itt-group', 'bottom_ad_link' );
  register_setting( 'ad-itt-group', 'bottom_ad_img' );
  register_setting( 'ad-itt-group', 'bottom_ad_position' );
}

function are_ad_itt_dependencies_installed() {
  return is_plugin_active("jquery-colorbox/jquery-colorbox.php");
}

add_action( 'admin_notices', 'ad_itt_notice_notice' );
function ad_itt_notice_notice(){
  $activate_nonce = wp_create_nonce( "activate-ad-itt-dependencies" );
  $jquery_colorbox = is_plugin_active("jquery-colorbox/jquery-colorbox.php");
  if (!($jquery_colorbox)) { ?>
    <div class='updated below-h2'><p>Before you can use Ad Itt, please install/activate:</p>
    <ul>
      <?php 

      if (!$jquery_colorbox) { ?>
        <li>
          <a target="_blank" href="http://wordpress.org/extend/plugins/jquery-colorbox/">JQuery Colorbox</a>
           &#8211; 
          <?php if (file_exists(ABSPATH."/wp-content/plugins/jquery-colorbox/jquery-colorbox.php")) { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/?ad_itt_install_dependency=jquery-colorbox&_wpnonce=<?php echo($activate_nonce); ?>">Activate</a>
          <?php } else { ?>
            <a href="<?php echo(get_option("siteurl")); ?>/wp-admin/plugin-install.php?tab=plugin-information&plugin=jquery-colorbox&_wpnonce=<?php echo($activate_nonce); ?>&TB_iframe=true&width=640&height=876">Install</a> 
          <?php } ?>
        </li>
      <?php } ?>
    </ul>
    </div>
    <?php
  }

}

add_action( 'admin_init', 'register_ad_itt_install_dependency_settings' );
function register_ad_itt_install_dependency_settings() {
  if (isset($_GET["ad_itt_install_dependency"])) {
    if (wp_verify_nonce($_REQUEST['_wpnonce'], "activate-ad-itt-dependencies")) {
      switch ($_GET["ad_itt_install_dependency"]) {
        case 'jquery-colorbox':
          activate_plugin('jquery-colorbox/jquery-colorbox.php', 'plugins.php?error=false&plugin=jquery-colorbox.php');
          wp_redirect(get_option("siteurl")."/wp-admin/admin.php?page=ad-itt/ad-itt.php");
          exit();
          break;   
        default:
          throw new Exception("Sorry unable to install plugin.");
          break;
      }
    } else {
      die("Security Check Failed.");
    }
  }
}



function ad_itt_settings_page() {
?>

<?php
    wp_enqueue_script('jquery');
    wp_register_script( 'my-jquery-colorbox', get_option("siteurl")."/wp-content/plugins/jquery-colorbox/js/jquery.colorbox-min.js" );
    wp_enqueue_script('my-jquery-colorbox');
    wp_register_script( 'my-form-script', plugins_url('/js/jquery.form.js', __FILE__) );
    wp_enqueue_script('my-form-script');
    wp_register_style( 'my-jquery-colorbox-style',get_option("siteurl")."/wp-content/plugins/jquery-colorbox/themes/theme1/colorbox.css");
    wp_enqueue_style('my-jquery-colorbox-style');
?>

<script type="text/javascript">
  jQuery(function() {
    var current_input;
    jQuery(".image-uploader").click(function() {
      current_input = jQuery(this).prev("input");
      jQuery.colorbox({inline:true, href:"#upload_image_container", width: "940px", height: "550px"});
    });

    jQuery("#filter_image_name").live("keydown", function() {
        if (jQuery(this).val().length < 2) {
          jQuery("#images_container").html("");
        } else {
          jQuery.post("<?php echo(get_option('siteurl')); ?>/wp-admin/admin.php?page=ad-itt/ad-itt.php", { filter_image_name: jQuery(this).val() },
              function(data) {
                jQuery("#images_container").html(data);
              }
          );
        }
    });

    jQuery("#images img").live("click", function() {
      jQuery(current_input).val(jQuery(this).attr("src"));
      jQuery("#cboxClose").click();
    });

    var bar = jQuery('.bar');
    var percent = jQuery('.percent');
    jQuery(".percent").hide();
    jQuery('#uploadfile_form').ajaxForm({
        beforeSend: function() {
            jQuery(".percent").hide();
            var percentVal = '0%';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        uploadProgress: function(event, position, total, percentComplete) {
            jQuery(".percent").show();
            var percentVal = percentComplete + '%';
            bar.width(percentVal)
            percent.html(percentVal);
        },
        complete: function(xhr) {
            jQuery(".percent").hide();
            jQuery("#filter_image_name").val(jQuery("#uploadfiles").val().match("[a-z|A-Z|\.|-|_]*$")[0]);
            jQuery("#filter_image_name").val(jQuery("#filter_image_name").val().replace(new RegExp("\.[a-z|A-Z]*$","i"),""));
            jQuery("#filter_image_name").keydown();
        }
    }); 
    
  });
</script>
<style>
  #upload_image_container, #images {display: none;}
  #cboxWrapper #upload_image_container, #cboxWrapper #images {display: block;}
  ul#images li {float: left; margin-right: 5px;}
  .hint { color: #008000;}
  .inside th {text-align: left;}
  .inside table {margin-left: 10px;}
  tr.odd th {background: #cac9c9;}
  tbody tr.odd td, tr.odd th {background: #dfdfdf;}
  .inside table {width: 100%;}
  th.enable-col {width: 20px;text-align: center;}
</style>

<?php if (isset($_GET["settings-updated"]) && $_GET["settings-updated"]=="true") { ?>
  <div id="message" class="updated below-h2"><p>Ad Itt Updated</p></div>
<?php } ?>
<div id="upload_image_container">
  <div class="wrap">
<h2>Ad Itt</h2>
<div class="postbox " style="display: block; ">
<div class="inside">
  <table class="form-table">
    <tbody>

      <tr valign="top">
        <th scope="row">
          <label for="filter_image_name">Upload</label>
        </th>
        <td>
          <form name="uploadfile" id="uploadfile_form" method="POST" enctype="multipart/form-data" action="#uploadfile" accept-charset="utf-8" >
            <input type="file" name="uploadfiles[]" id="uploadfiles" size="35" class="uploadfiles" />
            <input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="Upload"  />
          </form>
          <div class="progress">
              <div class="bar"></div >
              <div class="percent">0%</div >
          </div>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">
          <label for="filter_image_name">Search</label>
        </th>
        <td>
          <input type="text" id="filter_image_name" name="filter_image_name" value="" />
        </td>
      </tr>
      <tr>
        <td></td>
        <td><div id="images_container"></div></td>
      </tr>
    </tbody>
  </table>
</div>
</div>
</div>
</div>

<div class="wrap">
<h2>Ad Itt</h2>
<div class="postbox " style="display: block; ">
<div class="inside">
<form method="post" action="options.php">
  <?php settings_fields( 'ad-itt-group' ); ?>
  <table class="form-table">
    <thead>
      <tr>
        <th class="enable-col">Enable/Disable</th>
        <th colspan="2"></th>
      </tr>
    </thead>
    <tbody>
      <tr valign="top" class="odd">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="css_content_wrapper_selector">Css Content Wrapper Selector</label>
        </th>
        <td>
          <input type="text" name="css_content_wrapper_selector" value="<?php echo get_option('css_content_wrapper_selector'); ?>" />
          <span class="hint">Example: #content, #main-content, .content</span>
        </td>
      </tr>

      <tr valign="top" class="even">
        <th class="enable-col">
          <input type="hidden" name="enable_top_ad_link" value="" />
          <input type="checkbox" value="on" name="enable_top_ad_link" <?php if (get_option("enable_top_ad_link") == "on") { echo "checked"; } ?>/>
        </th>
        <th scope="row">
          <label for="top_ad_link">Top Ad Link</label>
        </th>
        <td>
          <input type="text" id="top_ad_link" name="top_ad_link" value="<?php echo get_option('top_ad_link'); ?>" />
        </td>
      </tr>

      <tr valign="top" class="even">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="top_ad_img">Top Ad Image</label>
        </th>
        <td>
          <input type="text" id="top_ad_img" name="top_ad_img" value="<?php echo get_option('top_ad_img'); ?>" />
          <input type="button" class="image-uploader" value="Upload" />
          <span>Sets the width of image as 100%.</span>
        </td>
      </tr>

      <tr valign="top" class="even">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="top_ad_close_img">Top Ad Close Image</label>
        </th>
        <td>
          <input type="text" id="top_ad_close_img" name="top_ad_close_img" value="<?php echo get_option('top_ad_close_img'); ?>" />
          <input type="button" class="image-uploader" value="Upload" />
        </td>
      </tr>

      <tr valign="top" class="even">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="top_ad_retract_time">Top Ad Time Limit (in milliseconds)</label>
        </th>
        <td>
          <input type="text" id="top_ad_retract_time" name="top_ad_retract_time" value="<?php echo get_option('top_ad_retract_time'); ?>" />
        </td>
      </tr>

      <tr valign="top" class="even">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="top_ad_position">Top Ad Position</label>
        </th>
        <td>
          <select id="top_ad_position" name="top_ad_position">
            <option value="relative" <?php if (get_option("top_ad_position") == "relative") {echo("selected");}?> >Relative</option>
            <option value="absolute" <?php if (get_option("top_ad_position") == "absolute") {echo("selected");}?> >Absolute</option>
          </select>
        </td>
      </tr>

      <tr valign="top" class="odd">
        <th class="enable-col">
          <input type="hidden" name="enable_bottom_ad_link" value="" />
          <input type="checkbox" value="on" name="enable_bottom_ad_link" <?php if (get_option("enable_bottom_ad_link") == "on") { echo "checked"; } ?>/>
        </th>
        <th scope="row">
          <label for="bottom_ad_link">Bottom Ad Link</label>
        </th>
        <td>
          <input type="text" id="bottom_ad_link" name="bottom_ad_link" value="<?php echo get_option('bottom_ad_link'); ?>" />
        </td>
      </tr>

      <tr valign="top" class="odd">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="bottom_ad_img">Bottom Ad Image</label>
        </th>
        <td>
          <input type="text" id="bottom_ad_img" name="bottom_ad_img" value="<?php echo get_option('bottom_ad_img'); ?>" />
          <input type="button" class="image-uploader" value="Upload" />
          <span>Sets the width of image as 100%.</span>
        </td>
      </tr>

<!--       <tr valign="top" class="odd">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="bottom_ad_position">Bottom Ad Position</label>
        </th>
        <td>
          <select id="bottom_ad_position" name="bottom_ad_position">
            <option value="absolute" <php if (get_option("bottom_ad_position") == "absolute") {echo("selected");}> >Absolute</option>
            <option value="static" <php if (get_option("bottom_ad_position") == "static") {echo("selected");}> >Static</option>
          </select>
        </td>
      </tr> -->

      <tr valign="top" class="even">
        <th class="enable-col">
          <input type="hidden" name="enable_left_ad_link" value="" />
          <input type="checkbox" value="on" name="enable_left_ad_link" <?php if (get_option("enable_left_ad_link") == "on") { echo "checked"; } ?>/>
        </th>
        <th scope="row">
          <label for="left_ad_link">Left Ad Link</label>
        </th>
        <td>
          <input type="text" id="left_ad_link" name="left_ad_link" value="<?php echo get_option('left_ad_link'); ?>" />
        </td>
      </tr>

      <tr valign="top" class="even">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="left_ad_img">Left Ad Image</label>
        </th>
        <td>
          <input type="text" id="left_ad_img" name="left_ad_img" value="<?php echo get_option('left_ad_img'); ?>" />
          <input type="button" class="image-uploader" value="Upload" />
          <span>Sets the width of image as 221px.</span>
        </td>
      </tr>

      <tr valign="top" class="even">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="left_ad_position">Left Ad Position</label>
        </th>
        <td>
          <select id="left_ad_position" name="left_ad_position">
            <option value="fixed" <?php if (get_option("top_ad_position") == "fixed") {echo("selected");}?> >Fixed</option>
            <option value="absolute" <?php if (get_option("top_ad_position") == "absolute") {echo("selected");}?> >Absolute</option>
          </select>
        </td>
      </tr>

      <tr valign="top" class="odd">
        <th class="enable-col">
          <input type="hidden" name="enable_right_ad_link" value="" />
          <input type="checkbox" value="on" name="enable_right_ad_link" <?php if (get_option("enable_right_ad_link") == "on") { echo "checked"; } ?>/>
        </th>
        <th scope="row">
          <label for="right_ad_link">Right Ad Link</label>
        </th>
        <td>
          <input type="text" id="right_ad_link" name="right_ad_link" value="<?php echo get_option('right_ad_link'); ?>" />
        </td>
      </tr>

      <tr valign="top" class="odd">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="right_ad_img">Right Ad Image</label>
        </th>
        <td>
          <input type="text" id="right_ad_img" name="right_ad_img" value="<?php echo get_option('right_ad_img'); ?>" />
          <input type="button" class="image-uploader" value="Upload" />
          <span>Sets the width of image as 221px.</span>
        </td>
      </tr>

      <tr valign="top" class="even">
        <th class="enable-col"></th>
        <th scope="row">
          <label for="right_ad_position">Right Ad Position</label>
        </th>
        <td>
          <select id="right_ad_position" name="right_ad_position">
            <option value="fixed" <?php if (get_option("right_ad_position") == "fixed") {echo("selected");}?> >Fixed</option>
            <option value="absolute" <?php if (get_option("right_ad_position") == "absolute") {echo("selected");}?> >Absolute</option>
          </select>
        </td>
      </tr>

    </tbody>
  </table>
  <p class="submit">
    <input type="submit" name="Submit" value="Update Options">
  </p>
  </form>
</div>
</div>
</div>
<?php 
  AdIttTomM8::add_social_share_links("http://wordpress.org/extend/plugins/ad-itt");
}


add_action('wp_head', 'add_ad_itt_js_and_css');
function add_ad_itt_js_and_css() { 
  wp_enqueue_script('jquery');

  // embed the javascript file that makes the AJAX request
  wp_enqueue_script( 'my-ad-itt-ajax-request', plugin_dir_url( __FILE__ ) . 'js/ad-itt.js', array( 'jquery' ) );

  // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
  wp_localize_script( 'my-ad-itt-ajax-request', 'AdIttAjax', array( 
      "css_content_wrapper_selector" => get_option("css_content_wrapper_selector"),
      "enable_left_ad_link" => get_option("enable_left_ad_link"),
      "left_ad_link" => get_option("left_ad_link"),
      "left_ad_img" => get_option("left_ad_img"),
      "left_ad_position" => get_option("left_ad_position"),
      "enable_right_ad_link" => get_option("enable_right_ad_link"),
      "right_ad_link" => get_option("right_ad_link"),
      "right_ad_img" => get_option("right_ad_img"),
      "right_ad_position" => get_option("right_ad_position"),
      "enable_top_ad_link" => get_option("enable_top_ad_link"),
      "top_ad_link" => get_option("top_ad_link"),
      "top_ad_img" => get_option("top_ad_img"),
      "top_ad_retract_time" => get_option("top_ad_retract_time"),
      "top_ad_close_img" => get_option("top_ad_close_img"),
      "top_ad_position" => get_option("top_ad_position"),
      "enable_bottom_ad_link" => get_option("enable_bottom_ad_link"),
      "bottom_ad_link" => get_option("bottom_ad_link"),
      "bottom_ad_img" => get_option("bottom_ad_img"),
      "bottom_ad_position" => get_option("bottom_ad_position")
    ) );

  wp_register_style( 'my-ad-itt-style', plugins_url('/css/ad-itt.css?20130115b', __FILE__) );
    wp_enqueue_style('my-ad-itt-style');
} ?>