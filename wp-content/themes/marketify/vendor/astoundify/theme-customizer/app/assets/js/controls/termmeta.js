/* global wp, jQuery */
(function( $ ){
	var api = wp.customize || {};

	/**
	 * Dynamically add a setting.
	 *
	 * @since 1.2.0
	 */
	var addSetting = function( args ) {
		var defaults = {
			fresh: true
		};

		args = _.defaults( args, defaults );

		var section = api.section( args.linked.section() );
		var type = args.linked.params.type;
		var settingId = args.linked.id + '[' + args.linked.taxonomy + '][' + args.termId + ']';

	  /**
		 * If no setting exists, create one.
		 *
		 * If the data is not fresh, set the value here, otherwise allow it to be set later.
		 */
		if ( ! api.has( settingId ) ) {
			var setting = api.create( settingId, settingId, ( ! args.fresh ? args.metaValue : '' ), {
				previewer: api.previewer,
				transport: false,
				dirty: false
			} );
		} 

		/**
		 * If no control exist, create one.
		 */
		if ( ! api.control.has( settingId ) ) {

			// // we have to add our own container
			// var controlContainer = section.container.append( 
			// 	$( '<li/>' )
			// 		.addClass( 'customize-control' )
			// 		.addClass( 'customize-control-edit-term-' + type )
			// 		.attr( 'id', 'customize-control-my-term-colors-' + args.linked.taxonomy + '-' + args.termId )
			// );

			// currently supported edit control types
			var typeConstructor = {
				'TermMetaColor': 'EditTermMetaColorControl'
			};

			var constructor = api[ typeConstructor[ type ] ];

			// create a control 
			var control = new constructor( settingId, {
				params: {
					section: section.id,
					priority: 99,
					type: 'edit-term-' + type,
					settings: {
					},

					metaKey: args.metaKey,
					termLabel: args.termLabel,
					termId: args.termId
				},
				previewer: api.previewer
			} );
			
			console.log(control);

			// add to the api
			api.control.add( settingId, control );
			console.log(control.section.get());
			control.embed();
		}

		// set the value. If previously set it will not change and be marked dirty
		api( settingId ).set( args.metaValue );
	};

	/**
	 * Search for terms and associate data with them
	 *
	 * @since 1.2.0
	 */
	api.AddTermMetaControl = api.Control.extend({

		/**
		 * When the control has been embedded in to the section.
		 *
		 * @since 1.5.0
		 * @param {int} id
		 * @param {Array} options
		 */
		ready: function() {
			api.Control.prototype.ready.apply( this );

			var control = this;

			// move some parameters to the root object
			control.$search = control.container.find( '.search-terms' );
			control.taxonomy = control.params.taxonomy;

			// add each of the existing terms
			_.each( control.params.values, function( term ) {
				addSetting({
					termLabel: term.label,
					termId: term.id,
					metaValue: term.value,

					fresh: false,
					linked: control
				});
			});

			// add a way to search terms
			control.addSearch();

			// add a way to attach meta to the term
			control.addMetaSetter();

			// add/remove terms
			_.bindAll( control, 'addTerms' );

			control.container.on( 'click', '.js-astoundify-themecustomizer-add-term', control.addTerms );
		},

		/**
		 * Remove a term.
		 *
		 * @since 1.2.0
		 */
		addTerms: function() {
			var control = this;

			control.selectedTerms = control.$search.find( 'option:selected' );

			_.each( control.selectedTerms, function(term) {
				addSetting({
					termLabel: term.text(),
					termId: term.val(),
					metaValue: control.getMetaValue(),

					fresh: true,
					linked: control
				});
			} );
		},

		/**
		 * Remove a term.
		 *
		 * @since 1.2.0
		 */
		removeTerm: function() {
			var control = this;

			control.setting.set( '' );
			api.control.remove( control.id );
			control.container.remove();
		},

		/**
		 * AJAX term searching.
		 *
		 * Using select2 create a searchable select box that returns relevant results.
		 *
		 * @since 1.2.0
		 */
		addSearch: function() {
			var control = this;

			// this only looks nasty...
			control.$search.select2({
				width: '100%',
				allowClear: true,
				placeholder: 'Search for terms...'
			});
		},

	});

	/**
	 * Search for terms and associate data with them
	 *
	 * This control does not do anything by itself and should
	 * only be used as a base to associate different information with a term.
	 *
	 * @since 1.2.0
	 */
	api.controlConstructor.TermMetaColor = api.AddTermMetaControl.extend({

		/**
		 * Use the value the control's render_content() to craete a term.
		 *
		 * @since 1.2.0
		 */
		addMetaSetter: function() {
			var control = this;

			control.metaSetter = control.container.find( '.' + control.id + '-color-picker' );
			control.metaSetter.wpColorPicker();
		},

		/**
		 * Get the set met value.
		 *
		 * @since 1.2.0
		 */
		getMetaValue: function() {
			var control = this;

			return control.metaSetter.wpColorPicker( 'color' );
		}

	});

	/**
	 * Edit Term Control
	 *
	 * Generic control for editing a term that contains associated information.
	 * Automatically binds a removal method, but handling additional instantiation
	 * on addition of a control should be be added in an extending Control.
	 *
	 * @since 1.2.0
	 */
	api.EditTermMetaControl = api.Control.extend({

		/**
		 * When the control has been embedded in to the section.
		 *
		 * @since 1.2.0
		 * @param {int} id
		 * @param {Array} options
		 */
		ready: function() {
			api.Control.prototype.ready.apply( this );

			var control = this;

			_.bindAll( control, 'removeTerm' );

			control.container.on( 'click', '.js-astoundify-themecustomizer-remove-term', control.removeTerm );
		},

		/**
		 * Remove the control (and term meta)
		 */
		removeTerm: function() {
			var control = this;

			control.setting.set( '' );
			api.control.remove( control.id );
			control.container.remove();
		}

	});

	/**
	 * Edit Color Control
	 *
	 * @since 1.2.0
	 */
	api.EditTermMetaColorControl = api.EditTermMetaControl.extend({

		initialize: function( id, options ) {
			var control = this;
			api.EditTermMetaControl.prototype.initialize.call( control, id, options );
		},

		/**
		 * When the control has been embedded in to the section.
		 *
		 * @since 1.2.0
		 * @param {int} id
		 * @param {Array} options
		 */
		ready: function() {
			api.EditTermMetaControl.prototype.ready.apply( this );

			var control = this;

			// create the color picker control
			api.ColorControl.prototype.ready.apply( this );
		} 

	});

})( jQuery );
