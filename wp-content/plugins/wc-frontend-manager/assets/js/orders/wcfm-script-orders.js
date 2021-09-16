$wcfm_orders_table = '';
$order_status = '';	
$filter_by_date = '';
$order_product = '';
$commission_status = '';
$order_vendor = '';
$delivery_boy = '';
var orderTableRefrsherTime = '';


jQuery(document).ready(function($) {
	var dateNow = new Date();
				
	 $('input[name="daterange"]').daterangepicker({
			    opens: 'left',
			    autoApply:true,
			    timePicker: true,
			    autoUpdateInput: false,
			    timePicker24Hour: false,
			    startDate: moment(dateNow).hours(0).minutes(0).seconds(0).milliseconds(0) ,
    			endDate: moment(dateNow).add(1, 'days').hours(23).minutes(59).seconds(1).milliseconds(0),
			   // defaultDate:moment(dateNow).hours(0).minutes(0).seconds(0).milliseconds(0) ,
			    locale: {
			      format: 'YYYY-MM-DD hh:mm a',
			      cancelLabel: 'Clear', 
			    },
			     ranges: {
	           'Today': [moment(dateNow).hours(0).minutes(0), moment(dateNow).hours(23).minutes(59)],
	           'Tomorrow': [moment(dateNow).add(1, 'days').hours(0).minutes(0), moment(dateNow).add(1, 'days').hours(23).minutes(59)],
	           'Yesterday': [moment(dateNow).subtract(1, 'days').hours(0).minutes(0), moment(dateNow).subtract(1, 'days').hours(23).minutes(59)],
	           'Last 7 Days': [moment(dateNow).subtract(6, 'days').hours(0).minutes(0), moment(dateNow).hours(23).minutes(59)],
	           'Last 30 Days': [moment(dateNow).subtract(29, 'days').hours(0).minutes(0), moment(dateNow).hours(23).minutes(59)],
	           'This Month': [moment(dateNow).startOf('month').hours(0).minutes(0), moment(dateNow).endOf('month').hours(23).minutes(59)],
	           'Last Month': [moment(dateNow).subtract(1, 'month').startOf('month').hours(0).minutes(0), moment(dateNow).subtract(1, 'month').endOf('month').hours(23).minutes(59)],
	           'Next 24 hours': [moment(), moment().add(1, 'days')]
	          	}
			  }, function(start, end, label) {
	  });
	  $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
		  //do something, like clearing an input
		  $('#daterange').val('');
		  $wcfm_orders_table.ajax.reload();
		});
		$('#daterange').on('apply.daterangepicker', function(ev, picker) {
	      $(this).val(picker.startDate.format('YYYY-MM-DD hh:mm a') + ' to ' + picker.endDate.format('YYYY-MM-DD hh:mm a'));
	      $wcfm_orders_table.ajax.reload();
	     });

		
	$order_vendor = GetURLParameter( 'order_vendor' );
		
	// Dummy Mark Complete Dummy
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_order_mark_complete_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( wcfm_dashboard_messages.wcfmu_upgrade_notice );
				return false;
			});
		});
	});
	
	// Invoice Dummy
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_pdf_invoice_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( wcfm_dashboard_messages.pdf_invoice_upgrade_notice );
				return false;
			});
		});
	});
	
	// Invoice dummy - vendor
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_pdf_invoice_vendor_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( wcfm_dashboard_messages.wcfmu_missing_feature );
				return false;
			});
		});
	});
	
	// Mark Shipped dummy - vendor
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_wcvendors_order_mark_shipped_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( wcfm_dashboard_messages.wcfmu_missing_feature );
				return false;
			});
		});
	});
	
	if( dataTables_config.is_allow_hidden_export ) {
		$wcfm_datatable_button_args = [
																		{
																			extend: 'print',
																		},
																		{
																			extend: 'pdfHtml5',
																			orientation: 'landscape',
																			pageSize: 'LEGAL'
																		},
																		{
																			extend: 'excelHtml5',
																		}, 
																		{
																			extend: 'csv',
																		}
																	];
	}
	function bookingDate(){
		var textString = '';
		var filter_date_form_text = "";
		if($filter_date_form!=""){
			filter_date_form_text+=" from "+$filter_date_form;
		}
		if($filter_date_to!=""){
			filter_date_form_text+=" to "+$filter_date_to;
		}
		if(filter_date_form_text!=""){
			textString+="<li><strong>booked date:</strong> "+filter_date_form_text+"</li>";
		}
		return textString;
	}
	function deliveryDate(){
		var textString = '';
		var delivery = $('#daterange').val();
		if(delivery!=""){
			var deliveryArray = delivery.split(" to ");
			var dateFrom = new Date(deliveryArray[0]).toDateString();
			var timeFrom = deliveryArray[0].trim().replace(" ", ",").split(",")[1];
			var dateTo = new Date(deliveryArray[1]).toDateString();
			var timeTo = deliveryArray[1].trim().replace(" ", ",").split(",")[1];
			textString+="<li><strong>Delivery time:</strong> "+dateFrom+" "+timeFrom+" <i class='to-seperator-delivery'>to</i> "+dateTo+" "+timeTo+" </li>";
		}
		return textString;
	}
	function searchingFromResult(){
		var textString = '';

		textString+= bookingDate();
		if($order_vendor!==undefined && $order_vendor !==null){
			textString+="<li><strong>Vendor:</strong> "+$order_vendor+"</li>";
		}
		if($delivery_boy!=""){
			textString+="<li><strong>Delivery boy:</strong> "+$delivery_boy+"</li>";
		}
		if($order_product!=""){
			textString+="<li><strong>Order product:</strong> "+$order_product+"</li>";
		}
		if($commission_status!== ""){
			textString+="<li><strong>Commission status:</strong> "+$commission_status+"</li>";	
		}
		if(GetURLParameter( 'order_status' )!== undefined){
			textString+="<li><strong>Order status:</strong> "+GetURLParameter( 'order_status' )+"</li>";	
		}
		searchInput = $('#wcfm-orders_filter input').val();
		if(searchInput!=""){
			textString+="<li><strong>Search:</strong> "+searchInput+"</li>";
		}

		textString+= deliveryDate();

		if(textString!=""){
			$('#data_filter_applied').html('<ul><li class="fa-filter-wrapper"><strong  class="fa fa-filter fa-1x"></strong></li>'+textString+"</ul>");
		}

	}
	
	$wcfm_orders_table = $('#wcfm-orders').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"bFilter"   : wcfm_datatable_columns.bFilter,
		"pageLength": parseInt(dataTables_config.pageLength),
		"dom"       : 'Bfrtip',
		"language"  : $.parseJSON(dataTables_language),
    "buttons"   : $wcfm_datatable_button_args,
		"columns"   : $.parseJSON(wcfm_datatable_columns.priority),
		"columnDefs": $.parseJSON(wcfm_datatable_columns.defs),
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {

				d.action            = 'wcfm_ajax_controller',
				d.controller        = 'wcfm-orders',
				d.order_status      = GetURLParameter( 'order_status' ),
				d.filter_date_form  = $filter_date_form,
				d.filter_date_to    = $filter_date_to,  
				d.order_product     = $order_product,  
				d.commission_status = $commission_status,
				d.order_vendor      = $order_vendor,
				d.delivery_boy      = $delivery_boy,
				d.delivery_date_range =  $('#daterange').val()//"2021-09-11 2:40:19 - 2021-09-14 11:23:19"
			},
			"beforeSend": function() {
				searchingFromResult();
				$('#wcfm-orders').block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
		    
		    },
			"complete" : function (data,status,xhr) {
				initiateTip();
				$('#wcfm-orders').unblock();

				$('.show_order_items').click(function(e) {
					e.preventDefault();
					$(this).next('div.order_items').toggleClass( "order_items_visible" );
					return false;
				});
				
				// Fire wcfm-orders table refresh complete
				$( document.body ).trigger( 'updated_wcfm-orders' );
			}
		}
	} );
	
	$( document.body ).on( 'wcfm-date-range-refreshed', function() {
		$wcfm_orders_table.ajax.reload();
	});
	
	// Product Filter
	if( $('#order_product').length > 0 ) {
		$('#order_product').on('change', function() {
		  $order_product = $('#order_product').val();
		  $wcfm_orders_table.ajax.reload();
		}).select2( $wcfm_product_select_args );
	}
	
	// Commission Status Filter
	if( $('#commission-status').length > 0 ) {
		$('#commission-status').on('change', function() {
			$commission_status = $('#commission-status').val();
			$wcfm_orders_table.ajax.reload();
		});
	}
	
	// Vendor Filter
	if( $('#dropdown_vendor').length > 0 ) {
		$('#dropdown_vendor').on('change', function() {
			$order_vendor = $('#dropdown_vendor').val();
			$wcfm_orders_table.ajax.reload();
		}).select2( $wcfm_vendor_select_args );
	}
	
	// Delivery Boy Filter
	if( $('#wcfm_delivery_boy').length > 0 ) {
		$('#wcfm_delivery_boy').on('change', function() {
			$delivery_boy = $('#wcfm_delivery_boy').val();
			$wcfm_orders_table.ajax.reload();
		});
	}
	
	// Order Table auto Refresher
	function orderTableRefrsher() {
		$wcfm_orders_table.ajax.reload();
	}
	orderTableRefrsher();
	
	// Mark Order as Completed
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_order_mark_complete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm( wcfm_dashboard_messages.order_mark_complete_confirm );
				if(rconfirm) markCompleteWCFMOrder($(this));
				return false;
			});
		});
	});
	
	function markCompleteWCFMOrder(item) {
		clearTimeout(orderTableRefrsherTime);
		$('#wcfm-orders_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'wcfm_order_mark_complete',
			orderid : item.data('orderid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$wcfm_orders_table.ajax.reload();
				$('#wcfm-orders_wrapper').unblock();
				orderTableRefrsher();
			}
		});
	}
	
	// Screen Manager
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$.each(wcfm_orders_screen_manage, function( column, column_val ) {
		  $wcfm_orders_table.column(column).visible( false );
		} );
	});
	
	// Hidden Column
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$.each(wcfm_orders_screen_manage_hidden, function( column, column_val ) {
		  $wcfm_orders_table.column(column).visible( false );
		} );
	});
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
		$('.wcfm_filters_wrap').css( 'display', 'inline-block' );
	}
	
} );