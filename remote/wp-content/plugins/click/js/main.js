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

  });
})(jQuery);