var DataTable;
var minDateFilter;
var maxDateFilter;
var selectedStatus;
/*
	clear time filtering and show all rows
*/
function toeClearTimeFilteringCsp(){
	minDateFilter = undefined;
	maxDateFilter = undefined;
	jQuery('#orderDateFrom').val("");
	jQuery('#orderDateTo').val("");
    DataTable.fnDraw();
}

jQuery(document).ready(function(){
	// @deprecated
	/*var timePickerOpt = {
		timeFormat:"HH:mm:ss",
		dateFormat:"mm/dd/yy"
	};*/
	
	jQuery( "#orderDateFrom" ).datetimepicker( {
		timeFormat:"HH:mm:ss",
		dateFormat:"mm/dd/yy",
		onSelect: function(date) {
			minDateFilter = new Date(date).getTime();
			jQuery('#orderDateTo').datepicker("option", 'minDate', new Date(date));
			DataTable.fnDraw();
			if(!jQuery('.toeClearTimefiltering').is(':visible')){
				jQuery('.toeClearTimefiltering').show();
			}
		}
	} ).keyup( function () {
		  minDateFilter = new Date(this.value).getTime();
		  DataTable.fnDraw();
	} );

	jQuery( "#orderDateTo" ).datetimepicker( {
		timeFormat:"HH:mm:ss",
		dateFormat:"mm/dd/yy",
		onSelect: function(date) {
			maxDateFilter = new Date(date).getTime();
			DataTable.fnDraw();
		}
	} ).keyup( function () {
			maxDateFilter = new Date(this.value).getTime();
			DataTable.fnDraw();
	} );

	/*
	Add custom type for currency sorting	
	*/
	jQuery.fn.dataTableExt.aTypes.unshift(
		function ( data){
			if ( data.search("$") != -1 ) {
				return 'currency';
			} else {
				return "string";
			}
		}
	);
	/*
	add methods to sorting currency
	*/
	jQuery.extend( jQuery.fn.dataTableExt.oSort, {
		"currency-pre": function ( a ) {
			a = (a==="-") ? 0 : a.replace( /[^\d\-\.]/g, "" );
			return parseFloat( a );
		},
	 
		"currency-asc": function ( a, b ) {
			return a - b;
		},
	 
		"currency-desc": function ( a, b ) {
			return b - a;
		}
	} );
/*
methods for string sorting
*/
	jQuery.fn.dataTableExt.oSort['string-case-asc']  = function(x,y) {
		return ((x < y) ? -1 : ((x > y) ?  1 : 0));
	};
	 
	jQuery.fn.dataTableExt.oSort['string-case-desc'] = function(x,y) {
		return ((x < y) ?  1 : ((x > y) ? -1 : 0));
	};
 
	jQuery.fn.dataTableExt.afnFiltering.push(function( oSettings, aData, iDataIndex ) {
		if ( typeof aData._date == 'undefined' ) {
			aData._date = new Date(aData[3]).getTime();
		}

		if ( minDateFilter && !isNaN(minDateFilter) ) {
			if ( aData._date < minDateFilter ) {
				return false;
			}
		}
		if ( maxDateFilter && !isNaN(maxDateFilter) ) {
			if ( aData._date > maxDateFilter ) {
				return false;
			}
		}
		return true;
	});

	jQuery.fn.dataTableExt.afnFiltering.push(
		function( oSettings, aData, iDataIndex ) {
			if(selectedStatus == undefined){
				selectedStatus = 'all';
			}
			var status = jQuery.trim(aData[2]);
			if(selectedStatus == 'all'){
				return true;
			}
			if(status == selectedStatus){
				return true;
			}
			return false;
		}
	);
	var default_options = {	
		oLanguage: {
			sLengthMenu: "Display _MENU_ Orders In Page",
			sSearch: "Search:",
			sZeroRecords: "Not found",
			sInfo: "Show  _START_ to _END_ from _TOTAL_ records",
			sInfoEmpty: "show 0 to 0 from 0 records",
			sInfoFiltered: "(filtered from _MAX_ total records)"

		},
		aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
		bProcessing: true ,
		bPaginate: true,
		aaSorting: [[ 0, "desc" ]],
		aoColumns:[
			 {'sType':'numeric'},
			 {'sType':'currency'},
			 {'sType':'string'},
			 {'sType':'date'},
			 {'sType':'string'},
			 {'sType':'string'}
		]
	};
	DataTable = jQuery(".toeOrderListTableCsp").dataTable(default_options)
	jQuery(".toeDropDownStatusesListCsp").mouseover(function(){
		jQuery('.toeDropdownMenu').show();
	});
	jQuery('.toeDropdownMenu').mouseleave(function(){
		jQuery(this).hide();
	});
	jQuery('#orderDateTo').keyup(function(){

	});
	jQuery('.toeClearTimefiltering').click(function(){
		toeClearTimeFilteringCsp();
	});
	jQuery('#toeOrderStatusesListCsp a').click(function(){
		jQuery('#toeOrderStatusesListCsp a').removeClass('selectedMenuItem');
		jQuery(this).addClass('selectedMenuItem');
		selectedStatus = jQuery(this).attr('data_sort').replace('toe_',"");
		jQuery('.toeCurrentOrderStatusCsp').html(jQuery(this).text());
		DataTable.fnDraw();
	});
});