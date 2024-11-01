<?php
/*
Copyright 2009 Diana Kantor and John Keck

This file is part of Vodpod Video Gallery WordPress Plugin.

Vodpod Video Gallery WordPress Plugin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Vodpod Video Gallery WordPress Plugin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Vodpod Video Gallery WordPress Plugin.  If not, see <http://www.gnu.org/licenses/>.

Plugin Name: Vodpod Video Gallery
Plugin URI: http://www.xondie.com/resources
Description: Creates a video gallery page from a Vodpod account, which lets you post videos from any site that supports EMBEDs.
Author: Xondie
Version: 3.1.7
Author URI: http://www.xondie.com
*/


/* ADMIN MENU */
function videogallery_add_admin() {
	add_options_page('Vodpod Video Gallery', 'Vodpod Video Gallery', 9, 'videogallery', 'videogallery_admin_options');
}

add_action('admin_menu', 'videogallery_add_admin');

/* Global options page */
function videogallery_admin_options() 
{

    if (isset($_POST['info_update'])) 
    {

		echo '<div id="message" class="updated fade"><p><strong>';

		update_option('videogallery_user', $_POST['videogallery_user']);
		update_option('videogallery_collection', $_POST['videogallery_collection']);
		update_option('videogallery_tag', $_POST['videogallery_tag']);
		update_option('videogallery_per_page', $_POST['videogallery_per_page']);
		update_option('videogallery_iframe_height', $_POST['videogallery_iframe_height']);

		echo 'Configuration Updated!';

		echo '</strong></p></div>';

	}
	
	if (get_option('videogallery_per_page') == '')
		update_option('videogallery_per_page', 8);
		
	if (get_option('videogallery_iframe_height') == '')
		update_option('videogallery_iframe_height', '');

	?>
	<div class="wrap">
	<h2>Vodpod Video Gallery</h2>

	<p>For information and updates, please visit:<br />
	<a href="http://www.xondie.com/resources/vodpod-video-gallery-plugin-for-wordpress/">http://www.xondie.com/resources/vodpod-video-gallery-plugin-for-wordpress/</a></p>

	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<input type="hidden" name="info_update" id="info_update" value="true" />

	<p>To insert a Vodpod Video Gallery into a page or post enter this text into the content area:<br/>[vodpod_video_gallery].</p>
	<p>Alternatively, if you have multiple pods that you are using, you can enter this text:<br/>[vodpod_video_gallery collection="collection_id"], where "collection_id" is the ID of your vodpod collection (not the "name" you assign to the collection). </p>
	<p>Or you can also specify a different vodpod user:<br/>
	[vodpod_video_gallery user="vodpod_username" collection="collection_id"]
	</p>
	<p>You can also specify the number of video thumbnails per page and the height of the iframe that displays the thumbnails:<br/>[vodpod_video_gallery collection="collection_id" per_page="16" iframe_height="280"]</p>
	<p>You can also limit your gallery to a specific tag:<br/>[vodpod_video_gallery collection="collection_id" tag="tagname"]</p>
    <p><b>How to determine your collection ID:</b> The collection ID is different from the name you assign to the collection. If you only have 1 collection, it is likely that the ID of that collection is the same as your vodpod username.  To determine the ID of your collection, go to your vodpod page at vodpod.com/username. Click on the dropdown  above your videos to find the option for your desired collection and click it to go to that page. Look at the URL for your collection page. The collection ID will be the last part of the URL, which will be structured like this: http://www.vodpod.com/username/collection_id</p>
	<table width="100%" border="0" cellspacing="0" cellpadding="6">

	<tr valign="top">
	<td width="25%" align="right">
		<b>Vodpod username</b>
	</td>
	<td align="left">
		<input name="videogallery_user" type="text" size="15" value="<?php echo trim(get_option('videogallery_user')); ?>" /> 
		<i>The username on your vodpod account.</i>
	</td>
	</tr>

	<tr valign="top">
	<td width="25%" align="right">
		<b>Vodpod collection ID</b>
	</td>
	<td align="left">
		<input name="videogallery_collection" type="text" size="15" value="<?php echo trim(get_option('videogallery_collection')); ?>" /> 
		<i>The ID (not the name) of the vodpod collection you wish to display by default. See above for how to determine your collection ID.
		</i>
	</td>
	</tr>

	<tr valign="top">
	<td width="25%" align="right">
		<b>Vodpod tag (optional)</b>
	</td>
	<td align="left">
		<input name="videogallery_tag" type="text" size="15" value="<?php echo trim(get_option('videogallery_tag')); ?>" /> 
		<i>A specific tag to limit the videos returned.</i>
	</td>
	</tr>

	<tr valign="top"><td width="25%" align="right">
		<b>Videos per page (default is 8)</b>
	</td><td align="left">
		<input name="videogallery_per_page" type="text" size="3" value="<?php echo trim(get_option('videogallery_per_page')); ?>" /> 
		<i>The number of thumbnails that will be shown on each page of the video gallery.</i>
	</td></tr>

	<tr valign="top"><td width="25%" align="right">
		<b>Thumb iframe height (optional)</b>
	</td><td align="left">
		<input name="videogallery_iframe_height" type="text" size="3" value="<?php echo trim(get_option('videogallery_iframe_height')); ?>" /> 
		<i>The height of the iframe that holds the gallery thumbnails. If left blank, the iframe will do its best to autosize to fit the given number of thumbnails and pagination links.</i>
	</td></tr>

	</table>

	<div class="submit">
		<input type="submit" name="info_update" value="<?php _e('Update options'); ?> &raquo;" />
	</div>
	</form>

	</div>
	<?php
}


function videogallery_public_head() {
}
add_action('wp_head', 'videogallery_public_head');

function vvg_shortcode($atts, $content=null) 
{
    $user;
    $collection;
    $tag;
    $per_page;
    $iframe_height;
	extract(shortcode_atts(array(
	    'user' => '',
		'collection' => '',
		'tag' => '',
		'per_page' => '',
		'iframe_height' => ''
	), $atts));
	if($user == '')
	    $user = get_option('videogallery_user');
	if($collection == '')
	    $collection = get_option('videogallery_collection');
	if($tag == '')
	    $tag = get_option('videogallery_tag');
	if($per_page == '')
	    $per_page = get_option('videogallery_per_page');
	if($per_page == '')
	    $per_page = 8;
	if($iframe_height == '')
	    $iframe_height = get_option('videogallery_iframe_height');
	
    if(!$collection)
	    print "<p><b>Site administrator:</b> Please set your vodpod collection name in your settings. If you only have one collection in your vodpod account, it is likely that your collection name will be the same as your vodpod account name.</p>";
    if(!$user)
	    print "<p><b>Site administrator:</b> Please set your vodpod user name in your settings.</p>";

    if($collection && $user)
    {
	    $tag = str_replace(' ','_sp_',$tag);
	    return videogallery_get_html($user, $collection, $tag, $per_page, $iframe_height);
	}
	else return "";
}
add_shortcode('vodpod_video_gallery', 'vvg_shortcode');


function videogallery_get_html($user, $collection, $tag, $per_page, $iframe_height)
{
    $video_id = $_GET["vid"];
    $gallery_id = get_the_ID().str_replace(' ','-',$collection.$tag);
    if(!$video_id) $video_id=0;

    $return_content = '<div id="vp_gallery" class="vp_gallery">'."\n";
    $return_content .= '<div class="vp_gallery_top"></div>'."\n";
    $return_content .= '<div class="vp_container">'."\n";
    $return_content .= '    <div id="vp_video_player_'.$gallery_id.'" class="vp_video_player">'."\n";

    $total_videos = "";
    $vodpod_user = $user;
    $vodpod_collection = $collection;
    $vodpod_tag = str_replace('_sp_','%20',$tag);

    $videos_per_page = $per_page;
    	
    $tag_params="";
    if($vodpod_tag)
        $tag_params = "&tag_mode=any&tags=".$vodpod_tag;
    $api_url = "http://api.vodpod.com/v2/users/".strtolower($vodpod_user)."/collections/".strtolower($vodpod_collection)."/videos.xml?api_key=1993b813d6cc8293&sort=ranking&per_page=1".$tag_params;

	if($video_id > 0)
	{
    	$xml_parser_vidid = xml_parser_create();
	    $vodpod_vidid_parser = new VodpodMainVideoParser();
	    xml_set_object($xml_parser_vidid, $vodpod_vidid_parser);
	    xml_set_element_handler($xml_parser_vidid, "startElement", "endElement");
	    xml_set_character_data_handler($xml_parser_vidid, "characterData");
	    $api_url_vidid = "http://api.vodpod.com/v2/users/".strtolower($vodpod_user)."/collections/".strtolower($vodpod_collection)."/videos/".$video_id."?api_key=1993b813d6cc8293";

    	$data = file_get_contents($api_url_vidid);
    	xml_parse($xml_parser_vidid, $data)
			or die(sprintf("XML error: %s at line %d", 
				xml_error_string(xml_get_error_code($xml_parser)), 
				xml_get_current_line_number($xml_parser)));

        $return_content .= $vodpod_vidid_parser->getcontent();
        
		xml_parser_free($xml_parser_vidid);
	}
	    
	$xml_parser = xml_parser_create();
	$vodpod_main_parser = new VodpodMainVideoParser();
	xml_set_object($xml_parser, $vodpod_main_parser);
	xml_set_element_handler($xml_parser, "startElement", "endElement");
	xml_set_character_data_handler($xml_parser, "characterData");

	$data = file_get_contents($api_url);
	
	if(!$data)
	    print "<br/><br/><p><b>There was a problem with the request to Vodpod.</b> This is most often caused by an error in your plugin settings. Please make sure that you used a valid vodpod account name and collection name. You can test this by putting the vodpod call directly into your browser:<br/><a href=\"".$api_url."\" target=\"_blank\">".$api_url."</a></p><br/><br/>";	
 
    else
    {
	    xml_parse($xml_parser, $data)
			or die(sprintf("XML error: %s at line %d", 
				xml_error_string(xml_get_error_code($xml_parser)), 
				xml_get_current_line_number($xml_parser)));
	
	    if($video_id<=0)	//only display if not already gotten from video id
            $return_content .= $vodpod_main_parser->getcontent();
	}        
				
	$total_videos = trim($vodpod_main_parser->getTotalVideos());
	
	xml_parser_free($xml_parser);
	
	if($iframe_height!='')
        $iframe_height = $iframe_height;
    else if(get_option('videogallery_iframe_height'))
        $iframe_height = get_option('videogallery_iframe_height');
    else
    {
        if($total_videos >= $videos_per_page)
            $iframe_height = (ceil($videos_per_page/4)*85 + ceil(ceil($total_videos/$videos_per_page)/22)*16)+20;       
        else
            $iframe_height = (ceil($total_videos/4)*85 + 36);
    }

    $return_content .= '    </div><!-- VP_VIDEO_PLAYER -->'."\n";
    $return_content .= '    <iframe src="'.get_bloginfo('wpurl').'/wp-content/plugins/vodpod-video-gallery/vodpod_gallery_thumbs.php?u='.$vodpod_user.'&amp;c='.$vodpod_collection.'&amp;t='.$tag.'&amp;vpp='.$videos_per_page.'&amp;gid='.$gallery_id.'" width="100%" height="'.$iframe_height.'" frameborder="0" marginwidth="0" marginheight="0" hspace="0" vspace="0" scrolling="no" style="border-style:none"></iframe>'."\n";
    $return_content .= '</div><!-- VP_CONTAINER -->'."\n";
    $return_content .= '<div class="vp_gallery_bottom">'."\n";
    $return_content .= '    <span id="vp_bottom_left"><a href="http://vodpod.com/'.strtolower($vodpod_user).'/'.strtolower($vodpod_collection).'" target="new">'.$vodpod_user.'\'s videos</a></span>'."\n";
    $return_content .= '    <span id="vp_bottom_right"><a href="http://xondie.com/resources/" target="new">Video Gallery by Xondie</a> | <a href="http://vodpod.com" target="new">Powered by VodPod</a></span>'."\n";
    $return_content .= '</div>'."\n";
    $return_content .= '</div><!-- VP_GALLERY -->'."\n";
    
    return $return_content;
}


/* Load CSS into WP header */
add_action('wp_head', 'videogallery_add_css');

function videogallery_add_css() {
	echo "\n" . '<link href="'.get_bloginfo('wpurl').'/wp-content/plugins/vodpod-video-gallery/gallery.css" media="all" rel="Stylesheet" type="text/css" />';
}

class VodpodMainVideoParser {

	var $insideitem = false;
	var $ignore_host_description = true;
	var $tag = "";
	var $title = "";
	var $video_id = "";
	var $description = "";
	var $link = "";
	var $embed = "";
	var $thumbnail = "";
	var $orig_url = "";
	var $total_videos;
	var $return_content;
	var $error_msg;

	function getTotalVideos()
	{
	    return $this->total_videos;
	}
	
	function getContent()
	{
	    return $this->return_content;
	}

	function startElement($parser, $tagName, $attrs) {
	    if ($tagName == "ERROR")
	    {
	        $this->tag = $tagName;
	        $this->insideItem = true;
	        print "FOUND ERROR";
	    }
        if ($tagName == "VIDEOS") {
			$this->total_videos = $attrs['TOTAL'];
		}
		elseif ($this->insideitem) {
			$this->tag = $tagName;
		} elseif ($tagName == "COLLECTION_VIDEO" || $tagName == "VIDEO") {
			$this->insideitem = true;
			$this->ignore_host_description = true;
		}
	    if ($tagName == "VIDEO_HOST")
	    {
	        if(trim($this->description)=="")
	            $this->ignore_host_description = false;
	        else
	            $this->ignore_host_description = true;
	    }
    }

	function endElement($parser, $tagName) 
	{
		if ($tagName == "COLLECTION_VIDEO" || $tagName == "VIDEO") 
		{
            $content = '<div class="vp_video_title">'.trim($this->title).'</div>'."\n";
            $content .= '<div class="vp_embed_holder">';
            $content .= $this->embed;
            $content .= '</div>'."\n";
            $content .= '<div class="vp_video_description">'."\n";
            $content .= trim($this->description);
           // if($this->orig_url)
           //     $content .= '<p><a href="'.$this->orig_url.'" target="new">Original Video URL</a></p>'."\n";
            $content .= '</div>'."\n";
        
			$this->title = "";
			$this->description = "";
			$this->link = "";
			$this->embed = "";
			$this->thumbnail = "";
			$this->insideitem = false;
			$this->orig_url = "";
			$this->error_msg = "";
			
			$this->return_content = $content;
		}
		if ($tagName == "ERROR")
		{
		    $content .= "<p><b>Error returned from Vodpod: </b>".$this->error_msg."</p>";
			$this->error_msg = "";
		}
	}

	function characterData($parser, $data) {
		if ($this->insideitem) {
			switch ($this->tag) {
				case "KEY":
				$this->video_id .= trim($data);
				break;
				case "TITLE":
				$this->title .= str_replace('\'', '\\\'', $data);
				break;
				case "DESCRIPTION":
				if(!$this->ignore_host_description)
				{
			    	$desc = str_replace("\n", '<br/>', $data);
		            $desc = str_replace("\r", ' ', $desc);
				    $this->description .= $desc;
				}
				break;
				case "ORIGINAL_URL":
				$this->orig_url .= trim($data);
				break;
				case "EMBED":
				$this->embed .= preg_replace('/(height|HEIGHT|Height)={1}"[0-9]{2,3}[px;%\s]*"/', 'height="355px"', $data);
				$this->embed = preg_replace('/(width|WIDTH|Width)={1}"[0-9]{2,3}[px;%\s]*"/', 'width="425px"', $this->embed);
				$this->embed = preg_replace('/style="[0-9a-zA-Z%;:\s]*"/', 'height="355px" width="425px"', $this->embed);
				$this->embed = str_ireplace('width="1" height="1"', 'height="1" alt="" width="1"', $this->embed);
				break;
				case "LINK":
				$this->link .= $data;
				break;
				case "MESSAGE":
				$this->error_msg .= $data;
				break;
			}
		}
	}
}

?>
