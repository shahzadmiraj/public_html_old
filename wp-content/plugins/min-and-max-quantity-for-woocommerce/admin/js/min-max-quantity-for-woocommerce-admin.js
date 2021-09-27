(function( $ ) {
	'use strict';
	jQuery( '.multiselect2' ).select2();

	/**
	 * On load initialize the variables and activate the current menu
	 */
	$( window ).load( function() {
		$( 'a[href="admin.php?page=mmqw-rules-list"]' ).parent().addClass( 'current' );
		$( 'a[href="admin.php?page=mmqw-rules-list"]' ).addClass( 'current' );

		if ( jQuery( '#shipping-methods-listing tbody tr' ).length <= 0 ) {
			jQuery( '#delete-shipping-method' ).hide();
			jQuery( '.shipping-methods-order' ).hide();
		}

		/** Start: Get last url parameters */
		function getUrlVars() {
			var vars = [], hash, get_current_url;
			get_current_url = coditional_vars.current_url;
			var hashes = get_current_url.slice( get_current_url.indexOf( '?' ) + 1 ).split( '&' );
			for ( var i = 0; i < hashes.length; i ++ ) {
				hash = hashes[ i ].split( '=' );
				vars.push( hash[ 0 ] );
				vars[ hash[ 0 ] ] = hash[ 1 ];
			}
			return vars;
		}

		/** End: Get last url parameters */

		/** description toggle */
		$( 'span.mmqw_for_woocommerce_tab_description' ).click( function( event ) {
			event.preventDefault();
			$( this ).next( 'p.description' ).toggle();
		} );
		$( 'span.option_for_woocommerce_tab_description' ).click( function( event ) {
			event.preventDefault();
			$( this ).next( 'p.description' ).toggle();
		} );

		/** Add AP Product functionality start */
		/** get total count row from hidden field */
		var row_product_ele = $( '#total_row_product' ).val();
		var count_product;
		if ( row_product_ele > 2 ) {
			count_product = row_product_ele;
		} else {
			count_product = 2;
		}
		/** on click add rule create new method row */
		$( 'body' ).on( 'click', '#ap-product-add-field', function() {
			createAdvancePricingRulesField( 'select', 'qty', 'product', count_product, 'prd', '' );
			jQuery( '.multiselect2' ).select2();
			getProductListBasedOnThreeCharAfterUpdate();
			numberValidateForAdvanceRules();
			count_product ++;
		} );

		/** Add AP Product functionality end here */

		/** Add AP variable product functionality start */
		/** get total count row from hidden field */
		var row_total_row_product_variation_ele = $( '#total_row_product_variation' ).val();
		var count_product_variation;
		if ( row_total_row_product_variation_ele > 2 ) {
			count_product_variation = row_total_row_product_variation_ele;
		} else {
			count_product_variation = 2;
		}

		/** on click add rule create new method row */
		$( 'body' ).on( 'click', '#ap-product-variation-add-field', function() {
			/** design new format of advanced pricing method row html */
			createAdvancePricingRulesField( 'select', 'qty', 'product_variation', count_product_variation, 'product_variation', '' );
			jQuery( '.multiselect2' ).select2();
			getProductListBasedOnThreeCharAfterUpdate();
			numberValidateForAdvanceRules();
			count_product_variation ++;
		} );

		/** Add AP Category functionality start here */
		/** get total count row from hidden field */
		var row_category_ele = $( '#total_row_category' ).val();
		var row_category_count;
		if ( row_category_ele > 2 ) {
			row_category_count = row_category_ele;
		} else {
			row_category_count = 2;
		}
		/** on click add rule create new method row */
		$( 'body' ).on( 'click', '#ap-category-add-field', function() {
			createAdvancePricingRulesField( 'select', 'qty', 'category', row_category_count, 'cat', 'category_list' );
			jQuery( '.multiselect2' ).select2();
			numberValidateForAdvanceRules();
			row_category_count ++;
		} );

		/** Add AP Country functionality start here **/
		var row_country_ele = $( '#total_row_country' ).val();
		var row_country_count;
		if ( row_country_ele > 2 ) {
			row_country_count = row_country_ele;
		} else {
			row_country_count = 2;
		}

		/** AP country section validation */
		$( 'body' ).on( 'click', '#ap-country-add-field', function() {
			createAdvancePricingRulesField( 'select', 'subtotal', 'country', row_country_count, 'country', 'country_list' );
			jQuery( '.multiselect2' ).select2();
			numberValidateForAdvanceRules();
			row_country_count ++;
		} );

		/** get total count row from hidden field fro cart qty */
		var total_cart_qty_ele = $( '#total_row_total_cart_qty' ).val();
		var total_cart_qty_count;
		if ( total_cart_qty_ele > 2 ) {
			total_cart_qty_count = total_cart_qty_ele;
		} else {
			total_cart_qty_count = 2;
		}
		/** on click add rule create new method row for total cart */
		$( 'body' ).on( 'click', '#ap-total-cart-qty-add-field', function() {
			createAdvancePricingRulesField( 'label', 'qty', 'total_cart_qty', total_cart_qty_count, 'total_cart_qty', '' );
			numberValidateForAdvanceRules();
			total_cart_qty_count ++;
		} );

		/**
		 * Advanced pricing section tab on click toggle inner layout
		 */
		$( 'ul.tabs li' ).click( function() {
			var tab_id = $( this ).attr( 'data-tab' );

			$( 'ul.tabs li' ).removeClass( 'current' );
			$( '.tab-content' ).removeClass( 'current' );

			$( this ).addClass( 'current' );
			$( '#' + tab_id ).addClass( 'current' );
		} );

		/**
		 * Create new advanced price section html input element based on passed the parameters.
		 *
		 * @param field_type
		 * @param qty_or_weight
		 * @param field_title
		 * @param field_count
		 * @param field_title2
		 * @param category_list_option
		 */
		function createAdvancePricingRulesField( field_type, qty_or_weight, field_title, field_count, field_title2, category_list_option ) {
			var label_text, min_input_placeholder, max_input_placeholder, inpt_class, inpt_type;
			if ( qty_or_weight == 'qty' ) {
				label_text = coditional_vars.cart_qty;
			} else if ( qty_or_weight == 'weight' ) {
				label_text = coditional_vars.cart_weight;
			} else if ( qty_or_weight == 'subtotal' ) {
				label_text = coditional_vars.cart_subtotal;
			} else {
				label_text = coditional_vars.cart_qty;
			}

			if ( qty_or_weight == 'qty' ) {
				min_input_placeholder = coditional_vars.min_quantity;
			} else if ( qty_or_weight == 'weight' ) {
				min_input_placeholder = coditional_vars.min_weight;
			} else if ( qty_or_weight == 'subtotal' ) {
				min_input_placeholder = coditional_vars.min_subtotal;
			}

			if ( qty_or_weight == 'qty' ) {
				max_input_placeholder = coditional_vars.max_quantity;
			} else if ( qty_or_weight == 'weight' ) {
				max_input_placeholder = coditional_vars.max_weight;
			} else if ( qty_or_weight == 'subtotal' ) {
				max_input_placeholder = coditional_vars.max_subtotal;
			}

			if ( qty_or_weight == 'qty' ) {
				inpt_class = 'qty-class';
				inpt_type = 'number';
			} else if ( qty_or_weight == 'weight' ) {
				inpt_class = 'qty-class';
				inpt_type = 'number';
			} else if ( qty_or_weight == 'subtotal' ) {
				inpt_class = 'qty-class';
				inpt_type = 'number';
			}
			var tr = document.createElement( 'tr' );
			tr = setAllAttributes( tr, {
				'class': 'ap_' + field_title + '_row_tr',
				'id': 'ap_' + field_title + '_row_' + field_count,
			} );

			var product_td = document.createElement( 'td' );
			if ( field_type == 'select' ) {
				var product_select = document.createElement( 'select' );
				product_select = setAllAttributes( product_select, {
					'rel-id': field_count,
					'id': 'ap_' + field_title + '_fees_conditions_condition_' + field_count,
					'name': 'fees[ap_' + field_title + '_fees_conditions_condition][' + field_count + '][]',
					'class': 'min_max_select ap_' + field_title + ' product_fees_conditions_values multiselect2',
					'multiple': 'multiple',
					'data-placeholder': coditional_vars.validation_length1
				} );

				product_td.appendChild( product_select );

				if ( category_list_option == 'category_list' ) {
					var all_category_option = JSON.parse( $( '#all_category_list' ).html() );
					for ( var i = 0; i < all_category_option.length; i ++ ) {
						var option = document.createElement( 'option' );
						var category_option = all_category_option[ i ];
						option.value = category_option.attributes.value;
						option.text = allowSpeicalCharacter( category_option.name );
						product_select.appendChild( option );
					}
				}
				if ( category_list_option == 'country_list' ) {
					var all_category_option = JSON.parse( $( '#all_country_list' ).html() );
					for ( var i = 0; i < all_category_option.length; i ++ ) {
						var option = document.createElement( 'option' );
						var category_option = all_category_option[ i ];
						option.value = category_option.attributes.value;
						option.text = allowSpeicalCharacter( category_option.name );
						product_select.appendChild( option );
					}
				}
				if ( category_list_option == 'shipping_class_list' ) {
					var all_category_option = JSON.parse( $( '#all_shipping_class_list' ).html() );
					for ( var i = 0; i < all_category_option.length; i ++ ) {
						var option = document.createElement( 'option' );
						var category_option = all_category_option[ i ];
						option.value = category_option.attributes.value;
						option.text = allowSpeicalCharacter( category_option.name );
						product_select.appendChild( option );
					}
				}
			}
			if ( field_type == 'label' ) {
				var product_label = document.createElement( 'label' );
				var product_label_text = document.createTextNode( label_text );
				product_label = setAllAttributes( product_label, {
					'for': label_text.toLowerCase(),
				} );
				product_label.appendChild( product_label_text );
				product_td.appendChild( product_label );

				var input_hidden = document.createElement( 'input' );
				input_hidden = setAllAttributes( input_hidden, {
					'id': 'ap_' + field_title + '_fees_conditions_condition_' + field_count,
					'type': 'hidden',
					'name': 'fees[ap_' + field_title + '_fees_conditions_condition][' + field_count + '][]',
				} );
				product_td.appendChild( input_hidden );
			}
			tr.appendChild( product_td );

			var min_qty_td = document.createElement( 'td' );
			min_qty_td = setAllAttributes( min_qty_td, {
				'class': 'column_' + field_count + ' condition-value',
			} );
			var min_qty_input = document.createElement( 'input' );
			if ( qty_or_weight == 'qty' ) {
				min_qty_input = setAllAttributes( min_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '[]',
					'name': 'fees[ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': min_input_placeholder,
					'value': '',
					'min': '0',
					'required': '1',
				} );
			} else {
				min_qty_input = setAllAttributes( min_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '[]',
					'name': 'fees[ap_fees_ap_' + field_title2 + '_min_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': min_input_placeholder,
					'value': '',
					'min': '0',
					'required': '1',
				} );
			}

			min_qty_td.appendChild( min_qty_input );
			tr.appendChild( min_qty_td );

			var max_qty_td = document.createElement( 'td' );
			max_qty_td = setAllAttributes( max_qty_td, {
				'class': 'column_' + field_count + ' condition-value',
			} );
			var max_qty_input = document.createElement( 'input' );
			if ( qty_or_weight == 'qty' ) {
				max_qty_input = setAllAttributes( max_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '[]',
					'name': 'fees[ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': max_input_placeholder,
					'value': '',
					'min': '0',
				} );
			} else {
				max_qty_input = setAllAttributes( max_qty_input, {
					'type': inpt_type,
					'id': 'ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '[]',
					'name': 'fees[ap_fees_ap_' + field_title2 + '_max_' + qty_or_weight + '][]',
					'class': 'text-class ' + inpt_class,
					'placeholder': max_input_placeholder,
					'value': '',
					'min': '0',
				} );
			}

			max_qty_td.appendChild( max_qty_input );
			tr.appendChild( max_qty_td );

			var delete_td = document.createElement( 'td' );
			var delete_a = document.createElement( 'a' );
			delete_a = setAllAttributes( delete_a, {
				'id': 'ap_' + field_title + '_delete_field',
				'rel-id': field_count,
				'title': coditional_vars.delete,
				'class': 'delete-row',
				'href': 'javascript:;'
			} );
			var delete_i = document.createElement( 'i' );
			delete_i = setAllAttributes( delete_i, {
				'class': 'fa fa-trash'
			} );
			delete_a.appendChild( delete_i );
			delete_td.appendChild( delete_a );

			tr.appendChild( delete_td );

			$( '#tbl_ap_' + field_title + '_method tbody tr' ).last().after( tr );
		}

		/**
		 * On rule save change button click check the validation of the input fields
		 */
		$( '.mmqw-main-table input[name="submitFee"]' ).on( 'click', function( e ) {
			validation( e );
		} );

		/**
		 * Validate the advanced rules section
		 *
		 * @param e
		 * @returns {boolean}
		 */
		function validation( e ) {

			var validation_color_code = '#dc3232';
			var default_color_code = '#0085BA';
			var fees_pricing_rules_validation = true;

			if ( $( 'input[name="ap_rule_status"]' ).prop( 'checked' ) == true ) {
				if ( $( '.pricing_rules:visible' ).length != 0 ) {
					//set flag default to n
					var submit_prd_form_flag = true;
					var submit_prd_flag = false;

					var submit_prd_subtotal_form_flag = true;
					var submit_prd_subtotal_flag = false;

					var submit_cat_form_flag = true;
					var submit_cat_flag = false;

					var submit_cat_subtotal_form_flag = true;
					var submit_cat_subtotal_flag = false;

					var prd_val_arr = [];
					var prd_subtotal_val_arr = [];
					var cat_val_arr = [];
					var cat_subtotal_val_arr = [];

					var no_one_product_row_flag;
					var no_one_product_variation_row_flag;
					var no_one_category_row_flag;
					var no_one_country_row_flag;

					//Start loop each row of AP Product rules
					no_one_product_row_flag = $( '#tbl_ap_product_method tr.ap_product_row_tr' ).length;
					no_one_product_variation_row_flag = $( '#tbl_ap_product_variation_method tr.ap_product_variation_row_tr' ).length;
					no_one_category_row_flag = $( '#tbl_ap_category_method tr.ap_category_row_tr' ).length;
					no_one_country_row_flag = $( '#tbl_ap_country_method tr.ap_country_row_tr' ).length;

					var count_total_tr = no_one_product_row_flag +
						no_one_product_variation_row_flag +
						no_one_category_row_flag +
						no_one_country_row_flag;
					if ( $( '#tbl_ap_product_method tr.ap_product_row_tr' ).length ) {
						$( '#tbl_ap_product_method tr.ap_product_row_tr' ).each( function( index, item ) {
							//initialize variables
							var min_qty = '',
								max_qty = '';
							var product_id_count = '';
							var tr_id = jQuery( this ).attr( 'id' );
							var tr_int_id = tr_id.substr( tr_id.lastIndexOf( '_' ) + 1 );
							var max_qty_flag = true;

							//check product empty or not
							if ( jQuery( this ).find( '[name="fees[ap_product_fees_conditions_condition][' + tr_int_id + '][]"]' ).length ) {
								product_id_count = jQuery( this ).find( '[name="fees[ap_product_fees_conditions_condition][' + tr_int_id + '][]"]' ).find( 'option:selected' ).length;
								if ( product_id_count == 0 ) {
									jQuery( $( this ).find( '.select2-container .selection .select2-selection' ) ).css( 'border', '1px solid ' + validation_color_code );
								} else {
									jQuery( $( this ).find( '.select2-container .selection .select2-selection' ) ).css( 'border', '' );
								}
							}

							/** check if min quantity empty or not */
							if ( $( this ).find( '[name="fees[ap_fees_ap_prd_min_qty][]"]' ).length ) {
								min_qty = $( this ).find( '[name="fees[ap_fees_ap_prd_min_qty][]"]' ).val();
								if ( min_qty == '' ) {
									jQuery( $( this ).find( '[name="fees[ap_fees_ap_prd_min_qty][]"]' ) ).css( 'border', '1px solid ' + validation_color_code );
								} else {
									jQuery( $( this ).find( '[name="fees[ap_fees_ap_prd_min_qty][]"]' ) ).css( 'border', '' );
								}
							}
							//check if max quantity empty or not
							if ( $( this ).find( '[name="fees[ap_fees_ap_prd_max_qty][]"]' ).length ) {
								max_qty = $( this ).find( '[name="fees[ap_fees_ap_prd_max_qty][]"]' ).val();
								if ( max_qty != '' && min_qty != '' ) {
									max_qty = parseInt( max_qty );
									if ( min_qty > max_qty ) {
										jQuery( $( this ).find( '[name="fees[ap_fees_ap_prd_max_qty][]"]' ) ).css( 'border', '1px solid ' + validation_color_code );
										max_qty_flag = false;
									} else {
										jQuery( $( this ).find( '[name="fees[ap_fees_ap_prd_max_qty][]"]' ) ).css( 'border', '' );
									}
								}
							}

							if ( product_id_count == 0 && min_qty == '' ) {
								submit_prd_flag = false;
							} else if ( product_id_count == 0 ) {
								submit_prd_flag = false;
							} else if ( min_qty == '' ) {
								submit_prd_flag = false;
							} else if ( max_qty_flag == false ) {
								submit_prd_flag = false;
								displayMsg( 'message_prd_qty', coditional_vars.min_max_qty_error );
							} else {
								submit_prd_flag = true;
							}

							prd_val_arr[ tr_int_id ] = submit_prd_flag;

						} );

						if ( prd_val_arr != '' ) {
							var current_tab_id = jQuery( $( '#tbl_ap_product_method tr.ap_product_row_tr' ).parent().parent().parent().parent() ).attr( 'id' );
							if ( jQuery.inArray( false, prd_val_arr ) !== - 1 ) {
								submit_prd_form_flag = false;
								changeColorValidation( current_tab_id, false, validation_color_code );
							} else {
								submit_prd_form_flag = true;
								changeColorValidation( current_tab_id, true, default_color_code );
							}
						}
					}

					if ( $( '#tbl_ap_product_variation_method tr.ap_product_variation_row_tr' ).length ) {
						$( '#tbl_ap_product_variation_method tr.ap_product_variation_row_tr' ).each( function( index, item ) {
							//initialize variables
							var min_qty = '',
								max_qty = '';
							var product_id_count = '';
							var tr_id = jQuery( this ).attr( 'id' );
							var tr_int_id = tr_id.substr( tr_id.lastIndexOf( '_' ) + 1 );
							var max_qty_flag = true;

							/** check product empty or not */
							if ( jQuery( this ).find( '[name="fees[ap_product_variation_fees_conditions_condition][' + tr_int_id + '][]"]' ).length ) {
								product_id_count = jQuery( this ).find( '[name="fees[ap_product_variation_fees_conditions_condition][' + tr_int_id + '][]"]' ).find( 'option:selected' ).length;
								if ( product_id_count == 0 ) {
									jQuery( $( this ).find( '.select2-container .selection .select2-selection' ) ).css( 'border', '1px solid ' + validation_color_code );
								} else {
									jQuery( $( this ).find( '.select2-container .selection .select2-selection' ) ).css( 'border', '' );
								}
							}
							/** check if min quantity empty or not */
							if ( $( this ).find( '[name="fees[ap_fees_ap_product_variation_min_qty][]"]' ).length ) {
								min_qty = $( this ).find( '[name="fees[ap_fees_ap_product_variation_min_qty][]"]' ).val();
								if ( min_qty == '' ) {
									jQuery( $( this ).find( '[name="fees[ap_fees_ap_product_variation_min_qty][]"]' ) ).css( 'border', '1px solid ' + validation_color_code );
								} else {
									jQuery( $( this ).find( '[name="fees[ap_fees_ap_product_variation_min_qty][]"]' ) ).css( 'border', '' );
								}
							}
							//check if max quantity empty or not
							if ( $( this ).find( '[name="fees[ap_fees_ap_product_variation_max_qty][]"]' ).length ) {
								max_qty = $( this ).find( '[name="fees[ap_fees_ap_product_variation_max_qty][]"]' ).val();
								if ( max_qty != '' && min_qty != '' ) {
									max_qty = parseInt( max_qty );
									if ( min_qty > max_qty ) {
										jQuery( $( this ).find( '[name="fees[ap_fees_ap_product_variation_max_qty][]"]' ) ).css( 'border', '1px solid ' + validation_color_code );
										max_qty_flag = false;
									} else {
										jQuery( $( this ).find( '[name="fees[ap_fees_ap_product_variation_max_qty][]"]' ) ).css( 'border', '' );
									}
								}
							}

							if ( product_id_count == 0 && min_qty == '' ) {
								submit_prd_subtotal_flag = false;
							} else if ( product_id_count == 0 ) {
								submit_prd_subtotal_flag = false;
							} else if ( min_qty == '' ) {
								submit_prd_subtotal_flag = false;
							} else if ( max_qty_flag == false ) {
								submit_prd_subtotal_flag = false;
								displayMsg( 'message_prd_subtotal', coditional_vars.min_max_subtotal_error );
							} else {
								submit_prd_subtotal_flag = true;
							}

							prd_subtotal_val_arr[ tr_int_id ] = submit_prd_subtotal_flag;

						} );

						if ( prd_subtotal_val_arr != '' ) {
							var current_tab_id = jQuery( $( '#tbl_ap_product_variation_method tr.ap_product_variation_row_tr' ).parent().parent().parent().parent() ).attr( 'id' );
							if ( jQuery.inArray( false, prd_subtotal_val_arr ) !== - 1 ) {
								submit_prd_subtotal_form_flag = false;
								changeColorValidation( current_tab_id, false, validation_color_code );
							} else {
								submit_prd_subtotal_form_flag = true;
								changeColorValidation( current_tab_id, true, default_color_code );
							}
						}
					}
					/** End loop each row of AP Product rules */

					/** Start loop each row of AP Category rules */
					if ( $( '#tbl_ap_category_method tr.ap_category_row_tr' ).length ) {
						$( '#tbl_ap_category_method tr.ap_category_row_tr' ).each( function( index, item ) {
							//initialize variables
							var category_id_count = '';
							var min_qty = '',
								max_qty = '';
							var cat_tr_id = jQuery( this ).attr( 'id' );
							var cat_tr_int_id = cat_tr_id.substr( cat_tr_id.lastIndexOf( '_' ) + 1 );
							var max_qty_flag = true;

							/** check product empty or not */
							if ( $( this ).find( '[name="fees[ap_category_fees_conditions_condition][' + cat_tr_int_id + '][]"]' ).length ) {
								category_id_count = jQuery( this ).find( '[name="fees[ap_category_fees_conditions_condition][' + cat_tr_int_id + '][]"]' ).find( 'option:selected' ).length;
								if ( category_id_count == 0 ) {
									jQuery( $( this ).find( '.select2-container .selection .select2-selection' ) ).css( 'border', '1px solid ' + validation_color_code );
								} else {
									jQuery( $( this ).find( '.select2-container .selection .select2-selection' ) ).css( 'border', '' );
								}
							}
							/** check if min quantity empty or not */
							if ( $( this ).find( '[name="fees[ap_fees_ap_cat_min_qty][]"]' ).length ) {
								min_qty = $( this ).find( '[name="fees[ap_fees_ap_cat_min_qty][]"]' ).val();
								if ( min_qty == '' ) {
									jQuery( $( this ).find( '[name="fees[ap_fees_ap_cat_min_qty][]"]' ) ).css( 'border', '1px solid ' + validation_color_code );
								} else {
									jQuery( $( this ).find( '[name="fees[ap_fees_ap_cat_min_qty][]"]' ) ).css( 'border', '' );
								}
							}

							//check if max quantity empty or not
							if ( $( this ).find( '[name="fees[ap_fees_ap_cat_max_qty][]"]' ).length ) {
								max_qty = $( this ).find( '[name="fees[ap_fees_ap_cat_max_qty][]"]' ).val();
								if ( max_qty != '' && min_qty != '' ) {
									max_qty = parseInt( max_qty );
									if ( min_qty > max_qty ) {
										jQuery( $( this ).find( '[name="fees[ap_fees_ap_cat_max_qty][]"]' ) ).css( 'border', '1px solid ' + validation_color_code );
										max_qty_flag = false;
									} else {
										jQuery( $( this ).find( '[name="fees[ap_fees_ap_cat_max_qty][]"]' ) ).css( 'border', '' );
									}
								}
							}

							if ( category_id_count == 0 && min_qty == '' ) {
								submit_cat_flag = false;
							} else if ( category_id_count == 0 ) {
								submit_cat_flag = false;
							} else if ( min_qty == '' ) {
								submit_cat_flag = false;
							} else if ( max_qty_flag == false ) {
								submit_cat_flag = false;
								displayMsg( 'message_cat_qty', coditional_vars.min_max_weight_error );
							} else {
								submit_cat_flag = true;
							}

							cat_val_arr[ cat_tr_int_id ] = submit_cat_flag;

						} );

						if ( cat_val_arr != '' ) {
							var current_tab_id = jQuery( $( '#tbl_ap_category_method tr.ap_category_row_tr' ).parent().parent().parent().parent() ).attr( 'id' );
							if ( jQuery.inArray( false, cat_val_arr ) !== - 1 ) {
								submit_cat_form_flag = false;
								changeColorValidation( current_tab_id, false, validation_color_code );
							} else {
								submit_cat_form_flag = true;
								changeColorValidation( current_tab_id, true, default_color_code );
							}
						}
					}

					if ( $( '#tbl_ap_country_method tr.ap_country_row_tr' ).length ) {
						$( '#tbl_ap_country_method tr.ap_country_row_tr' ).each( function( index, item ) {
							//initialize variables
							var category_id_count = '';
							var min_qty = '',
								max_qty = '';
							var cat_tr_id = jQuery( this ).attr( 'id' );
							var cat_tr_int_id = cat_tr_id.substr( cat_tr_id.lastIndexOf( '_' ) + 1 );
							var max_qty_flag = true;

							//check product empty or not
							if ( $( this ).find( '[name="fees[ap_country_fees_conditions_condition][' + cat_tr_int_id + '][]"]' ).length ) {
								category_id_count = jQuery( this ).find( '[name="fees[ap_country_fees_conditions_condition][' + cat_tr_int_id + '][]"]' ).find( 'option:selected' ).length;
								if ( category_id_count == 0 ) {
									jQuery( $( this ).find( '.select2-container .selection .select2-selection' ) ).css( 'border', '1px solid ' + validation_color_code );
								} else {
									jQuery( $( this ).find( '.select2-container .selection .select2-selection' ) ).css( 'border', '' );
								}
							}
							//check if min quantity empty or not
							if ( $( this ).find( '[name="fees[ap_fees_ap_country_min_subtotal][]"]' ).length ) {
								min_qty = $( this ).find( '[name="fees[ap_fees_ap_country_min_subtotal][]"]' ).val();
								if ( min_qty == '' ) {
									jQuery( $( this ).find( '[name="fees[ap_fees_ap_country_min_subtotal][]"]' ) ).css( 'border', '1px solid ' + validation_color_code );
								} else {
									jQuery( $( this ).find( '[name="fees[ap_fees_ap_country_min_subtotal][]"]' ) ).css( 'border', '' );
								}
							}

							//check if max quantity empty or not
							if ( $( this ).find( '[name="fees[ap_fees_ap_country_max_subtotal][]"]' ).length ) {
								max_qty = $( this ).find( '[name="fees[ap_fees_ap_country_max_subtotal][]"]' ).val();
								if ( max_qty != '' && min_qty != '' ) {
									max_qty = parseInt( max_qty );
									if ( min_qty > max_qty ) {
										jQuery( $( this ).find( '[name="fees[ap_fees_ap_country_max_subtotal][]"]' ) ).css( 'border', '1px solid ' + validation_color_code );
										max_qty_flag = false;
									} else {
										jQuery( $( this ).find( '[name="fees[ap_fees_ap_country_max_subtotal][]"]' ) ).css( 'border', '' );
									}
								}
							}

							if ( category_id_count == 0 && min_qty == '' ) {
								submit_cat_subtotal_flag = false;
							} else if ( category_id_count == 0 ) {
								submit_cat_subtotal_flag = false;
							} else if ( min_qty == '' ) {
								submit_cat_subtotal_flag = false;
							} else if ( max_qty_flag == false ) {
								submit_cat_subtotal_flag = false;
								displayMsg( 'message_cat_qty', coditional_vars.min_max_country_error );
							} else {
								submit_cat_subtotal_flag = true;
							}
							cat_subtotal_val_arr[ cat_tr_int_id ] = submit_cat_subtotal_flag;
						} );

						if ( cat_subtotal_val_arr != '' ) {
							var current_tab_id = jQuery( $( '#tbl_ap_country_method tr.ap_country_row_tr' ).parent().parent().parent().parent() ).attr( 'id' );
							if ( jQuery.inArray( false, cat_subtotal_val_arr ) !== - 1 ) {
								submit_cat_subtotal_form_flag = false;
								changeColorValidation( current_tab_id, false, validation_color_code );
							} else {
								submit_cat_subtotal_form_flag = true;
								changeColorValidation( current_tab_id, true, default_color_code );
							}
						}
					}

					/** End loop each row of AP Product rules */

					/** f error in validation than prevent form submit. */

					if (
						submit_prd_form_flag == false
						|| submit_prd_subtotal_form_flag == false
						|| submit_cat_form_flag == false
						|| submit_cat_subtotal_form_flag == false ) {//if validate error found
						fees_pricing_rules_validation = false;
					} else {
						if ( count_total_tr > 0 ) {
							fees_pricing_rules_validation = true;
						} else {
							fees_pricing_rules_validation = false;
						}
					}
				}
			}
			if ( fees_pricing_rules_validation == false ) {
				if ( $( '#warning_msg_5' ).length <= 0 ) {
					var div = document.createElement( 'div' );
					div = setAllAttributes( div, {
						'class': 'warning_msg',
						'id': 'warning_msg_5'
					} );
					div.textContent = coditional_vars.warning_msg5;
					$( div ).insertBefore( '.mmqw-section-left .mmqw-main-table' );
				}
				if ( $( '#warning_msg_5' ).length ) {
					$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
					setTimeout( function() {
						$( '#warning_msg_5' ).remove();
					}, 7000 );
				}
				e.preventDefault();
				return false;
			} else {
				if ( jQuery( '.mmqw-condition-rules .advance-country-method-table' ).is( ':hidden' ) ) {
					jQuery( '.mmqw-condition-rules .advance-country-method-table tr td input' ).each( function() {
						$( this ).removeAttr( 'required' );
					} );
				}
				return true;
			}
		}

		/**
		 * Change the color of tab if there are any errors found during the validation
		 *
		 * @param current_tab
		 * @param required
		 * @param validation_color_code
		 */
		function changeColorValidation( current_tab, required, validation_color_code ) {
			if ( required == false ) {
				jQuery( '.pricing_rules_tab ul li[data-tab=' + current_tab + ']' ).css( 'border-top-color', validation_color_code );
				jQuery( '.pricing_rules_tab ul li[data-tab=' + current_tab + ']' ).css( 'box-shadow', 'inset 0 3px 0 ' + validation_color_code );
			} else {
				jQuery( '.pricing_rules_tab ul li[data-tab=' + current_tab + ']' ).css( 'border-top-color', '' );
				jQuery( '.pricing_rules_tab ul li[data-tab=' + current_tab + ']' ).css( 'box-shadow', '' );
			}

		}

		/**
		 * Display warning based on error types
		 *
		 * @param msg_id
		 * @param msg_content
		 */
		function displayMsg( msg_id, msg_content ) {
			if ( $( '#' + msg_id ).length <= 0 ) {
				var msg_div = document.createElement( 'div' );
				msg_div = setAllAttributes( msg_div, {
					'class': 'warning_msg',
					'id': msg_id
				} );

				msg_div.textContent = msg_content;
				$( msg_div ).insertBefore( '.mmqw-section-left .mmqw-main-table' );

				$( 'html, body' ).animate( { scrollTop: 0 }, 'slow' );
				setTimeout( function() {
					$( '#' + msg_id ).remove();
				}, 7000 );
			}
		}

		/**
		 * Call the inout number type validation
		 */
		numberValidateForAdvanceRules();

		/**
		 * Check the inout number type validation
		 */
		function numberValidateForAdvanceRules() {
			$( '.number-field' ).keypress( function( e ) {
				var regex = new RegExp( '^[0-9-%.]+$' );
				var str = String.fromCharCode( ! e.charCode ? e.which : e.charCode );
				if ( regex.test( str ) ) {
					return true;
				}
				e.preventDefault();
				return false;
			} );
			$( '.qty-class' ).keypress( function( e ) {
				var regex = new RegExp( '^[0-9]+$' );
				var str = String.fromCharCode( ! e.charCode ? e.which : e.charCode );
				if ( regex.test( str ) ) {
					return true;
				}
				e.preventDefault();
				return false;
			} );
		}

		/**
		 * Call product list based on product search
		 */
		getProductListBasedOnThreeCharAfterUpdate();

		/**
		 * Return product list based on product search
		 */
		function getProductListBasedOnThreeCharAfterUpdate() {
			$( '.pricing_rules .ap_product, ' +
				'.pricing_rules .ap_product_variation' ).each( function() {
				var select_name = $( this ).attr( 'id' );
				if ( $( this ).hasClass( 'ap_product_variation' ) ) {
					var with_variable = 'true';
				} else {
					var with_variable = 'false';
				}
				$( '#' + select_name ).select2( {
					ajax: {
						url: coditional_vars.ajaxurl,
						dataType: 'json',
						delay: 250,
						data: function( params ) {
							return {
								value: params.term,
								action: 'mmqw_simple_and_variation_product_list_ajax',
								with_variable: with_variable
							};
						},
						processResults: function( data ) {
							var options = [];
							if ( data ) {
								$.each( data, function( index, text ) {
									options.push( { id: text[ 0 ], text: allowSpeicalCharacter( text[ 1 ] ) } );
								} );

							}
							return {
								results: options
							};
						},
						cache: true
					},
					minimumInputLength: 3
				} );
			} );
		}

		/**
		 * Call variable product list based on product search
		 */
		varproductFilter();

		/**
		 * Return variable product list based on product search
		 */
		function varproductFilter() {
			$( '.product_fees_conditions_values_var_product' ).each( function() {
				var select_name = $( this ).attr( 'id' );
				$( '#' + select_name ).select2( {
					ajax: {
						url: coditional_vars.ajaxurl,
						dataType: 'json',
						delay: 250,
						data: function( params ) {
							return {
								value: params.term,
								action: 'mmqw_product_fees_conditions_variable_values_product_ajax'
							};
						},
						processResults: function( data ) {
							var options = [];
							if ( data ) {
								$.each( data, function( index, text ) {
									options.push( { id: text[ 0 ], text: allowSpeicalCharacter( text[ 1 ] ) } );
								} );

							}
							return {
								results: options
							};
						},
						cache: true
					},
					minimumInputLength: 3
				} );
			} );
		}

		/**
		 * Remove tr on delete icon click
		 */
		$( 'body' ).on( 'click', '.delete-row', function() {
			$( this ).parent().parent().remove();
		} );

		/**
		 * Set all the attributes
		 *
		 * @param element
		 * @param attributes
		 * @returns {*}
		 */
		function setAllAttributes( element, attributes ) {
			Object.keys( attributes ).forEach( function( key ) {
				element.setAttribute( key, attributes[ key ] );
				// use val
			} );
			return element;
		}

		/**
		 * Replace the special character code to symbol
		 *
		 * @param str
		 * @returns {string}
		 */
		function allowSpeicalCharacter( str ) {
			return str.replace( '&#8211;', '–' ).replace( '&gt;', '>' ).replace( '&lt;', '<' ).replace( '&#197;', 'Å' );
		}

		/**
		 * Checked un checked condition
		 */
		$( 'body' ).on( 'click', '.condition-check-all', function() {
			$( 'input.multiple_delete_fee:checkbox' ).not( this ).prop( 'checked', this.checked );
		} );

		$( 'body' ).on( 'click', 'input.multiple_delete_fee', function() {
			var total_checkbox = $( '.multiple_delete_fee:checkbox' ).length;
			var activated_checkbox = $( '.multiple_delete_fee:checkbox:checked' ).length;
			if(total_checkbox == activated_checkbox) {
				$( '.condition-check-all' ).prop( 'checked', true );
			} else {
				$( '.condition-check-all' ).prop( 'checked', false );
			}
		} );



		/**
		 * Delete selected rules
		 */
		$( '#delete-shipping-method' ).click( function() {
			if ( 0 == $( '.multiple_delete_fee:checkbox:checked' ).length ) {
				alert( 'Please select at least one rules to delete' );
				return false;
			}
			if ( confirm( 'Are You Sure You Want to Delete?' ) ) {
				var allVals = [];
				$( '.multiple_delete_fee:checked' ).each( function() {
					allVals.push( $( this ).val() );
				} );
				$.ajax( {
					type: 'GET',
					url: coditional_vars.ajaxurl,
					data: {
						'action': 'mmqw_wc_multiple_delete_shipping_method',
						'nonce': coditional_vars.dsm_ajax_nonce,
						'allVals': allVals
					},
					success: function( response ) {
						if ( 1 == response ) {
							alert( 'Selected rules deleted Successfully' );
							$( '.multiple_delete_fee' ).prop( 'checked', false );
							location.reload();
						}
					}
				} );
			}
		} );

		/**
		 * Save the order of the rules
		 */
		saveAllIdOrderWise( 'on_load' );

		/**
		 * Start code for save all method as per sequence in list
		 *
		 */
		function saveAllIdOrderWise( position ) {
			var smOrderArray = [];

			$( 'table#shipping-methods-listing tbody tr' ).each( function() {
				smOrderArray.push( this.id );
			} );
			$.ajax( {
				type: 'GET',
				url: coditional_vars.ajaxurl,
				data: {
					'action': 'mmqw_sm_sort_order',
					'smOrderArray': smOrderArray
				},
				success: function( response ) {
					if ( 'on_click' === jQuery.trim( position ) ) {
						alert( coditional_vars.success_msg1 );
					}
				}
			} );
		}

		/**
		 * End code for save all method as per sequence in list
		 *
		 */
		$( '.tablesorter' ).tablesorter( {
			headers: {
				0: {
					sorter: false
				},
				4: {
					sorter: false
				}
			}
		} );

		var fixHelperModified = function( e, tr ) {
			var $originals = tr.children();
			var $helper = tr.clone();
			$helper.children().each( function( index ) {
				$( this ).width( $originals.eq( index ).width() );
			} );
			return $helper;
		};

		/** Make diagnosis table sortable */
		/*$( 'table#shipping-methods-listing tbody' ).sortable( {
			helper: fixHelperModified
		} );*/

		$( 'table#shipping-methods-listing tbody' ).disableSelection();

		$( document ).on( 'click', '.shipping-methods-order', function() {
			saveAllIdOrderWise( 'on_click' );
		} );

		/** Add AP Category functionality end here */

		/**
		 * Clone the rules login here
		 */
		$( document ).on( 'click', '#mmqw_clone_rule', function() {
			var current_shipping_id = $( this ).attr( 'data-attr' );
			$.ajax( {
				type: 'GET',
				url: coditional_vars.ajaxurl,
				data: {
					'action': 'mmqw_clone_rule',
					'current_shipping_id': current_shipping_id
				}, beforeSend: function() {
					var div = document.createElement( 'div' );
					div = setAllAttributes( div, {
						'class': 'loader-overlay',
					} );

					var img = document.createElement( 'img' );
					img = setAllAttributes( img, {
						'id': 'before_ajax_id',
						'src': coditional_vars.ajax_icon
					} );

					div.appendChild( img );
					jQuery( '#shipping-methods-listing' ).after( div );
				}, complete: function() {
					jQuery( '.mmqw-main-table img#before_ajax_id' ).remove();
				},
				success: function( response ) {
					var response_data = JSON.parse( response );
					if ( 'true' === jQuery.trim( response_data[ '0' ] ) ) {
						location.href = response_data[ '1' ];
					}
				}
			} );
		} );

		/**
		 * Start: Change shipping status form list section
		 * */
		$( document ).on( 'click', '#shipping_status_id', function() {
			var current_shipping_id = $( this ).attr( 'data-smid' );
			var current_value = $( this ).prop( 'checked' );
			$.ajax( {
				type: 'GET',
				url: coditional_vars.ajaxurl,
				data: {
					'action': 'mmqw_change_status_from_list_section',
					'current_shipping_id': current_shipping_id,
					'current_value': current_value
				}, beforeSend: function() {
					var div = document.createElement( 'div' );
					div = setAllAttributes( div, {
						'class': 'loader-overlay',
					} );

					var img = document.createElement( 'img' );
					img = setAllAttributes( img, {
						'id': 'before_ajax_id',
						'src': coditional_vars.ajax_icon
					} );

					div.appendChild( img );
					jQuery( '#shipping-methods-listing' ).after( div );
				}, complete: function() {
					jQuery( '.mmqw-main-table .loader-overlay' ).remove();
				}, success: function( response ) {
					alert( jQuery.trim( response ) );
				}
			} );
		} );

		/** Hide and show pricing rules based on status */
		hideShowPricingRulesBasedOnPricingRuleStatus();

		/** Hide and show pricing rules based on runtime status */
		function hideShowPricingRulesBasedOnPricingRuleStatus() {
			jQuery( '.multiselect2' ).select2();
			if ( true === $( 'input[name="ap_rule_status"]' ).prop( 'checked' ) ) {
				jQuery( '.multiselect2' ).select2();
				jQuery( '.pricing_rules' ).css( 'display', 'inline-block' );
			} else if ( false === $( 'input[name="ap_rule_status"]' ).prop( 'checked' ) ) {
				jQuery( '.pricing_rules' ).css( 'display', 'none' );
			}
		}

		/** Hide and show pricing rules based on click on status*/
		$( 'body' ).on( 'click', 'input[name="ap_rule_status"]', function() {
			if ( true === $( this ).prop( 'checked' ) ) {
				jQuery( '.pricing_rules' ).css( 'display', 'inline-block' );
				jQuery( '.multiselect2' ).select2();
			} else if ( false === $( this ).prop( 'checked' ) ) {
				jQuery( '.pricing_rules' ).css( 'display', 'none' );
			}
		} );
		/** End: hide show pricing rules status*/

	} );

	/**
	 * ON load initialize the variables
	 */
	jQuery( window ).on( 'load', function() {
		/**
		 * Replace special characters code the symbol
		 *
		 * @param str
		 * @returns {string}
		 */
		function allowSpeicalCharacter( str ) {
			return str.replace( '&#8211;', '–' ).replace( '&gt;', '>' ).replace( '&lt;', '<' ).replace( '&#197;', 'Å' );
		}

		/**
		 * Product search ajax
		 */
		jQuery( '.pricing_rules .ap_product, ' +
			'.pricing_rules .ap_product_weight, ' +
			'.pricing_rules .ap_product_variation' ).each( function() {
			var select_name = jQuery( this ).attr( 'id' );

			if ( $( this ).hasClass( 'ap_product_variation' ) ) {
				var with_variable = 'true';
			} else {
				var with_variable = 'false';
			}
			jQuery( '#' + select_name ).select2( {
				ajax: {
					url: coditional_vars.ajaxurl,
					dataType: 'json',
					delay: 250,
					data: function( params ) {
						return {
							value: params.term,
							action: 'mmqw_simple_and_variation_product_list_ajax',
							with_variable: with_variable
						};
					},
					processResults: function( data ) {
						var options = [];
						if ( data ) {
							jQuery.each( data, function( index, text ) {
								options.push( { id: text[ 0 ], text: allowSpeicalCharacter( text[ 1 ] ) } );
							} );

						}
						return {
							results: options
						};
					},
					cache: true
				},
				minimumInputLength: 3
			} );
		} );
	} );
})( jQuery );

/**
 * On document ready
 */
jQuery( document ).ready( function() {
	if ( jQuery( window ).width() <= 980 ) {
		jQuery( '.mmqw-condition-rules .pricing_rules .pricing_rules_tab_content .tab-content' ).click( function() {
			var acc_id = jQuery( this ).attr( 'id' );
			jQuery( '.mmqw-condition-rules .pricing_rules .pricing_rules_tab_content .tab-content' ).removeClass( 'current' );
			jQuery( '#' + acc_id ).addClass( 'current' );
		} );
	}
} );

/**
 * On resize
 */
jQuery( window ).resize( function() {
	if ( jQuery( window ).width() <= 980 ) {
		jQuery( '.mmqw-condition-rules .pricing_rules .pricing_rules_tab_content .tab-content' ).click( function() {
			var acc_id = jQuery( this ).attr( 'id' );
			jQuery( '.mmqw-condition-rules .pricing_rules .pricing_rules_tab_content .tab-content' ).removeClass( 'current' );
			jQuery( '#' + acc_id ).addClass( 'current' );
		} );
	}
} );

/**
 * MMQW JS
 */
jQuery( window ).on( 'load', function() {
	setTimeout( function() {
		jQuery( '.ms-msg' ).fadeOut( 300 );
	}, 2000 );
} );