=== Plugin Name ===
Contributors: xondie
Donate link: http://www.xondie.com/resources/
Tags: video, gallery, vodpod
Requires at least: 2.0.2
Tested up to: 3.0.1
Stable tag: 2.1

Creates a video gallery page from a Vodpod collection. Vodpod allows you to add videos from any site that supports EMBEDs.

== Description ==

This plugin was upgraded to use the new version 2 of the vodpod API. It now requires you to configure both your vodpod username AND your vodpod collection ID (not the "name" you assign to the collection).  If you only have 1 collection, it is likely that the ID of that collection is the same as your vodpod username. 

This plugin is based on Vodpod and creates a video gallery from a Vodpod account. Vodpod is a great tool for collecting videos. You can download a browser toolbar so that all you have to do is click a button when you are viewing a video and it will insert it into your Vodpod account, which will then allow it to appear in your WordPress gallery automatically. So get yourself a Vodpod account and then give this plugin a try.

Multiple video galleries within one WordPress site is also supported. You may also specify a tag name in order to limit your gallery to a subset of your vodpod videos.

This plugin looks best on themes that have a content area that is wider than the video player (425px) which is most of them. It has been tested in Firefox, Safari, IE 7, and IE 6. If you find bugs, please let me know and I will try to fix them as soon as possible.

== Installation ==

This plugin uses WordPress "short codes".

1. Copy the vodpod-video-gallery folder to your wordpress plugins directory.
1. Activate the Vodpod Video Gallery plugin from your admin plugins page.
1. Go to the "Vodpod Video Gallery" page in the "Settings" menu and configure your Vodpod account name and collection ID. 
1. Put the text `[vodpod_video_gallery]` into any page.
   Alternatively, for multiple pods on one wordpress site, you can specify the vodpod user and/or collection:
   [`vodpod_video_gallery` user="vodpod_username" collection="collection_id"]
   You can also specify collection name, number of thumbnails per page, and (optional) height of the iframe that holds the thumbnails:
   [`vodpod_video_gallery` collection="collection_id" `per_page`="16" `iframe_height`="280"]
   You may also specify a particular tag:
   [`vodpod_video_gallery` collection="collection_id" tag="tagname"]
   
How to determine your collection ID: The collection ID is different from the name you assign to the collection. If you only have 1 collection, it is likely that the ID of that collection is the same as your vodpod username.  To determine the ID of your collection, go to your vodpod page at vodpod.com/username. Click on the dropdown  above your videos to find the option for your desired collection and click it to go to that page. Look at the URL for your collection page. The collection ID will be the last part of the URL, which will be structured like this: http://www.vodpod.com/username/collection_id
   
The iframe height is now optional. You do not need to specify it. By default, the plugin will determine how high to make the iframe based on number of videos per page and how many lines of pagination you have. You can override the iframe height if you need to.

If you have problems with the `file_get_contents()` function, you can either try creating a php.ini file in your root WP directory and add the line `allow_url_fopen = On` or you can try the alternative curl version of this plugin, which works better for some servers, located at http://www.xondie.com/temp/vodpod-video-gallery-curl-4.zip. If you use this though, remember to ignore any suggestions from WP to upgrade the Vodpod Video Gallery plugin, as this will overwrite your alternative curl version. Also, this version does not get updated very often so it might be missing some bug fixes. Let me know if you need something updated on it and I'll do what I can. Really try to get the regular version working first. Configuration errors are the most common reason why the plugin does not work at first.