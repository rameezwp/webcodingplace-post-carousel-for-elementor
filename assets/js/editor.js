/**
 * Editor script: the "dpce-query" control.
 *
 * Extends Elementor's Select2 control so posts and terms are searched over
 * the REST API instead of being printed into the page. Saved IDs get their
 * labels from the same endpoint.
 */
( function () {
	'use strict';

	var labelCache = {};

	var cacheKey = function ( query, id ) {
		return query.kind + ':' + query.source + ':' + id;
	};

	var request = function ( query, params ) {
		return window.wp.apiFetch( {
			path: window.wp.url.addQueryArgs( '/dpce/v1/search', Object.assign( { kind: query.kind, source: query.source }, params ) ),
		} );
	};

	window.addEventListener( 'elementor/init', function () {
		var Select2 = window.elementor.modules.controls.Select2;

		var QueryControl = Select2.extend( {
			getQuery: function () {
				return this.model.get( 'query' ) || {};
			},

			getSavedIds: function () {
				var value = this.getControlValue();
				if ( ! value ) {
					return [];
				}
				return ( Array.isArray( value ) ? value : [ value ] ).map( String ).filter( Boolean );
			},

			getSelect2Options: function () {
				var query = this.getQuery();

				return window.jQuery.extend( Select2.prototype.getSelect2Options.apply( this, arguments ), {
					minimumInputLength: 0,
					ajax: {
						delay: 250,
						transport: function ( params, success, failure ) {
							var data = params.data || {};
							request( query, { search: data.term || '', page: data.page || 1 } )
								.then( success )
								.catch( failure );
						},
						processResults: function ( data ) {
							( data.results || [] ).forEach( function ( item ) {
								labelCache[ cacheKey( query, item.id ) ] = item.text;
							} );
							return { results: data.results || [], pagination: { more: !! data.more } };
						},
					},
				} );
			},

			/**
			 * Make sure every saved ID has an <option> before Select2 starts,
			 * otherwise the selection would be dropped.
			 */
			addSavedOptions: function () {
				var self = this,
					query = this.getQuery(),
					$select = this.ui.select,
					missing = [];

				this.getSavedIds().forEach( function ( id ) {
					var $option = $select.find( 'option' ).filter( function () {
						return this.value === id;
					} );
					var label = labelCache[ cacheKey( query, id ) ];

					if ( ! $option.length ) {
						$option = window.jQuery( '<option>' ).val( id ).text( label || '#' + id ).appendTo( $select );
					}
					$option.prop( 'selected', true );

					if ( ! label ) {
						missing.push( id );
					}
				} );

				if ( ! missing.length || ! query.kind ) {
					return;
				}

				request( query, { include: missing } ).then( function ( data ) {
					( data.results || [] ).forEach( function ( item ) {
						labelCache[ cacheKey( query, item.id ) ] = item.text;
						$select.find( 'option' ).filter( function () {
							return this.value === item.id;
						} ).text( item.text );
					} );
					// Select2 caches option labels, so rebuild it to show the new ones.
					// The saved value is untouched: the same options stay selected.
					if ( ! self.isDestroyed && self.select2Instance ) {
						self.select2Instance.destroy();
						Select2.prototype.applySavedValue.call( self );
					}
				} );
			},

			applySavedValue: function () {
				this.addSavedOptions();
				Select2.prototype.applySavedValue.apply( this, arguments );
			},
		} );

		window.elementor.addControlView( 'dpce-query', QueryControl );

		registerNewWidgetDefaults();
	} );

	/**
	 * New carousels get the modern engine (and the pause button).
	 *
	 * Elementor does not save settings that equal the control default, so
	 * the Slider Engine default has to stay "slick" to keep old carousels on
	 * the classic engine. Instead, a carousel dragged in from the panel (it
	 * has no settings yet) is switched to "swiper" right after it is created.
	 * Pasted or duplicated carousels keep their own settings.
	 */
	function registerNewWidgetDefaults() {
		var $e = window.$e;
		if ( ! $e || ! $e.modules || ! $e.modules.hookData || ! $e.modules.hookData.After ) {
			return;
		}

		var isNewCarousel = function ( model ) {
			return model && 'dpce_post_carousel' === model.widgetType &&
				( ! model.settings || 0 === Object.keys( model.settings ).length );
		};

		class NewCarouselDefaults extends $e.modules.hookData.After {
			getCommand() {
				return 'document/elements/create';
			}

			getId() {
				return 'dpce-new-carousel-defaults';
			}

			getConditions( args ) {
				return isNewCarousel( args && args.model );
			}

			apply( args, result ) {
				var containers = Array.isArray( result ) ? result : [ result ];
				containers.forEach( function ( container ) {
					if ( ! container || ! container.settings ) {
						return;
					}
					$e.internal( 'document/elements/set-settings', {
						container: container,
						settings: {
							slider_engine: 'swiper',
							pause_button: 'yes',
							style_id: 'card',
						},
						options: { render: true },
					} );
				} );
			}
		}

		$e.hooks.registerDataAfter( new NewCarouselDefaults() );
	}
}() );
