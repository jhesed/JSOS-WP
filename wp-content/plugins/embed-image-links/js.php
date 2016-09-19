<?php
header('Content-Type: application/javascript');

$iMaxWidthPercentage = $_GET['max-width-percentage'];
if ( !$iMaxWidthPercentage ) {
	$iMaxWidthPercentage = 100;
}
$iMaxWidthPercentage = $iMaxWidthPercentage / 100;
?>

if (typeof jQuery == "undefined") {
	var oJqueryScript = document.createElement('script');
	oJqueryScript.setAttribute('type', 'text/javascript');
	oJqueryScript.setAttribute('src', '//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js');
	document.getElementsByTagName('body')[0].appendChild( oJqueryScript );
}

jQuery(window).load(function() {
	var oImg = jQuery('.embedded-image-link');
	
	oImg.each(function() {
		var oParent = jQuery(this).parent();
		if ( jQuery(this).outerWidth() > oParent.outerWidth() * <?php echo $iMaxWidthPercentage; ?> ) {
			jQuery(this).attr("width", oParent.outerWidth() * <?php echo $iMaxWidthPercentage; ?>);
		}
	});
});