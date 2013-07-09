<?php
function drawTab($text,$from,$to) {
	global $default_page;
	print('<div class="tab"><div class="tabbox" onClick="ajaxLoad(\''.$from.'\',\''.$to.'\')">'.$text.'</div></div>');
	if ($default_page==$text) {
		$default_page=$from;
	}
}
function drawFullBackground($src) {
	print("<img src=\"$src\" style=\"position:fixed;top:0;left:0;width:100%;height:100%;z-index:-20\" />");
}
function includeCSS($src) {
	print('<link rel="stylesheet" type="text/css" href="'.$src.'">');
}
function includeJS($src) {
	print('<script src="'.$src.'"></script>');
}
function includeJQ() {
	print('<script src="http://code.jquery.com/jquery-latest.js"></script>');
}
function metaCharset($set) {
	print('<meta http-equiv="Content-Type" content="text/html" charset="'.$set.'" />');
}
?>