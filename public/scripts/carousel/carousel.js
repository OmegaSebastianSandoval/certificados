/*
Autor: Carlos Alberto Rodriguez Laiton
*/
(function($){
	$.fn.carousel = function(options) {
		var defaults = {
			pause: 5000,
			auto: 'true',
			animationTime: 500,
			quantity:5,
			sizes : new Array(),
			automatic:''
		}
		$(this).each(function(){
			var element = $(this);
			var settings = $.extend({}, defaults, options);
			element.find(".right_scroll").click(function(){rotate('Right');});
			element.find(".left_scroll").click(function(){rotate('Left');});
			function init(){
				defineWidth();
				autorotate();
			}
			function defineWidth(){
				if(settings.sizes.length === 0){
					width = (element.find('.carousel_inner').outerWidth()/settings.quantity);
				}else{
					widthWindow= $(window).width();
					current='';
					higher='';
					$.each(settings.sizes,function(k,v){
						if(parseInt(k)>= parseInt(widthWindow)){
							if(parseInt(k)<parseInt(current) || current == ''){
								current = k;
							}
						}
						if(higher=='' || parseInt(k) >parseInt(higher)){
							higher = k ;
						}
					});
					if(current!='' && parseInt(higher) >= parseInt($(window).width()) ){
						if(parseInt(settings.sizes[current])>0){
							width = (element.find('.carousel_inner').outerWidth()/settings.sizes[current]);
						}else{
							width = (element.find('.carousel_inner').outerWidth()/settings.quantity);
						}
					}else{
						width = (element.find('.carousel_inner').outerWidth()/settings.quantity);
					}
				}
				element.find('ul li').css({'width': width+"px" });
			}

			function rotate(direction){
				autorotate(element);
				var item_width =parseInt(element.find('ul li').outerWidth()) + parseInt(element.find('ul li').css("marginLeft"))+ parseInt(element.find('ul li').css("marginRight"));
				if(direction=='Left'){
					var left_indent = parseInt(element.find('ul').css('left')) + item_width;
					element.find('ul:not(:animated)').animate({'left' : left_indent},settings.animationTime,function(){
						element.find('ul li:first').before(element.find('ul li:last'));
						element.find('ul').css({'left' : '0px'});
					});
				}else{
					var left_indent = parseInt(element.find('ul').css('left')) - item_width;
					element.find('ul:not(:animated)').animate({'left' : left_indent},settings.animationTime,function(){
						element.find('ul li:last').after(element.find('ul li:first'));
						element.find('ul').css({'left' : '0px'});
					});
				}
			}
			function autorotate(){
				if( settings.auto != 'false'){
					clearInterval(settings.automatic);
					settings.automatic = setTimeout(function(){rotate('Right')},settings.pause);
				}
			}
			init();
			$( window ).resize(function() { defineWidth();});
		});
	}

})(jQuery);