var $ = jQuery.noConflict();

var windowHeight = $(window).height();
var windowWidth = $(window).width();
//console.log(new Date());

function StickyMenu() {
      
  if (windowWidth > 850) {
    //if ($('body').length) {
      $(window).on('scroll', function() {
          var winH = $(window).scrollTop();
          //console.log(winH);
          var $pageHeader = $('#nav_container');
          if (winH > 190) {
              $pageHeader.addClass('navbar-fixed-top');
          } else {
              $pageHeader.removeClass('navbar-fixed-top');
          }
      });
   // }
  }
};

function ConfirmCookiesPolicy() {
  createCookie('cookie_policy','set',168);
  $("#cookies_policy").remove();
}

function createCookie(name,value,hours) {
  var expires = "";
  if (hours) {
    var date = new Date();
    date.setTime(date.getTime()+(hours*60*60*1000));
    expires = "; expires="+date.toGMTString();
  }
  document.cookie = name+"="+value+expires+"; path=/";
}

function LoadPaginationNews(offset) {
  if(offset == "") return;
  $("#ajax_loader").show();
  setTimeout(function() {
      $("#ajax_loader").hide()
  }, 5e3);
  var news_category_id = $(".news_category_id").val();
  var current_lang = $(".current_lang").val();
  var language_id = $(".language_id").val();
  var news_count = $(".news_count").val();
  $.ajax({
    url: "/site/ajax/get-pagination-news.php",
    type: "POST",
    data: {
      news_category_id:news_category_id,
      offset: offset,
      news_count: news_count,
      current_lang:current_lang,
      language_id:language_id
    }
  }).done(function(news_blocks) {
    
    $('html, body').animate({scrollTop:0}, 'slow');
    $("#news_list").html(news_blocks);
    $("#ajax_loader").hide()
      
  }).fail(function(e) {
      console.log(e)
  })
}

StickyMenu();


//$('form#newsletterform').submit(function (event) {
//
//    var This = $(this);
//    var action = $(This).attr('action');
//
//    var data_value = decodeURI($(This).serialize());
//    $.ajax({
//        type: "POST",
//        url:  action,
//        data: data_value,
//        error: function (xhr, status, error) {
//          confirm('The page save failed.');
//        },
//        success: function (response) {
//          $('.ajax_inquiery_msg').html(response);
//          $('.ajax_inquiery_msg').slideDown('slow');
//          if (response.match('success') != null) $(This).slideUp('slow');
//        }
//    });
//
//    event.preventDefault();
//});

//TOGGLE-------------------------------------------------------
$(document).ready(function () {
	
	$('#toggle-view li').click(function () {

		var text = $(this).children('div.panel');

		if (text.is(':hidden')) {
			text.slideDown('10');
			$(this).children('.ui-accordion-header').addClass('ui-accordion-header-active');		
		} else {
			text.slideUp('10');
			$(this).children('.ui-accordion-header').removeClass('ui-accordion-header-active');		
		}

	});

});


$(document).ready(function(){
  
  $('[data-toggle="tooltip"]').tooltip();

  //ACCORDION-----------------------------------------------------
  $("#accordion").accordion({
    autoHeight: false,
     /*icons: { "header": "plus", "headerSelected": "minus" }*/
  });

  //DROPDOWN MENU--------------------------------------------------
  $('ul.sf-menu').superfish({
  autoArrows:  false,
  dropShadows: false
  });

  //ADAPTIVE MENU--------------------------------------------------
  // add select 
  $('<select />').appendTo('nav#main-nav');

  //add options to select
  $('<option />', {
      'selected': 'selected',
      'value' : '',
      'text': 'Choose Page...'
  }).appendTo('nav select');

  $('nav#main-nav ul li a').each(function(){
      var target = $(this);

      $('<option />', {
          'value' : target.attr('href'),
          'text': target.text()
      }).appendTo('nav#main-nav select');

  });

  //  onclicking 
  $('nav#main-nav select').on('change',function(){
      window.location = $(this).find('option:selected').val();
  });

  //FANCYBOX-------------------------------------------------------
  $(".lightbox").live("mousedown", function()
      { 
          $(this).fancybox(
          { 
              'titleShow'		: false,
              'overlayShow'	: false,
              'transitionIn'	: 'elastic',
              'transitionOut'	: 'elastic'
          });	
      });
  $("a.iframe").fancybox({ 
    'titleShow'		: true,
    'autoDimensions'    : true, 
    'width'				: 800,
    'height'			: 450,
    'autoScale'     	: true,
    'type'				: 'iframe'

  });
  
  //PORTFOLIO FILTER
  // Clone portfolio items to get a second collection for Quicksand plugin
  var $portfolioClone = $("#portfolio").clone();

  // Attempt to call Quicksand on every click event handler
  $("#filter a").click(function(e){

      $("#filter li").removeClass("current");	

      // Get the class attribute value of the clicked link
      var $filterClass = $(this).parent().attr("class");

      if ( $filterClass == "all" ) {
          var $filteredPortfolio = $portfolioClone.find("li");
      } else {
          var $filteredPortfolio = $portfolioClone.find("li[data-type~=" + $filterClass + "]");
      }

      // Call quicksand
      $("#portfolio").quicksand( $filteredPortfolio, { 
          duration: 800, 
          easing: 'swing' 
      });


      $(this).parent().addClass("current");

  })
    
  //CONTENT TABS---------------------------------------------------
  var $tabsNav    = $('.tabs-nav'),
      $tabsNavLis = $tabsNav.children('li'),
      $tabContent = $('.tab-content');

  $tabsNav.each(function() {
      var $this = $(this);

      $this.next().children('.tab-content').stop(true,true).hide()
                                           .first().show();

      $this.children('li').first().addClass('active').stop(true,true).show();
  });

  $tabsNavLis.on('click', function(e) {
      var $this = $(this);

      $this.siblings().removeClass('active').end()
           .addClass('active');

      $this.parent().next().children('.tab-content').stop(true,true).hide()
                                                    .siblings( $this.find('a').attr('href') ).fadeIn();

      e.preventDefault();
  });
  
  //SCROLL TO TOP----------------------------------------------------
  $('.scrollup').click(function () {
      $('body,html').animate({
          scrollTop: 0
      }, 600);
      return false;
  });
  
  $("table").each(function() {
    $(this).find("tbody tr:even").addClass("even");
    $(this).find("tbody tr:odd").addClass("odd");
  });
    
  //SUBSCRIBE FORM AJAX SUBMIT...
  $('form[name="newsletterform"]').submit(function (event) {

    var This = $(this);
    var action = $(This).attr('action');
    var data_value = decodeURI($(This).serialize());

    $.ajax({
      type: "POST",
      url: action,
      data: data_value,
      error: function (xhr, status, error) {
        confirm('The page save failed.');
      },
      success: function (response) {
        $('#newsletter_email').val("");
        $('#newsletter_email').blur();
        $('#ajax_subscribe_msg').html(response);
        $('#ajax_subscribe_msg').slideDown('slow');
        setTimeout(function () { $("#ajax_subscribe_msg").slideUp(); }, 5000);
      }
    });

    event.preventDefault();
  });

  //FORM VALIDATION JAVASCRIPT----------------------------------------------------
  $('form#contact-form').submit(function(event) {
      var hasError = false;
      $('.requiredField').each(function() {
          var parent = $(this).parent();
          if(jQuery.trim($(this).val()) == '') {
              //console.log("empty");
              if($(this).hasClass('email')) {
                if(!parent.find('.invalid_email').hasClass('hidden')) {
                  parent.find('.invalid_email').addClass('hidden');
                }
              }
              parent.find('.error').removeClass('hidden');
              hasError = true;
          } else if($(this).hasClass('email')) {
              var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
              if(!emailReg.test(jQuery.trim($(this).val()))) {
                //console.log("invalid_email");
                parent.find('.error').addClass('hidden');
                parent.find('.invalid_email').removeClass('hidden');
                hasError = true;
              }
              else {
                parent.find('.error').addClass('hidden');
                parent.find('.invalid_email').addClass('hidden');
              }
          }
          else {
            parent.find('.error').addClass('hidden');
          }
      });
      if(!hasError) {
          $('form#contact-form input.submit').fadeOut('normal', function() {
              $(this).parent().append('');
          });
          var formInput = $(this).serialize();
          $.post($(this).attr('action'),formInput, function(data){
              $('form#contact-form').slideUp("fast", function() {
                $(".contact-form-container p.success").removeClass("hidden");
              });
          });
      }

      event.preventDefault();

    });

  //FORM VALIDATION JAVASCRIPT----------------------------------------------------
  $('form#course-inquiery-form').submit(function(event) {
      var hasError = false;
      $('form#course-inquiery-form .requiredField').each(function() {
          var parent = $(this).parent();
          if(jQuery.trim($(this).val()) == '') {
              //console.log("empty");
              if($(this).hasClass('email')) {
                if(!parent.find('.invalid_email').hasClass('hidden')) {
                  parent.find('.invalid_email').addClass('hidden');
                }
              }
              parent.find('.error').removeClass('hidden');
              hasError = true;
          } else if($(this).hasClass('email')) {
              var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
              if(!emailReg.test(jQuery.trim($(this).val()))) {
                //console.log("invalid_email");
                parent.find('.error').addClass('hidden');
                parent.find('.invalid_email').removeClass('hidden');
                hasError = true;
              }
              else {
                parent.find('.error').addClass('hidden');
                parent.find('.invalid_email').addClass('hidden');
              }
          }
          else {
            parent.find('.error').addClass('hidden');
          }
      });
      if(!hasError) {
          $('form#course-inquiery-form input.submit').fadeOut('normal', function() {
              $(this).parent().append('');
          });
          var formInput = $(this).serialize();
          $.post($(this).attr('action'),formInput, function(data){
              $('form#course-inquiery-form').slideUp("fast", function() {
                $("#cd_make_inquiery p.success").removeClass("hidden");
              });
          });
      }

      event.preventDefault();

    });

  //FORM VALIDATION JAVASCRIPT----------------------------------------------------
  $('form#course-sign-up-form').submit(function(event) {
      var hasError = false;
      $('form#course-sign-up-form .requiredField').each(function() {
          var parent = $(this).parent();
          if(jQuery.trim($(this).val()) == '') {
              //console.log("empty");
              if($(this).hasClass('email')) {
                if(!parent.find('.invalid_email').hasClass('hidden')) {
                  parent.find('.invalid_email').addClass('hidden');
                }
              }
              parent.find('.error').removeClass('hidden');
              hasError = true;
          } else if($(this).hasClass('email')) {
              var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
              if(!emailReg.test(jQuery.trim($(this).val()))) {
                //console.log("invalid_email");
                parent.find('.error').addClass('hidden');
                parent.find('.invalid_email').removeClass('hidden');
                hasError = true;
              }
              else {
                parent.find('.error').addClass('hidden');
                parent.find('.invalid_email').addClass('hidden');
              }
          }
          else {
            parent.find('.error').addClass('hidden');
          }
      });
      if(!hasError) {
          $('form#course-sign-up-form input.submit').fadeOut('normal', function() {
              $(this).parent().append('');
          });
          var formInput = $(this).serialize();
          $.post($(this).attr('action'),formInput, function(data){
              $('form#course-sign-up-form').slideUp("fast", function() {
                $("#cd_sign_up p.success").removeClass("hidden");
              });
          });
      }

      event.preventDefault();

    });
  
    //BLACK & WHITE HOVER EFFECT--------------------------------------
    $('.bw-wrapper').BlackAndWhite({
        hoverEffect : true, // default true
        speed: { //this property could also be just speed: value for both fadeIn and fadeOut
            fadeIn: 200, // 200ms for fadeIn animations
            fadeOut: 300 // 800ms for fadeOut animations
        }
    });
});

//FLICKR FEED 
//$(document).ready(function() {		
//	$('#flickrfeed').jflickrfeed({
//		limit: 6,
//		qstrings: {
//			id: '91212552@N07'
//		},
//		itemTemplate:
//		'<li>' +
//			'<a class="lightbox" rel="colorbox" href="{{image}}" title="{{title}}">' +
//				'<img src="{{image_s}}" alt="{{title}}" />' +
//			'</a>' +
//		'</li>'
//	}, function(data) {
//			$(".lightbox").fancybox({
//			'overlayShow'	: false,
//			'transitionIn'	: 'elastic',
//			'transitionOut'	: 'elastic'
//		});
//	});
//});	

//TWITTER FEED----------------------------------------------------
//$(document).ready(function() {
//    $(".tweet").tweet({
//		 // join_text: false,
//		  username: "abcgomel", // Change username here
//		  modpath: './js/twitter/', // Twitter files path
//		  avatar_size: false, // you can active avatar
//		  count: 2, // number of tweets
//		  loading_text: "loading tweets..."
//    });
//});

//CAROUSEL--------------------------------------------------------
(function($){
	$(document).ready(function(){

// Add classes for other carousels
var $carousel = $('.latest-work-jc, .latest-posts-jc, .testimonials-jc');

var scrollCount;

function adjustScrollCount() {
	if( $(window).width() < 768 ) {
		scrollCount = 1;
	} else {
		scrollCount = 1;
	}

}

function adjustCarouselHeight() {

	$carousel.each(function() {
		var $this    = $(this);
		var maxHeight = -1;
		$this.find('li').each(function() {
			maxHeight = maxHeight > $(this).height() ? maxHeight : $(this).height();
		});
		$this.height(parseInt(maxHeight)+10);
	});
}
function initCarousel() {
	adjustCarouselHeight();
	adjustScrollCount();
	var i = 0;
	var g = {};
	$carousel.each(function() {
		i++;

		var $this = $(this);
		g[i] = $this.jcarousel({
			animation           : 500,
			scroll              : scrollCount,
			wrap: 'circular'
		});
		$this.jcarousel('scroll', 0);
		 $this.prev().find('.jcarousel-prev').bind('active.jcarouselcontrol', function() {
			$(this).addClass('active');
		}).bind('inactive.jcarouselcontrol', function() {
			$(this).removeClass('active');
		}).jcarouselControl({
			target: '-='+scrollCount,
			carousel: g[i]
		});

		$this.prev().find('.jcarousel-next').bind('active.jcarouselcontrol', function() {
			$(this).addClass('active');
		}).bind('inactive.jcarouselcontrol', function() {
			$(this).removeClass('active');
		}).jcarouselControl({
			target: '+='+scrollCount,
			carousel: g[i]
		});

		$this.touchwipe({
		wipeLeft: function() {
			$this.jcarousel('scroll','+='+scrollCount);
		},
		wipeRight: function() {
			$this.jcarousel('scroll','-='+scrollCount);
		}
	});

	});
}
$(window).load(function(){
	initCarousel();
});

$(window).resize(function () {
	$carousel.each(function() {
		var $this = $(this);
		$this.jcarousel('destroy');
	});
	initCarousel();
});


});

})(this.jQuery);

/**
 * jQuery Plugin to obtain touch gestures from iPhone, iPod Touch and iPad, should also work with Android mobile phones (not tested yet!)
 * Common usage: wipe images (left and right to show the previous or next image)
 *
 * @author Andreas Waltl, netCU Internetagentur (http://www.netcu.de)
 * @version 1.1.1 (9th December 2010) - fix bug (older IE's had problems)
 * @version 1.1 (1st September 2010) - support wipe up and wipe down
 * @version 1.0 (15th July 2010)
 */
(function($){$.fn.touchwipe=function(settings){var config={min_move_x:20,min_move_y:20,wipeLeft:function(){},wipeRight:function(){},wipeUp:function(){},wipeDown:function(){},preventDefaultEvents:true};if(settings)$.extend(config,settings);this.each(function(){var startX;var startY;var isMoving=false;function cancelTouch(){this.removeEventListener('touchmove',onTouchMove);startX=null;isMoving=false}function onTouchMove(e){if(config.preventDefaultEvents){e.preventDefault()}if(isMoving){var x=e.touches[0].pageX;var y=e.touches[0].pageY;var dx=startX-x;var dy=startY-y;if(Math.abs(dx)>=config.min_move_x){cancelTouch();if(dx>0){config.wipeLeft()}else{config.wipeRight()}}else if(Math.abs(dy)>=config.min_move_y){cancelTouch();if(dy>0){config.wipeDown()}else{config.wipeUp()}}}}function onTouchStart(e){if(e.touches.length==1){startX=e.touches[0].pageX;startY=e.touches[0].pageY;isMoving=true;this.addEventListener('touchmove',onTouchMove,false)}}if('ontouchstart'in document.documentElement){this.addEventListener('touchstart',onTouchStart,false)}});return this}})(jQuery);