/*
Copyright 2008 Diana Kantor and John Keck

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

function playVideo(title, description, embed_code) 
{ 
    parent.document.getElementById('vp_video_player').innerHTML = '<div class="vp_video_title">' + title + '</div><div class="vp_embed_holder">' + embed_code +'</div><div class="vp_video_description">'+description+'</div>';
}
