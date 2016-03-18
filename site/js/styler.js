/* Template Styles
-----------------------------------------------------------------------------------*/

window.console = window.console || (function(){
	var c = {}; c.log = c.warn = c.debug = c.info = c.error = c.time = c.dir = c.profile = c.clear = c.exception = c.trace = c.assert = function(){};
	return c;
})();


jQuery(document).ready(function($) {
	
		// Color Changer
		$(".yellow" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/yellow.css" );
			return false;
		});
		
		$(".green" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/green.css" );
			return false;
		});
		
		$(".light-blue" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/light-blue.css" );
			return false;
		});
		
		$(".aqua" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/aqua.css" );
			return false;
		});		
		
		$(".navy" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/navy.css" );
			return false;
		});

		$(".pear" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/pear.css" );
			return false;
		});		
		
		$(".peach" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/peach.css" );
			return false;
		});		
		
		$(".brown" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/brown.css" );
			return false;
		});		

		$(".orange" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/orange.css" );
			return false;
		});

		$(".dark" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/dark.css" );
			return false;
		});
		
		$(".blue" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/blue.css" );
			return false;
		});

		$(".grey" ).click(function(){
			$("#template-color" ).attr("href", "css/colors/grey.css" );
			return false;
		});


		$("#template-styles h2 a").click(function(e){
			e.preventDefault();
			var div = $("#template-styles");
			console.log(div.css("left"));
			if (div.css("left") === "-135px") {
				$("#template-styles").animate({
					left: "0px"
				}); 
			} else {
				$("#template-styles").animate({
					left: "-135px"
				});
			}
		})
		

		$(".colors li a").click(function(e){
			e.preventDefault();
			$(this).parent().parent().find("a").removeClass("active");
			$(this).addClass("active");
		})
		
		$(".bg li a").click(function(e){
			e.preventDefault();
			$(this).parent().parent().find("a").removeClass("active");
			$(this).addClass("active");
			var bg = $(this).css("backgroundImage");
			$("body").css("backgroundImage",bg)
		})

			

	});