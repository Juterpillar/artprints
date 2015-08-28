// Make sure html has loaded before actioning javascript as link in head of html doc
// $('#container').hide();
$(window).load(function() {	
	updateContainer();


// ------------- code for FANCY LIGHT BOX -------------------

if(document.getElementById('product') || document.getElementById('canvas_info')) {
	$(document).ready(function() {
	    $('.fancybox').fancybox({
	        padding : 0,
	        openEffect  : 'elastic'
	    });
	});
}


// ------------- code for FLEXSLIDER -------------------

// This codes adds the appropriate classes to the image gallery so the css (particularly display: none)
// only works for those who have javascript enabled.

 	if(document.getElementById('flexslider')) {
			var flexslider = document.getElementById('flexslider');
			var slides = document.getElementById('slides');
			slides.setAttribute("class", 'slides'); //For Most Browsers
			slides.setAttribute("className", 'slides'); //For IE

			flexslider.setAttribute("class", 'flexslider'); //For Most Browsers
			flexslider.setAttribute("className", 'flexslider'); //For IE
			$('.flexslider').flexslider({ 
				animation: "slide",
				easing: "easeOutQuint",
				reverse: false
			});

			$('.more_banners').each(function() {
				$(this).css("display", 'block');
			});
	}

// ------------ expand aside to full height ------------------


 $(window).resize(function() {
     updateContainer();
 });

function updateContainer() {
	if ($(window).width() > 568) {
		var contentHeight = ($('#content').outerHeight());
		var asideHeight = ($('aside').height());
		var newHeight = contentHeight - 1;
		$('aside').css('min-height', newHeight);
	}
}


/* ---------------------------   map   ------------------------------ */	

if (document.getElementById('contact')) {
	var mapLocation = new google.maps.LatLng(-41.29748, 174.78147);
	
	// change the colour
	var grayStyles = [
        {
          featureType: "all",
          stylers: [
            { saturation: -100 },
            { lightness: 35 }
          ]
        },
      ];

	// Map options
	var mapOptions = {
		center: mapLocation,
		styles: grayStyles,
		zoom: 16,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	// Get the canvas (where the map will be placed)
	var theCanvas = document.getElementById('map_options');

	// Draw the map to the canvas in the div 'map'
	var map = new google.maps.Map(theCanvas, mapOptions);
	
	// Adds the marker onto the map with a title
	var logo = 'images/marker.png';	
	var marker = new google.maps.Marker({      
	    position: new google.maps.LatLng(-41.29748, 174.78147),     
	    map: map,
	    icon: logo,
	    title: "artprints"
	  });
}


// ---------------------- filters ---------------------------

	if($('aside') && $('#pricing_heading')) {
		$('#pricing_heading').html('click here to show size/format options <i class="icon-circle-arrow-right"></i>');
		$('#filter_heading').html('click here to show filter options <i class="icon-circle-arrow-right"></i>');
		$('#artist_options').hide();
		$('#size_options').hide();
		$('#price_options').hide();
		$('#orientation_options').hide();
		if ($(window).width() > 568) {
			$('#subject').html('<i class="icon-circle-arrow-down"></i>' + ' subject');
			$('#artist').html('<i class="icon-circle-arrow-left"></i>' + ' artist');
			$('#size').html('<i class="icon-circle-arrow-left"></i>' + ' size');
			$('#price').html('<i class="icon-circle-arrow-left"></i>' + ' price');
			$('#orientation').html('<i class="icon-circle-arrow-left"></i>' + ' orientation');
		}
		else {
			$('#phone_options').hide();
			$('#email_options').hide();
			$('#address_options').hide();
			$('#map_options').delay(5000).slideUp('slow');
			$('#phone').html('phone <i class="icon-circle-arrow-right"></i><i class="icon-phone"></i>');
			$('#email').html('email <i class="icon-circle-arrow-right"></i><i class="icon-envelope-alt"></i>');
			$('#address').html('address <i class="icon-circle-arrow-right"></i><i class="icon-building"></i>');
			$('#map').html('map <i class="icon-circle-arrow-right"></i><i class="icon-screenshot"></i>');
			$('#options').hide();
			$('#filters').hide();
			$('#subject_options').hide();
			$('#subject').html('subject' + '<i class="icon-circle-arrow-right"></i>');
			$('#artist').html('artist' + '<i class="icon-circle-arrow-right"></i>');
			$('#size').html('size' + '<i class="icon-circle-arrow-right"></i>');
			$('#price').html('price' + '<i class="icon-circle-arrow-right"></i>');
			$('#orientation').html('orientation' + '<i class="icon-circle-arrow-right"></i>');
		}
	}
 

$('#pricing_heading').click(function() {
	if($('#pricing_heading').html() == 'click here to show size/format options <i class="icon-circle-arrow-right"></i>') {
		$('#pricing_heading').html('click here to hide size/format options <i class="icon-circle-arrow-down"></i>');
	}
	else {
		($('#pricing_heading').html('click here to show size/format options <i class="icon-circle-arrow-right"></i>'));
	}
	$('#options').slideToggle('slow', function() {
	});
});

$('#filter_heading').click(function() {
	if($('#filter_heading').html() == 'click here to show filter options <i class="icon-circle-arrow-right"></i>') {
		$('#filter_heading').html('click here to hide filter options <i class="icon-circle-arrow-down"></i>');
	}
	else {
		($('#filter_heading').html('click here to show filter options <i class="icon-circle-arrow-right"></i>'));
	}
	$('#filters').slideToggle('slow', function() {
	});
});

if(document.getElementById('contact')){
	if  ($(window).width() < 568) {
		document.getElementById('phone').onclick = toggleContacts;
		document.getElementById('email').onclick = toggleContacts;
		document.getElementById('address').onclick = toggleContacts;
		document.getElementById('map').onclick = toggleContacts;
	}
}

if(document.getElementById('checkout')){
	$('#review').html('click here to review your order <i class="icon-circle-arrow-down"></i>');
	$('#review_options').hide();
	document.getElementById('review').onclick = toggleReview;
}

function toggleReview() {
	var headingHtml = $(this).html();
	var side = 'click here to review your order <i class="icon-circle-arrow-right"></i>';
	var down = 'click here to hide your order <i class="icon-circle-arrow-down"></i>';
	$('#review_options').slideToggle('slow', function() {});
	if(headingHtml == 'click here to hide your order <i class="icon-circle-arrow-down"></i>') {
		$('#review').html(side);
	}
	else {
		$('#review').html(down);
	}
}

function toggleContacts() {
	var headingId = this.id;
	var headingHtml = $(this).html();
	var relatedDiv = headingId + '_options';
	if (headingId == 'phone') {
		var side = 'phone <i class="icon-circle-arrow-right"></i><i class="icon-phone"></i>';
		var down = 'phone <i class="icon-circle-arrow-down"></i><i class="icon-phone"></i>';
	}
	else if (headingId == 'email') {
		var side = 'email <i class="icon-circle-arrow-right"></i><i class="icon-envelope-alt"></i>';
		var down = 'email <i class="icon-circle-arrow-down"></i><i class="icon-envelope-alt"></i>';
	}
	else if (headingId == 'address') {
		var side = 'address <i class="icon-circle-arrow-right"></i><i class="icon-building"></i>';
		var down = 'address <i class="icon-circle-arrow-down"></i><i class="icon-building"></i>';
	}
	else if (headingId == 'map') {
		var side = 'map <i class="icon-circle-arrow-right"></i><i class="icon-screenshot"></i>';
		var down = 'map <i class="icon-circle-arrow-down"></i><i class="icon-screenshot"></i>';
	}
	$('#'+relatedDiv).slideToggle('slow', function() {});
	if(headingHtml == side) {
		$('#'+headingId).html(down);
	}
	else {
		$('#'+headingId).html(side);
	}
}

if(document.getElementById('filter_heading') && document.getElementById('subject')) {
	document.getElementById('subject').onclick = toggleOptions;
	document.getElementById('artist').onclick = toggleOptions;
	document.getElementById('size').onclick = toggleOptions;
	document.getElementById('price').onclick = toggleOptions;
	document.getElementById('orientation').onclick = toggleOptions;
}

function toggleOptions() {
	var headingId = this.id;
	var headingHtml = $(this).html();
	var relatedDiv = headingId + '_options';
	if (headingId == 'review') {
		headingId = 'order review';
		var side = 'headingId' + ' <i class="icon-circle-arrow-right"></i>';
		var down = 'headingId' + ' <i class="icon-circle-arrow-down"></i>';
	}
	else if ($(window).width() > 568) {
		var side = '<i class="icon-circle-arrow-left"></i> ' + headingId;
		var down = '<i class="icon-circle-arrow-down"></i> ' + headingId;
	}  
	else {
		var side = headingId + ' <i class="icon-circle-arrow-right"></i>';
		var down = headingId + ' <i class="icon-circle-arrow-down"></i>';
	}
	console.log(headingId);
	console.log(headingHtml);
	$('#'+relatedDiv).slideToggle('slow', function() {});
	if(headingHtml == (headingId + ' <i class="icon-circle-arrow-down"></i>') || headingHtml == ('<i class="icon-circle-arrow-down"></i> ' + headingId)) {
		$('#'+headingId).html(side);
	}
	else {
		$('#'+headingId).html(down);
	}
}

// ------------------------ faq -----------------------------

if(document.getElementById('faq')) {
	$('#general').html('general <i class="icon-circle-arrow-right"></i>');
	$('#artists').html('artists <i class="icon-circle-arrow-right"></i>');
	$('#prints').html('prints <i class="icon-circle-arrow-right"></i>');
	$('#framed').html('framed <i class="icon-circle-arrow-right"></i>');
	$('#canvas').html('canvas <i class="icon-circle-arrow-right"></i>');
	$('#payment').html('payment <i class="icon-circle-arrow-right"></i>');
//	$('#canvas_q').hide();
//	$('#framed_q').hide();
//	$('#artists_q').hide();
//	$('#general_q').hide();
//	$('#prints_q').hide();
//	$('#payment_q').hide();
	$('#canvas_q').slideUp('slow');
	$('#framed_q').slideUp('slow');
	$('#artists_q').slideUp('slow');
	$('#general_q').slideUp('slow');
	$('#prints_q').slideUp('slow');
	$('#payment_q').slideUp('slow');
	document.getElementById('general').onclick = toggleFaq;
	document.getElementById('artists').onclick = toggleFaq;
	document.getElementById('prints').onclick = toggleFaq;
	document.getElementById('canvas').onclick = toggleFaq;
	document.getElementById('framed').onclick = toggleFaq;
	document.getElementById('payment').onclick = toggleFaq;
}

function toggleFaq() {
	var headingId = this.id;
	var headingHtml = $(this).html();
	var relatedDiv = headingId + '_q';
	var side = headingId + ' <i class="icon-circle-arrow-right"></i>';
	var down = headingId + ' <i class="icon-circle-arrow-down"></i>';
	$('#'+relatedDiv).slideToggle('slow', function() {});
	if(headingHtml == (headingId + ' <i class="icon-circle-arrow-down"></i>') || headingHtml == ('<i class="icon-circle-arrow-down"></i> ' + headingId)) {
		$('#'+headingId).html(side);
	}
	else {
		$('#'+headingId).html(down);
	}
}

// ---------------------- product ---------------------------

	// this function alerts a customer with a pop up box before they are redirected to afas if not signed in
	if (document.getElementById('product')) {
		if (document.getElementById('redirect')) {
			document.getElementById('redirect').onclick = warning;
		}
	}	
	
	function warning() {
		console.log('here');
		var result = window.confirm('You are about to be redirected to Art For Art\'s Sake \(www.afas.co.nz\). Only logged in wholesalers can purchase from artprints.kiwi.co.nz.');
		if(result == false) {
			return false;
		}
	}

// ---------------------- register ---------------------------	

// this targets elements from the registration page	
	if(document.getElementById('register')) {
		// Get the elements
		var sameBox = document.getElementById('same');
		var billAddress = document.getElementById('bAddress')
		var billSuburb = document.getElementById('bSuburb')
		var billCity = document.getElementById('bCity')
		var billCode = document.getElementById('bCode')
		var delAddress = document.getElementById('dAddress');
		var delSuburb = document.getElementById('dSuburb');
		var delCity = document.getElementById('dCity');
		var delCode = document.getElementById('dCode');
		var agreeBox = document.getElementById('agree');

		// Events
		sameBox.onchange = billSameDel;

	}

	function billSameDel() {
		if (delAddress.value == '') {
			var addy = billAddress.value;
			var burb = billSuburb.value;
			var city = billCity.value;
			var code = billCode.value;
			delAddress.value = addy; 
			delSuburb.value = burb;
			delCity.value = city;
			delCode.value = code;
		}
		else {
			delAddress.value = ''; 
			delSuburb.value = '';
			delCity.value = '';
			delCode.value = '';
		}
	}

// --------------------------- contact --------------------------------

	// this function slides down a confirmation message on mobile when an email/testimonial has been sent
	$('.success')
		.hide()
		.slideDown('slow');
//		.delay(2000)
//		.slideUp('slow');


// ---------------------- account ---------------------------
/*
	// this function slides down a the update account details form on click
	if (document.getElementById('account')) {
		if(document.getElementById('account_links')) {
			$('#account_form').hide();
			var side = 'update your account details <i class="icon-circle-arrow-right"></i>';
			var down = 'update your account details <i class="icon-circle-arrow-down"></i>';
			$('#update_account')
			.html(side)
			.css('cursor', 'pointer')
			.click(function() {
				var html = $('#update_account').html();
				$('#account_form').slideToggle('slow');
				if (html == side) {
					$('#update_account').html(down);
				}
				else {
					$('#update_account').html(side);	
				}
			});
		}
	}
*/
	

// ---------------------- search ---------------------------

	// this function clears the value of the search box on focus if it is currently the default value
	// then reenters the value on blur if nothing was entered

	var origValue = $('#search').val();
	$('#search').focus(function() {
		if ($('#search').val() == origValue) {
   			$('#search').val('');
   		}
	});

	$('#search').blur(function() {
		var input = $('#search').val();
		if (input == '') {
 			$('#search').val(origValue);
 		}
	});


});