jQuery.noConflict();
jQuery(function($) {	
	function changeSize(element,size)
	{
		var fsize;
		var current=parseInt(element.css('font-size'));
		if(size=="smaller")
		{
			fsize=current-2;
			if(fsize>=10)
			{
				element.css('font-size',fsize+'px');
			}
		}
		if(size=="bigger")
		{
			fsize=current+2;
			if(fsize<=40)
			{
				element.css('font-size',fsize+'px');
			}
		}	
	}

	$('.plusfont').on('click',function(){
		changeSize($('body'),'bigger');
	})
	$('.minusfont').on('click',function(){
		changeSize($('body'),'smaller');
	})
	$('.reloadfont').on('click',function(){
		$('html body').css('font-size','');
	})
});