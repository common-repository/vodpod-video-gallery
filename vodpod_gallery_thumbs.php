<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

*/
?>
<html xmlns="http://www.w3.org/1999/xhtml">       
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Video Gallery Thumbnails</title>
<link href="gallery.css" media="all" rel="Stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function playVideo(title, description, embed_code, galleryname) 
{ 
    parent.document.getElementById('vp_video_player_'+galleryname).innerHTML = '<div class="vp_video_title">' + title + '</div><div class="vp_embed_holder">' + embed_code +'</div><div class="vp_video_description">'+description+'</div>';
}
</script>
<style type="text/css">
html {
	margin: 0 0 20px 0;
	padding: 0;
}
body {
	width: 436px; 
	margin: 0 auto; 
}
</style>
<!--[if IE]>
<style type="text/css">
body {
	width: 446px; 
	text-align: center
}
body>.vp_video_thumbs {
	width: 436px;
}
</style>
<![endif]--> 

</head>
<body bgcolor="#F1F1F1">
    <div style="opacity: 0.999999;" id="vp_video_thumbs">
<?php
$gallery_id;
if (isset($_POST['gid']))
    $gallery_id = $_POST['gid'];
elseif (isset($_GET['gid']))
    $gallery_id = $_GET['gid'];

class VodpodVideoListParser {

	var $insideitem = false;
	var $ignore_host_description = true;
	var $tag = "";
	var $title = "";
	var $description = "";
	var $orig_url = "";
	var $link = "";
	var $autoplay_embed = "";
	var $thumbnail = "";
	var $total_videos;
	var $video_id = "";
	var $gallery_id;
	
	function getTotalVideos()
	{
	    return $this->total_videos;
	}
	
	function setGalleryId($gallery_id)
	{
	    $this->gallery_id = $gallery_id;
	}

	function startElement($parser, $tagName, $attrs) {
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
		    $desc = trim($this->description);
		    $esc_title = str_replace('\'', '\\\'', trim($this->title));
		    
		    if($this->orig_url)
		        $desc .= '<p><a href="'.$this->orig_url.'" target="new">Original Video URL</a></p>';
			echo '<div id="video_'.$this->video_id.'" class="video_thumb"><a onclick="javascript:playVideo(\''.htmlspecialchars($esc_title).'\', \''.htmlspecialchars($desc).'\', \''.trim(str_replace('"','\\\'',$this->autoplay_embed)).'\',\''.$this->gallery_id.'\')"><img class="thumbnail" src="'.trim($this->thumbnail).'" alt="video thumbnail" /></a>';
			echo '<div class="thumb_title_box"></div>';
			echo '<div class="thumb_title"><a onclick="javascript:playVideo(\''.htmlspecialchars($esc_title).'\', \''.htmlspecialchars(trim($desc)).'\',\''.trim(str_replace('"','\\\'',$this->autoplay_embed)).'\',\''.$this->gallery_id.'\')">';
			echo htmlspecialchars($this->title).'</a>';
			echo '</div>';
			echo '</div>';
        
			$this->title = "";
			$this->description = "";
			$this->orig_url = "";
			$this->link = "";
			$this->autoplay_embed = "";
			$this->thumbnail = "";
			$this->insideitem = false;
		}
	}

	function characterData($parser, $data) {
		if ($this->insideitem) {
			switch ($this->tag) {
				case "KEY":
				$this->video_id .= trim($data);
				break;
				case "TITLE":
				$this->title .= trim($data);
				break;
				case "DESCRIPTION":
				if(!$this->ignore_host_description)
				{
				    $desc = str_replace('\'', '\\\'', $data);
				    $desc = str_replace("\n", '<br/>', $desc);
				    $desc = str_replace("\r", ' ', $desc);
				    $this->description .= $desc;
				}
				break;
				case "ORIGINAL_URL":
				$this->orig_url .= trim($data);
				break;
				case "AUTOPLAY_EMBED":
				$this->autoplay_embed .= preg_replace('/(height|HEIGHT|Height)={1}"[0-9]{2,3}[px;%\s]*"/', 'height="355px"', $data);
				$this->autoplay_embed = preg_replace('/(width|WIDTH|Width)={1}"[0-9]{2,3}[px;%\s]*"/', 'width="425px"', $this->autoplay_embed);
				$this->autoplay_embed = preg_replace('/style="[0-9a-zA-Z%;:\s]*"/', 'height="355px" width="425px"', $this->autoplay_embed);
				$this->autoplay_embed = str_ireplace('width="1" height="1"', 'height="1" alt="" width="1"', $this->autoplay_embed);
				$this->autoplay_embed = str_replace("\n", '', $this->autoplay_embed);
				$this->autoplay_embed = str_replace("\r", '', $this->autoplay_embed);
				break;
				case "LINK":
				$this->link .= $data;
				break;
				case "THUMBNAIL":
				$this->thumbnail .= $data;
				break;
			}
		}
	}
}

$page_num;
if (isset($_POST['cur_page']))
    $page_num = $_POST['cur_page'];
elseif (isset($_GET['page']))
    $page_num = $_GET['page'];
else $page_num = 1;

$videos_per_page;
if(isset($_POST['vpp']) && $_POST['vpp']>0)
    $videos_per_page = $_POST['vpp'];
elseif(isset($_GET['vpp']) && $_GET['vpp']>0)
    $videos_per_page = $_GET['vpp'];
else $videos_per_page = 8;
    
$vodpod_user;
if(isset($_POST['u']))
    $vodpod_user = $_POST['u'];
elseif(isset($_GET['u']))
    $vodpod_user = $_GET['u'];

$vodpod_collection;
if(isset($_POST['c']))
    $vodpod_collection = $_POST['c'];
elseif(isset($_GET['c']))
    $vodpod_collection = $_GET['c'];

$tag;
if(isset($_POST['t']))
    $tag = $_POST['t'];
elseif(isset($_GET['t']))
    $tag = $_GET['t'];
$vodpod_tag = str_replace('_sp_','%20',$tag);
    
$xml_parser = xml_parser_create();
$vodpod_parser = new VodpodVideoListParser();
$vodpod_parser->setGalleryId($gallery_id);
xml_set_object($xml_parser, $vodpod_parser);
xml_set_element_handler($xml_parser, "startElement", "endElement");
xml_set_character_data_handler($xml_parser, "characterData");

$tag_params="";
if($vodpod_tag)
    $tag_params = "&tag_mode=any&tags=".$vodpod_tag;

$data = file_get_contents("http://api.vodpod.com/v2/users/".strtolower($vodpod_user)."/collections/".strtolower($vodpod_collection)."/videos.xml?api_key=1993b813d6cc8293&sort=ranking&page=".$page_num."&per_page=".$videos_per_page.$tag_params);

xml_parse($xml_parser, $data)
		or die(sprintf("XML error: %s at line %d", 
			xml_error_string(xml_get_error_code($xml_parser)), 
			xml_get_current_line_number($xml_parser)));

xml_parser_free($xml_parser);

$total_videos = $vodpod_parser->getTotalVideos();
$is_next = ($total_videos>($page_num*$videos_per_page));
$is_previous = ($page_num>1);
$num_of_pages = ceil($total_videos/$videos_per_page);

?>
    </div>
   	   	<div class="vg_pagination" style="text-align:<?php if($num_of_pages>12) print "left"; else print "center";?>;">
		<?php 
		
		if($is_previous)
		{
		    print '<span><a class="vg_page" href="?u='.$vodpod_user.'&amp;c='.$vodpod_collection.'&amp;t='.$tag.'&amp;vpp='.$videos_per_page.'&amp;page='.($page_num-1).'&amp;gid='.$gallery_id.'" title="previous page">&laquo; previous</a></span>'."\n";
		}
		else
		    print '<span class="vg_page">&laquo; previous</span>'."\n";
		    
		for($i=1; $i<=$num_of_pages; $i++)
		{ 
		    //print $i+1;
		    if($page_num!=$i)
		        print '<span><a class="vg_page" href="?u='.$vodpod_user.'&amp;c='.$vodpod_collection.'&amp;t='.$tag.'&amp;vpp='.$videos_per_page.'&amp;page='.$i.'&amp;gid='.$gallery_id.'">'.$i.'</a></span>'."\n";
		    else
		        print '<span class="vg_page current_vg_page">'.$i.'</span>'."\n";
		}
		
		if($is_next)
		{
		    print '<span><a class="vg_page" href="?u='.$vodpod_user.'&amp;c='.$vodpod_collection.'&amp;t='.$tag.'&amp;vpp='.$videos_per_page.'&amp;page='.($page_num+1).'&amp;gid='.$gallery_id.'" title="next page">next &raquo;</a></span>'."\n";
		}
		else
		    print '<span class="vg_page">next &raquo;</span>'."\n";		    
		?>
	</div><!-- VG_PAGINATION -->

</body>
</html>