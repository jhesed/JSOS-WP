CHARITABLE_ADMIN = window.CHARITABLE_ADMIN || {};

/**
 * Datepicker
 */
( function( exports, $ ){

	if ( ! $.fn.datepicker ) {
		return;
	}

	var Datepicker = function( $el ) {
	
		this.$el = $el;
		options = {
			dateFormat 	: 'MM d, yy', 
			minDate 	: this.$el.data('min-date') || '',
			beforeShow	: function( input, inst ) {
				$('#ui-datepicker-div').addClass('charitable-datepicker-table');
			}	
		}

		this.$el.datepicker( options );

		if ( this.$el.data('date') ) {
			this.$el.datepicker( 'setDate', this.$el.data('date') );
		}

		if ( this.$el.data('min-date') ) {
			this.$el.datepicker( 'option', 'minDate', this.$el.data('min-date') );
		}
	}

	exports.Datepicker = Datepicker;

})( CHARITABLE_ADMIN, jQuery );

/**
 * Conditional settings.
 */
( function( exports, $ ){

	var Settings = function( $el ) {
		var triggers = [];

		var toggle_setting = function($setting, $trigger) {
			var $tr = $setting.parents('tr').first(),
				value = $setting.data('show-only-if-value'),
				show = (function(){
					if ('checked' === value) {
						return $trigger.is(':checked');
					}
					else if ('selected' === value) {
						return $trigger.selected();
					}
					else {
						return $trigger.val() === value;
					}
				})();

			if (show) {
				$tr.show();
			}
			else {
				$tr.hide();
			}
		};

		this.$el = $el;

		this.$el.find( '[data-show-only-if-key]' ).each( function(){
			var $this = $(this),
				trigger_id = '#' + $this.data('show-only-if-key');

			if ( 'undefined' === typeof triggers[trigger_id] ) {
				triggers[trigger_id] = [];
			}

			triggers[trigger_id].push( $this );
		});

		for ( trigger in triggers ) {
			var $trigger = $(trigger);			

			if ( ! triggers.hasOwnProperty( trigger ) ) {
				continue;
			}

			$trigger.on( 'change', function() {
				var settings = triggers[trigger];
				
				for ( setting_key in triggers[trigger] ) {
				
					if ( ! triggers[trigger].hasOwnProperty( setting_key ) ) {
						continue;
					}

					toggle_setting( triggers[trigger][setting_key], $trigger );
				};

				

				// console.log(triggers[id]);

				// triggers[id].each( function(){
				// 	console.log(this);

				// 	toggle_setting(this, show);
				// });

				// if (show) {
				// 	$tr.show();
				// }
				// else {
				// 	$tr.hide();
				// }

			}).change();
		};		
	};	

	exports.Settings = Settings;

})( CHARITABLE_ADMIN, jQuery );



( function($){

	var setup_charitable_ajax = function() {
		$('[data-charitable-action]').on( 'click', function( e ){
			var data 	= $(this).data( 'charitable-args' ) || {}, 
				action 	= 'charitable-' + $(this).data( 'charitable-action' );

			$.post( ajaxurl, 
				{
					'action'	: action,
					'data'		: data
				}, 
				function( response ) {
					console.log( "Response: " + response );
				} 
			);

			return false;
		} );
	};

	var setup_charitable_toggle = function() {
		$( '[data-charitable-toggle]' ).on( 'click', function( e ){
			var toggle_id = $(this).data( 'charitable-toggle' ), 
				toggle_text = $(this).attr( 'data-charitable-toggle-text' );

			if ( toggle_text && toggle_text.length ) {
				$(this).attr( 'data-charitable-toggle-text', $(this).text() );
				$(this).text( toggle_text );
			}

			$('#' + toggle_id).toggle();

			return false;
		} );
	};

	var setup_advanced_meta_box = function() {
		var $meta_box = $('#charitable-campaign-advanced-metabox');

		$meta_box.tabs();

		var min_height = $meta_box.find('.charitable-tabs').height();

		$meta_box.find('.ui-tabs-panel').each( function(){
			$(this).css( 'min-height', min_height );
		});
	};

	var toggle_custom_donations_checkbox = function() {
		var $custom = $('#campaign_allow_custom_donations'), 
			$suggestions = $('.charitable-campaign-suggested-donations tbody tr:not(.to-copy)'),
			has_suggestions = $suggestions.length > 1 || false === $suggestions.first().hasClass('no-suggested-amounts');
	
		$custom.attr( 'disabled', ! has_suggestions );

		if ( ! has_suggestions ) {
			$custom.prop( 'checked', true );
		}
	};

	var setup_sortable_suggested_donations = function(){
		$('.charitable-campaign-suggested-donations tbody').sortable({
			items: "tr:not(.to-copy)",
			handle: ".handle",
			stop: function( event, ui ) {
				reindex_rows();
			}

	    });
	};
		
	var add_suggested_amount_row = function( $button ) {
		var $table = $button.closest( '.charitable-campaign-suggested-donations' ).find('tbody');
		var $clone = $table.find('tr.to-copy').clone().removeClass('to-copy hidden');
		$table.find( '.no-suggested-amounts' ).hide();
		$table.append( $clone );
		reindex_rows();
		toggle_custom_donations_checkbox();
	};	

	var delete_suggested_amount_row = function($button) {
		console.log($button);
		$button.closest( 'tr' ).remove();
		var $table = $button.closest('.charitable-campaign-suggested-donations').find('tbody');
		if( $table.find( 'tr:not(.to-copy)' ).length == 1 ){
			$table.find( '.no-suggested-amounts' ).removeClass('hidden').show();
		}
		reindex_rows();
		toggle_custom_donations_checkbox();
	};	

	var reindex_rows = function(){
		$('.charitable-campaign-suggested-donations tbody').each(function(){
			$(this).children('tr').not('.no-suggested-amounts .to-copy').each(function(index) {
				$(this).data('index', index );
				$(this).find('input').each(function(i) {
					this.name = this.name.replace(/(\[\d\])/, '[' + index + ']');
				});
			});
		});		
	};

	var setup_dashboard_widgets = function() {
		var $widget = $( '#charitable_dashboard_donations' );

		if ( $widget.length ) {
			$.ajax({
				type: "GET",
				data: {
					action: 'charitable_load_dashboard_donations_widget'
				},
				url: ajaxurl,
				success: function (response) {
					$widget.find( '.inside' ).html( response );
				}
			});
		}
	};

	$(document).ready( function(){

		if ( CHARITABLE_ADMIN.Datepicker ) {
			$( '.charitable-datepicker' ).each( function() {
				CHARITABLE_ADMIN.Datepicker( $(this ) ); 
			});
		}

		$( '#charitable-settings' ).each( function(){
			CHARITABLE_ADMIN.Settings( $(this) );
		});

		$('body.post-type-campaign .handlediv, body.post-type-donation .handlediv').remove();
		$('body.post-type-campaign .hndle, body.post-type-donation .hndle').removeClass( 'hndle ui-sortable-handle' ).addClass( 'postbox-title' );

		setup_advanced_meta_box();
		setup_sortable_suggested_donations();
		toggle_custom_donations_checkbox();
		setup_charitable_ajax();	
		setup_charitable_toggle();	
		setup_dashboard_widgets();

		$('[data-charitable-add-row]').on( 'click', function() {
			var type = $( this ).data( 'charitable-add-row' );

			if ( 'suggested-amount' === type ) {
				add_suggested_amount_row($(this));
			}

			return false; 
		});

		$('.charitable-campaign-suggested-donations').on( 'click', '.charitable-delete-row', function() { console.log('clicked');
			delete_suggested_amount_row( $(this) );
			return false;
		});

		$('body').on( 'click', '[data-campaign-benefactor-delete]', function() {			
			var $block = $( this ).parents( '.charitable-benefactor' ),
				data = {
					action 			: 'charitable_delete_benefactor',
					benefactor_id 	: $(this).data( 'campaign-benefactor-delete' ), 
					nonce 			: $(this).data( 'nonce' )
				};

			$.ajax({
				type: "POST",
				data: data,
				dataType: "json",
				url: ajaxurl,
				xhrFields: {
					withCredentials: true
				},
				success: function (response) {
					if ( response.deleted ) {
						$block.remove();
					}
				}
			}).fail(function (data) {
				if ( window.console && window.console.log ) {
					console.log( 'failture' );
					console.log( data );
				}
			});

			return false;
		});

		$('#change-donation-status').on( 'change', function() {
			$(this).parents( 'form' ).submit();
		});
	});

})( jQuery );