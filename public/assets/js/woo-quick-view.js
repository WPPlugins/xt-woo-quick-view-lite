(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	*/
	
	$(function() {
		
		//final width --> this is the quick view image slider width
		//maxQuickWidth --> this is the max-width of the quick-view panel
		var layouts = {
			'S': 400,  
			'M': 768,  
			'L': 1124,   
			'XL':1170 
		},
		customizer = (typeof(wp.customize) !== 'undefined'),
		quickView = $('.woo-quick-view'),
		isTouchDevice = $('html').hasClass('.wooqv-touchevents'),
		addTimeoutId,
		currentSlider,
		closeModalOnAdded = 0,
		mobileSliderWidth = 350,
		mobileSliderHeight = 350,
		desktopSliderWidth = 400,
		desktopSliderHeight = 400,
		defaultMaxQuickWidth = 900,
		defaultMaxQuickHeight = 755,
		defaultSliderWidth,
		defaultSliderHeight,
		sliderAnimation,
		sliderAutoPlay = 0,
		sliderGallery = 1,
		sliderGalleryThumbs = 6,
		sliderArrowsEnabled = 0,
		sliderArrow = '',
		lightBoxEnabled = false,
		
		boxShadowBlur = '',
		boxShadowSpread = '',
		boxShadowColor = '',
		
		sliderFinalWidth = defaultSliderWidth,
		sliderFinalHeight = defaultSliderHeight,
		maxQuickWidth = defaultMaxQuickWidth,
		maxQuickHeight = defaultMaxQuickHeight,
		closeOnOverlayClick = true,
		widthOverflowSet = null,
		heightOverflowSet = null,
		isVisible = false,
		recentProduct  = null,
		mobileScreen = false,
		winWidth,
		winHeight;

		function initVars() {
		
			closeModalOnAdded = getOption('wooqv-close-on-added', 0, true);
			mobileSliderWidth = getOption('wooqv-mobile-slider-width', 350, true);
			mobileSliderHeight = getOption('wooqv-mobile-slider-height', 350, true);
			desktopSliderWidth = getOption('wooqv-desktop-slider-width', 400, true);
			desktopSliderHeight = getOption('wooqv-desktop-slider-height', 400, true);
			
			boxShadowBlur = getOption('wooqv-box-shadow-blur', 30);
			boxShadowSpread = getOption('wooqv-box-shadow-spread', 0);
			boxShadowColor = getOption('wooqv-box-shadow-color', 'rgba(0,0,0,0.3)');
			
			quickView.css({'box-shadow': '0 0 '+boxShadowBlur+'px '+boxShadowSpread+'px '+boxShadowColor+''});
			
		}
		
		function updateResponsiveVars() {
			
			winWidth = $(window).width(),
			winHeight = $(window).height(),
			mobileScreen = winWidth <= layouts.L,
			defaultSliderWidth = mobileScreen ? parseInt(mobileSliderWidth) : parseInt(desktopSliderWidth);
			defaultSliderHeight = mobileScreen ? parseInt(mobileSliderHeight) : parseInt(desktopSliderHeight);		
		}
		
		function initEvents() {
		
			if(customizer) {
				
				var handler;
				var bodyClickEvents = $( document.body ).data('events').click;
				
				for(var i = 0 ; i < bodyClickEvents.length ; i++) {
				
					if(bodyClickEvents[i].namespace === 'preview') {
						handler = bodyClickEvents[i].handler;
						break;
					}	
				}					
				
				if(handler) {
					$( document.body ).off( 'click.preview', 'a');
					$( document.body ).on( 'click.preview', 'a', function(e) {
	
						if(!$(e.target).hasClass('wooqv-trigger') && !$(e.target).hasClass('wooqv-trigger-icon')) {
							handler(e);
						}
	
					});
				}
				
				quickView.attrchange({
				    trackValues: true, /* Default to false, if set to true the event object is 
				                updated with old and new value.*/
				    callback: function (e) { 
				        //event               - event object
				        //event.attributeName - Name of the attribute modified
				        //event.oldValue      - Previous value of the modified attribute
				        //event.newValue      - New value of the modified attribute
				        //Triggered when the selected elements attribute is added/updated/removed
				  
				        if(e.attributeName.search('wooqv-') !== -1) {
					        
				        	initVars();
	
					        setTimeout(function() {

						        resizeQuickView();
						        resizeQuickView();
						        
						    },1); 
							
				        }
				    }        
				});
			}

			//open / close the quick view panel
			$('body').on('click', function(evt){
				
				if( $(evt.target).is('.wooqv-trigger') || $(evt.target).is('.wooqv-trigger-icon')) {
					
					evt.preventDefault();
					
					var selectedImage = $(evt.target).closest('.product').find('img.attachment-shop_catalog');
					if(selectedImage.length === 0) {
						
						selectedImage = $(evt.target).closest('.product').find('.woocommerce-LoopProduct-link > img');
						
						if(selectedImage.length === 0) {
							selectedImage = $(evt.target).closest('.product').find('.woocommerce-LoopProduct-link img').first();
						}
					}
					
					if(selectedImage.length === 0) {
						return false;	
					}
					
					var selectedImageUrl = selectedImage.attr('src');
		
					$('html').addClass('wooqv-active');
					animateQuickView(selectedImage, sliderFinalWidth, maxQuickWidth, 'open');

					
				}else if( 
					$(evt.target).is('.wooqv-close-icon') || 
					$(evt.target).is('html.wooqv-active') ||
					($(evt.target).is('.wooqv-overlay') && closeOnOverlayClick)
				) {
					closeQuickView( sliderFinalWidth, maxQuickWidth);
				}
			});
			
			$(document).keyup(function(evt){
				//check if user has pressed 'Esc'
		    	if(evt.which==='27'){
					closeQuickView( sliderFinalWidth, maxQuickWidth);
				}
			});

			//center quick-view on window resize
			$(window).on('resize', function(){

				window.requestAnimationFrame(resizeQuickView);
				window.requestAnimationFrame(resizeQuickView);
				
			});

			if(closeModalOnAdded) { 
				
				var closeModal = function() {
					
					if(isVisible) {
						closeQuickView( sliderFinalWidth, maxQuickWidth);
					}
				};
			
				$( document.body ).on( 'woofc_added_to_cart', closeModal);
				$( document.body ).on( 'wooqv_added_to_cart', closeModal);
			}
		}	
		
		
		function addToCart(trigger) {
			
			if(addTimeoutId){
				clearInterval(addTimeoutId);
			}
			
			if(trigger.data('loading')) {
				return false;
			}
			
			trigger.removeClass('added');
			
			var form = trigger.closest('form');
			var args = form.serializeJSON();
			args['add-to-cart'] = form.find('[name="add-to-cart"]').val();
			
			trigger.data('loading', true);
			trigger.addClass('loading');
			
			//update cart product list
			request(args, function() {
				
				trigger.removeClass('loading').addClass('added');
				trigger.removeData('loading');
				
				addTimeoutId = setTimeout(function() {
					trigger.removeClass('added');
				}, 3000);
				
				setTimeout(function() {
					
					$( document.body ).trigger( 'wc_fragment_refresh' );
					$( document.body ).trigger( 'wooqv_added_to_cart' );
					
				},200);	
			});

		}
		
		
		function request(args, callback) {
			
			$('html').addClass('wooqv-loading');
			
			var type = 'single-add';

			var params = {
				action: 'wooqv_update_cart',
				type: type
			};

			params = $.extend(params, args);
			
			$.ajax({
				url: location.href,
				data: params,
				type: 'post', 
				success: function() {
					
					$('html').removeClass('wooqv-loading');
					
					if(typeof(callback) !== 'undefined') {
						callback();
					}
				}
			});	
		}	
		
		function validateAddToCart(btn) {
			
			var form = btn.closest('form');
			var errors = 0;
			form.find('.required-product-addon').each(function() {
		    	var addon = $(this);
				var input = $(this).find('input');
		    	if(input.length > 1) {
			    	addon.removeClass('wooqv-error');
		    		if(!input.is(':checked')) {
						errors++;
						addon.addClass('wooqv-error');
					}
		    	}else{
			    	addon.removeClass('wooqv-error');
					if(input.val() === '') {
						errors++;
						addon.addClass('wooqv-error');
		        	}
		    	}
			});
			
			if(errors > 0) {
				var firstError = form.find('.required-product-addon.wooqv-error').first();
				$('html,body').animate({scrollTop: firstError.offset().top - 100}, 500);
			}
			
		    return (errors === 0);
		}

		function getOption(key, defaultVal, isInt) {
			
			var val;
			isInt = isInt ? isInt : false;
			
			if(quickView.attr(key)) {
				
				val = quickView.attr(key);
				
			}else{
			
				val = defaultVal;
			}	
			
			if(isInt) {
				val = parseInt(val);
			}
			
			return val;
		}

		function customizerValuesChanged() {

        	if(!mobileScreen) {
        		
        		$('.woo-quick-view').css('width', '' );
        		
        		$('.wooqv-slider-wrapper, .wooqv-slider li img').css('width', desktopSliderWidth+'px' );
        		$('.wooqv-slider-wrapper, .wooqv-item-info').css('height', desktopSliderHeight+'px');
        	
        	}else{
	        	
	        	$('.wooqv-slider li img').css('width', '' );
				$('.wooqv-item-info').css('height', '' );
				
	        	$('.woo-quick-view, .wooqv-slider-wrapper').css('width', mobileSliderWidth+'px' );
	        	$('.wooqv-slider-wrapper').css('height', mobileSliderHeight+'px' );
	        	
        	}

		}

		function resizeQuickView() {
			
			updateResponsiveVars();
			
			if(customizer) {
				customizerValuesChanged();
			}	
			
			//SET VARS FOR MOBILE
			
			if(winWidth <= defaultSliderWidth) {
				
				sliderFinalWidth = winWidth;
				maxQuickWidth = sliderFinalWidth;
								
			}else if(winWidth > defaultSliderWidth) {
					
				sliderFinalWidth = defaultSliderWidth;
				maxQuickWidth = defaultMaxQuickWidth;			
			}
			
			if(winHeight <= defaultSliderHeight) {
				
				sliderFinalHeight = winHeight;
				maxQuickHeight = sliderFinalHeight;
								
			}else if(winHeight > defaultMaxQuickHeight) {
					
				sliderFinalHeight = defaultSliderHeight;
				maxQuickHeight = defaultMaxQuickHeight;		
			}
			
			
			// SET OVERFLOW
			
			if(isVisible && (winWidth <= mobileSliderWidth) && (widthOverflowSet === false || widthOverflowSet === null)) {
				
				enableWidthOverflow();
				
			}else if((winWidth > mobileSliderWidth) && (widthOverflowSet === true || widthOverflowSet === null)){

				disableWidthOverflow();
			}	
			
			if(isVisible && (winHeight <= quickView.height()) && (heightOverflowSet === false || heightOverflowSet === null)) {
								
				enableHeightOverflow();
				
			}else if((winHeight > quickView.height()) && (heightOverflowSet === true || heightOverflowSet === null)){
				
				disableHeightOverflow();
			}
			
			var quickViewLeft = (winWidth - quickView.width())/2,
				quickViewTop = (winHeight - quickView.height())/2,
				quickViewWidth = ( winWidth * 0.8 < maxQuickWidth ) ? winWidth * 0.8 : maxQuickWidth,
				quickViewHeight = '',
				quickViewInfoWidth = parseInt(defaultMaxQuickWidth - desktopSliderWidth);

        
			if(mobileScreen) {		
	
				quickViewLeft =  (winWidth - quickView.width())/2;
				quickViewTop = (winHeight - quickView.height())/2;		
				quickViewWidth = mobileSliderWidth;
				quickViewHeight = ( winHeight * 0.8 < maxQuickHeight ) ? winHeight * 0.8 : maxQuickHeight;
				quickViewInfoWidth = '100%';
			}
		
			quickView.css({
			    "top": quickViewTop,
			    "left": quickViewLeft,
			    "width": quickViewWidth
			});
			
			quickView.find('.wooqv-item-info').css('width', quickViewInfoWidth);
			
		} 
		
		function enableWidthOverflow() {
			$('html').addClass('wooqv-width-overflow');
			widthOverflowSet = true;
		}
		
		function disableWidthOverflow() {
			$('html').removeClass('wooqv-width-overflow');
			widthOverflowSet = false;
		}
		
		function enableHeightOverflow() {
			$('html').addClass('wooqv-height-overflow');
			heightOverflowSet = true;
		}
		
		function disableHeightOverflow() {
			
			$('html').removeClass('wooqv-height-overflow');
			heightOverflowSet = false;
		}
	
		function closeQuickView(finalWidth, maxQuickWidth) {
			var close = $('.wooqv-close-icon'),
				activeSliderUrl = close.siblings('.wooqv-slider-wrapper').find('.selected img').attr('src'),
				selectedImage = $('.empty-box').find('img');
			//update the image in the gallery
			if( !quickView.hasClass('velocity-animating') && quickView.hasClass('wooqv-add-content')) {
				selectedImage.attr('src', activeSliderUrl);
				animateQuickView(selectedImage, finalWidth, maxQuickWidth, 'close');
			} else {
				closeNoAnimation(selectedImage, finalWidth, maxQuickWidth);
			}
		}
	
		function animateQuickView(image, finalWidth, maxQuickWidth, animationType) {
			
			updateResponsiveVars();

			//store some image data (width, top position, ...)
			//store window data to calculate quick view panel position
			image = image.length ? image : $('.empty-box');

			var parentListItem = image.closest('.product'),
			    productId = parentListItem.find('.wooqv-trigger').data('id'),
				topSelected = image.offset().top - $(window).scrollTop(),
				leftSelected = image.offset().left,
				widthSelected = image.width(),
				heightSelected = image.height(),
				
				finalHeight = finalWidth * heightSelected/widthSelected,
				finalLeft = (winWidth - finalWidth)/2,
				finalTop = (winHeight - finalHeight)/2,
				quickViewWidth = ( winWidth * 0.8 < maxQuickWidth ) ? winWidth * 0.8 : maxQuickWidth,
				quickViewHeight = finalHeight,
				quickViewLeft = (winWidth - quickViewWidth)/2,
				quickViewTop = finalTop,
				quickViewInfoWidth = parseInt(defaultMaxQuickWidth - desktopSliderWidth);
	
			if(mobileScreen) {		
			
				finalHeight = finalWidth * heightSelected/widthSelected;
				finalLeft = (winWidth - sliderFinalWidth)/2;
				finalTop = (winHeight - finalHeight)/2;	
				quickViewWidth = finalWidth;
				quickViewHeight = ( winHeight * 0.8 < maxQuickHeight ) ? winHeight * 0.8 : maxQuickHeight;
				quickViewLeft = finalLeft;
				quickViewTop = (winHeight - quickViewHeight)/2;
				quickViewInfoWidth = '100%';
			}

			if( animationType === 'open') {

				loadProductInfo(productId, function() {

					//hide the image in the gallery
					parentListItem.addClass('empty-box');
					//place the quick view over the image gallery and give it the dimension of the gallery image
					quickView.css({
					    "top": topSelected,
					    "left": leftSelected,
					    "width": widthSelected,
					}).velocity({
						//animate the quick view: animate its width and center it in the viewport
						//during this animation, only the slider image is visible
					    'top': finalTop+ 'px',
					    'left': finalLeft+'px',
					    'width': finalWidth+'px',
					    "scaleX": '1',
					    "scaleY": '1',
					    "opacity": 1
					}, 800, [ 400, 20 ], function(){
	
						if(winHeight <= maxQuickHeight){
							quickViewTop = 0;	
						}

						if(mobileScreen) {		

							quickViewTop = (winHeight - (quickView.find('.wooqv-item-info').outerHeight(true) + quickView.find('.wooqv-slider-wrapper').outerHeight(true)))/2;
						}
			
						//animate the quick view: animate its width to the final value
						quickView.addClass('wooqv-animate-width').velocity({
							'top': quickViewTop+'px',
							'left': quickViewLeft+'px',
					    	'width': quickViewWidth+'px'
						}, 300, 'ease' ,function(){
							//show quick view content
							
							quickView.find('.wooqv-item-info').css('width', quickViewInfoWidth);
							
							quickView.addClass('wooqv-add-content');
							quickView.addClass('wooqv-preview-gallery');
							setTimeout(function() {
								quickView.removeClass('wooqv-preview-gallery');
							},2000);
							
							if(winHeight <= maxQuickHeight){
								resizeQuickView();
							}
							
							$('html').removeClass('wooqv-loading');
							
						});
						
					}).addClass('wooqv-is-visible');
					
					isVisible = true;
				
				});
				
			} else {
				//close the quick view reverting the animation
				quickView.removeClass('wooqv-add-content').velocity({
				    'top': finalTop+ 'px',
				    'left': finalLeft+'px',
				    'width': finalWidth+'px',
				}, 300, 'ease', function(){
					$('html').removeClass('wooqv-active');
					quickView.removeClass('wooqv-animate-width').velocity({
						"top": topSelected,
					    "left": leftSelected,
					    "width": widthSelected,
					    "scaleX": '0.5',
					    "scaleY": '0.5', 
					    "opacity": 0
					}, 500, 'ease', function(){
						
						isVisible = false;
						quickView.removeClass('wooqv-is-visible');
						parentListItem.removeClass('empty-box');
						resizeQuickView();
					});
				});
			}
		}
		
		function closeNoAnimation(image, finalWidth, maxQuickWidth) {
			
			image = image.length ? image : $('.empty-box');
			
			var parentListItem = image.closest('.product'),
				topSelected = image.offset().top - $(window).scrollTop(),
				leftSelected = image.offset().left,
				widthSelected = image.width();
	
			//close the quick view reverting the animation
			$('html').removeClass('wooqv-active');
			parentListItem.removeClass('empty-box');
			quickView.velocity("stop").removeClass('wooqv-add-content wooqv-animate-width wooqv-is-visible').css({
				"top": topSelected,
			    "left": leftSelected,
			    "width": widthSelected,
			});
			isVisible = false;
			resizeQuickView();
		}

		function loadProductInfo(id, callback) {

			if(id === recentProduct) {
	
				if(typeof(callback) !== 'undefined') {
					callback();
				}
				return false;			
			}
						
			$('html').addClass('wooqv-loading');

			var params = {
				action: 'wooqv_quick_view',
				id: id
			};
			
			$.ajax({
				url: WOOQV.ajaxurl,
				data: params,
				type: 'post', 
				success: function(data) {
					
					quickView.find('.wooqv-product').replaceWith($(data.product_item));
					
					onProductLoaded(id, data, callback);

				}
			});
			
		}
		
		
		function onProductLoaded(id, data, callback) {
				
			recentProduct = id;
		
			if(customizer) {
				recentProduct = null;
				customizerValuesChanged();
			}
			
			initVariationsEvents();
			
			if(typeof(callback) !== 'undefined') {
				callback(data);
			}
		}

		function initVariationsEvents() {
			
			if ( typeof wc_add_to_cart_variation_params !== 'undefined' ) {
				quickView.find( '.variations_form' ).each( function() {
					$( this ).wc_variation_form();
				});
			}
		}

		$(document).ready(function() {
					
			initVars();
			updateResponsiveVars();
			initEvents();
			resizeQuickView();
		
		});

	});


})( jQuery );
