<?php
/* FootNote plugin
   Version 0.4.1
   By gofeel (gofeel_AT_gmail_DOT_com)
   License : General Public License
   Visit http://bringbring.com/entry/FootNote for the detail


   General Public License
   http://www.gnu.org/licenses/gpl.html

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

*/

function FootNote($target, $mother) {
	global $configVal,$pluginURL;
	requireComponent('Tattertools.Function.misc');
	$data = misc::fetchConfigVal( $configVal);
	if (!is_null($data)) {
		$back_text=$data['back_caption'];
		if ($data['back_option']) $back_text="<img src=\"".$pluginURL."/image/".$data['back_icon']."\" align=\"absmiddle\" style=\"margin-left:5px;\" />";
		$title_text="";
		if ($data['title_option']==1) $title_text=$data['title_caption'];
		if ($data['title_option']==2) $title_text="<img src=\"".$pluginURL."/image/".$data['title_icon']."\" style=\"margin-bottom:5px;\" />";
		if ($data['rt_1']) $patterns[]="\[footnote\](.*?)\[\/footnote\]";
		if ($data['rt_2']) $patterns[]="\(주:(.*?)\)";
		if ($data['rt_3']) $patterns[]="\[fn\](.*?)\[\/fn\]";
		if ($data['rt_4']) $patterns[]="\[각주\](.*?)\[\/각주\]";
		
	}

	if (!isset($back_text)) $back_text= "[Back]";
	if (!isset($patterns)) $patterns[]="\[footnote\](.*?)\[\/footnote\]";
	$footnote="<div class=footnotes>".$title_text."<div class=footnotes_in><ol class=footnotes>";
	$full_match_text="";

	foreach($patterns as $pattern)
	{
		$full_match_text.="(".$pattern.")|";
	}
	$full_match_text="/".substr($full_match_text,0,-1)."/i";

	$Count = preg_match_all($full_match_text,$target,$matches);
    for ($i=0;$Count>$i;$i++) {
	$num=$i+1;
		$selected_text=$matches[0][$i];
		foreach($patterns as $pattern)
		{
			if(preg_match("/".$pattern."/",$selected_text,$match))
			{
				$note_text=$match[1];
				break;
			}
		}			
		
		//$footnotelink="<span style=\"cursor: pointer;\" class=\"footnoteslink\" title=\"".$note_text."\"><sup>".$num."</sup></span>";
		$footnotelink="<sup style=\"font-family:tahoma;\"><a href=\"#footnote_".$mother."_".$num."\" id=\"footnote_link_".$mother."_".$num."\">".$num."</a></sup>";
		$target=str_replace($selected_text,$footnotelink,$target);
		$footnote.="<li id=\"footnote_".$mother."_".$num."\">".$note_text." <a href=\"#footnote_link_".$mother."_".$num."\">".$back_text."</a> </li>\n";
	}
	$footnote.="</ol></div></div>";
	if ($Count && $data['note_style']!=1) { $target.=$footnote; }
	return $target;
}
?>
<?php
function FootNoteIcon($target) {
	global $pluginURL, $service, $owner, $s_wysiwyg;
	ob_start();
?>
	<script type="text/javascript">
	//<![CDATA[
		function AddFootNote(){
			var isWYSIWYG = false;
			try{
				if(editor.editMode == "WYSIWYG")
					isWYSIWYG = true;
			}
			catch(e){ }
			if(isWYSIWYG) {
				var note = prompt('이 곳에 넣을 주석을 적어주세요.', '');
				if(note!='') { TTCommand("Raw", '[footnote]' + note + '[/footnote]', "");}
			}else{
				insertTag('[footnote]', '[/footnote]');
			}
		}
	//]]>
	</script>
	<script type="text/javascript">
		editor.contentDocument.body.innerHTML = editor.ttml2html(editor.textarea.value);
	</script>
	<a href="#" tabindex="125" onClick="AddFootNote();return false;" alt="" title=""/>주석추가</a>&nbsp;
<?php
	$script = ob_get_contents();
	ob_end_clean();
	return $script.$target;
}
?>
<?php
function FootNoteCss($target) {
	global $configVal;
	requireComponent('Tattertools.Function.misc');
	$data = misc::fetchConfigVal( $configVal);
	$css_text[0]="";
	$css_text[1]="<style type=\"text/css\">ol.footnotes{padding:0px;margin:0px 0px 0px 25px;}div.footnotes {border: 1px solid #DDDDDD; padding:4px; margin:0px; background-color:#ffffff;}div.footnotes_in {border: 0px solid #DDDDDD; padding:0px; margin:0px; background-color:#ffffff;}</style>";
	$css_text[2]="<style type=\"text/css\">div.footnotes {padding:5px; background-color:#eaf4ff;}</style>";
	return $css_text[$data['css']].$target;
};
?>