/* global nivoSliderParams */
/**
 * Theme functions file.
 *
 * Contains handler for navigation.
 */

( function( $ ) {
	$( document ).ready( function() {
		
		if($.trim($('#secondary').html()) == ''){
			$('#secondary').hide();
			$('#primary').removeClass('grid-70 tablet-grid-70');
			$('#primary').addClass('grid-100 tablet-grid-100');
		}
		
		// Sticky Header
		$(window).scroll(function() {
		  var headerHeight = $('header#masthead').innerHeight();
		  
		  var wpadminbarHeight = 0;
		  if($('#wpadminbar').length > 0)
			wpadminbarHeight = $('#wpadminbar').innerHeight();
		  //console.log('headerHeight: ' + headerHeight);
		  if ($(this).scrollTop() > headerHeight){
			$('#navi').addClass("sticky-nav");
			$('#navi.sticky-nav').css('top', wpadminbarHeight);
		  }
		  else{
			$('#navi').removeClass("sticky-nav");
		  }
		});
		
		$('#main-menu').smartmenus({
			subMenusSubOffsetX: 1,
			subMenusSubOffsetY: -6,
			markCurrentItem: true,
			markCurrentTree: false
		});
		$('#menu-button').click(function() {
			var $this = $(this),
				$menu = $('#main-menu');
			if ($menu.is(':animated')) {
				return false;
			}
			if (!$this.hasClass('collapsed')) {
				$menu.slideUp(250, function() { $(this).addClass('collapsed').css('display', ''); });
				$this.addClass('collapsed');
			} else {
				$menu.slideDown(250, function() { $(this).removeClass('collapsed'); });
				$this.removeClass('collapsed');
			}
			return false;
		});
		// Speaker
		$('.speaker-item-clickable').live('click', function(e){
			var $cId = $(this).data('id');
			$('#speaker-content').html($('#' + $cId + '-content').html());
			$('.speaker-menu-a').removeClass('active');
			$('#' + $cId).addClass('active');
		});
		if(window.location.hash) {
			var hash = window.location.hash.substring(1);
			var isClicked = false;
			$.each($('.speaker-item-clickable'), function(){
				if($(this).data('name') == hash){
					$(this).trigger('click');
					isClicked = true;
				}	
			});
			if(!isClicked)
				$('#all-speakers').trigger('click');
		} else {
			$('#all-speakers').trigger('click');
		}
		
		// Staff
		$('.staff-clickable').live('click', function(e){
			var that = this;
			var $cId = $(this).data('id');
			var $sectionId = $(this).data('section');
			var $sectionObj = $('#staff-content' + $sectionId);
			var $sectionStaffId = $sectionObj.data('staff-id');
			var $isShowing = $sectionObj.is(':visible');
			
			// Hide all section first
			$('.staff-section-content').slideUp();
			$('.staff-clickable').removeClass('current');
			
			// Fill content first
			$sectionObj.find('.content-detail').html($('#' + $cId + '-content').html());
			
			// Click twice on same staff
			if($cId == $sectionStaffId) {
				if(!$isShowing){
					$(that).addClass('current');
					//$('.staff-section-content').css('width', '100%');
					$sectionObj.show("slow", function() {
						$('.staff-section-content').jScrollPane({showArrows: true});
					});
				}
			} else {
				$(that).addClass('current');
				//$('.staff-section-content').css('width', '100%');
				$sectionObj.show("slow", function() {
					$('.staff-section-content').jScrollPane({showArrows: true});
				});
			}
			
			$('#staff-content' + $sectionId).data('staff-id', $cId);
		});
		// Close
		$('.staff-close-button').live('click', function(e){
			// Hide all section first
			$(this).closest('.staff-section-content').slideUp();
		});
	});
	$( window ).load( function() {
		$('#slider').nivoSlider({
			effect: 'random',
			animSpeed: nivoSliderParams.animspeed,
			pauseTime: nivoSliderParams.pausetime,
			controlNav: true,
			pauseOnHover: true
		});
	});
} )( jQuery );