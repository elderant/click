(function( $ ) {
	var click_handle_educacion_list_action = function(event) {
		//event.preventDefault();
		let $link = $(event.currentTarget);
		if(!$link.parent('.category-item').hasClass('active') &&
				$('#filter-row .category-item.active').length > 0) {

			let $menu = $('#filter-row .category-item.active');
			$menu.toggleClass('active');
			$menu.find('.category-list').slideToggle(500, function(){
				$(this).toggleClass('hidden');
				$menu.find('a.main-list .select-icon').each(function(){
					$(this).toggleClass('hidden');
				});
			});
		}

		// change link icon displayed.
		$link.find('.select-icon').each(function(){
			$(this).toggleClass('hidden');
		});

		$link.siblings('.category-list').slideToggle(500, function() {
			$(this).toggleClass('hidden');
		})

		$link.parents('.category-item').toggleClass('active');
	}

	var click_handle_educacion_filter_action = function(event, $container) {
		event.preventDefault();
		let $link = $(event.currentTarget);
		let category = $link.find('input').val();
		let name = $link.find('input').attr('name');

		if($link.find('label').hasClass('active')) {
			if(name === 'option-all') {
				let categoryArray = category.trim().split(/\s+/);
				for(let i = 0; i < categoryArray.length; i++) {
					let categoryPosition = window.click.educacion.filter.indexOf(categoryArray[i]);
					window.click.educacion.filter.splice(categoryPosition, 1);
				}
			}
			else {
				let categoryPosition = window.click.educacion.filter.indexOf(category);
				window.click.educacion.filter.splice(categoryPosition, 1);
			}
		}
		else {
			if(name === 'option-all') {
				let categoryArray = category.trim().split(/\s+/);
				for(let i = 0; i < categoryArray.length; i++) {
					window.click.educacion.filter.push(categoryArray[i]);	
				}
			}
			else {
				window.click.educacion.filter.push(category);
			}
		}

		$container.find('.edgtf-portfolio-item').each(function() {
			let filterArray = $(this).attr('data-filter').trim().split(/\s+/);
			let filtered = true;
			if(window.click.educacion.filter.length === 0) {
				filtered = false;
			}
			else {
				for (let i = 0; i < filterArray.length; i++) {
					if(window.click.educacion.filter.indexOf(filterArray[i]) !== -1) {
						filtered = false;
						break;
					}
				}
			}
			if(filtered == false && $(this).hasClass('removed')) {
				$(this).width('32%');
				$(this).removeClass('removed');
			}
			else if(filtered == true && !$(this).hasClass('removed')) {
				let itemWidth = $(this)[0].scrollWidth;
				
				$(this).width(itemWidth);
				$(this).css('width', '0');
				$(this).addClass('removed');
			}

		});
		// remove active from filter ui.
		$link.find('label').toggleClass('active');
	}

	var click_client_code_verification = function(event) {
    if(window.click.shopCodes === undefined || window.click.shopCodes.length === 0) {
      click_retreive_shop_codes();
    }
    
    let typedCode = event.currentTarget.value;
    let typedCodeHashed = md5(typedCode);
    if(window.click.shopCodes.includes(typedCodeHashed)) {
      $(event.currentTarget).siblings('input[type="submit"]').prop('disabled', false);
    }
    else {
      $(event.currentTarget).siblings('input[type="submit"]').prop('disabled', true);
    }
  }

  var click_retreive_shop_codes = function() {
    $.ajax({
      url : ajax_params.ajax_url,
      type : 'post',
      async: false,
      data : {
        action : 'get_shop_codes_array',
      },
      success : function( response ) {
        let data = JSON.parse(response);
        let shopCodesTemp = [];
        
        Object.keys(data).forEach(function(key) {
          shopCodesTemp.push(data[key]['code'])
        });

        webStateWaiting(false);
        window.click.shopCodes = shopCodesTemp;
      },
      error : function ( response ) {
        //$('').html('<p></p>');
        console.log(response);
      },
      beforeSend: function() {
        webStateWaiting(true);
        return true;
      },
    });
	}
	
	/**************************** Helper funcitons ********************************/
  var isMobile = function(){
    return $(window).width() <= 1023;
  };
  var isDesktop = function(){
    return $(window).width() > 1023;
  };

  /**
		* Disables all links and changes cursor for the website, used in ajax calls.
		*/
	var webStateWaiting = function(waiting){
		if(waiting) {
			$('body').css('cursor', 'progress');
		}
		else {
			$('body').css('cursor', 'default');
		}
		
		$('a').each(function() {
			if(!$(this).hasClass('disabled') && waiting && !$(this).hasClass('language-option') && !$(this).hasClass('menu-end-post-denominacion-a')) {
				$(this).addClass('disabled');	
			}
			else if ($(this).hasClass('disabled') && !waiting && !$(this).hasClass('language-option') && !$(this).hasClass('menu-end-post-denominacion-a')) {
				$(this).removeClass('disabled');
			}
		});
  }
  
  $(document).ready(function($){
    if($('.page-id-1543').length > 0) {
			window.click = {educacion : {filter : []}};
			let $container = $('#content-row .edgtf-portfolio-list-holder');
			$container.find('.edgtf-portfolio-item').each(function() {
				let classNameArray = $(this).attr('class').trim().split(/\s+/);
				let filterString = '';
				for(let i = 0; i < classNameArray.length; i++) {
					if(classNameArray[i].startsWith('portfolio_category_')) {
						filterString += classNameArray[i].substr(19) + ' ';
					}
				}
				$(this).attr('data-filter', filterString);
			});

      $('#filter-row .list-action.main-list').on('click', function(event){
        click_handle_educacion_list_action(event);
			});
			
			$(document).click(function(event) { 
				if(!$(event.target).closest('#filter-row .category-item.active').length) {
					let $menu = $('#filter-row .category-item.active');
					$menu.toggleClass('active');
					$menu.find('.category-list').slideToggle(500, function(){
						$(this).toggleClass('hidden');
						$menu.find('a.main-list .select-icon').each(function(){
							$(this).toggleClass('hidden');
						});
					});
				}        
			});

			$('.page-id-1543 #filter-row .category-item .list-action:not(.main-list)').on('click', function(event){
        click_handle_educacion_filter_action(event, $container);
      });
		}
		
		if($('.page-id-3566').length > 0) {
			$('header nav #nav-menu-item-3825 .second a, ' +
			'header nav #sticky-nav-menu-item-3825 .second a').each(function(index, link) {
				$(link).on('click', function(event) {
					// event.stopPropagation();
					event.preventDefault();
					let $link = $(event.currentTarget);
					let elementId = $link.attr('href').substr(21);

					$('html').animate({ scrollTop: $(elementId).offset().top }, 500);
				});
			});

			$('.edgtf-tc-nav-prev .edgtf-nav-label').html('ATRAS');
			$('.edgtf-tc-nav-next .edgtf-nav-label').html('ADELANTE');
		}

		//Main shop page
		if($('.page-id-5065').length > 0) {
      //console.log('entering ready function and creating array');
      window.click = {shopCodes : []};
      $('.edgtf-content .shop-code-section input[name="shop_code"]').on('change onkeyup paste input', click_client_code_verification);
    }

		if($('.woocommerce-checkout').length > 0) {
			$('form.checkout').on('change', 'input[name^="shipping_method"]', function() {
				if($(this).val() == 'flat_rate:9') {
					$('#customer_details .woocommerce-billing-fields #custom_checkout_field').removeClass('hidden');
				}
				else {
					$('#customer_details .woocommerce-billing-fields #custom_checkout_field').addClass('hidden');
				}
			});
		}

  });
})(jQuery);
