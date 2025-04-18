'use strict';

document.addEventListener('DOMContentLoaded', function() {

// Code to show notice messages with guternberge editor
if (typeof wp !== 'undefined') {
    ( function( wp ) {
        console.log(wp);
        
        const { subscribe, select, dispatch } = wp.data;
        const { isSavingPost, getCurrentPostId } = select( 'core/editor' );
        const { addQueryArgs } = wp.url;
        let checked = true;

        subscribe( () => {

            if( isSavingPost() ) {
                checked = false;
            } else {
                if ( ! checked ) {
                    checked = true;

                    // some variables
                    const postId = getCurrentPostId();
                    const path = addQueryArgs( '/wpw_auto_poster/v1/latest-error', { id: postId } );

                    wp.apiFetch({
                        path: path,
                    }).then( function( response ){

                        if( response.errors ){
                            response.errors.forEach(function(error,index) {
                                wp.data.dispatch('core/notices').createNotice(
                                    error.type, // Use 'warning' if you prefer
                                    error.message,
                                    {
                                        id: 'wpw_auto_poster_notice_'.index,
                                        isDismissible: true
                                    }
                                );
                            });
                        } else {
                            dispatch( 'core/notices' ).removeNotice( 'wpw_auto_poster_notice' );
                        }
                        jQuery(".interface-navigable-region").animate({ scrollTop: 0 }, "slow");
                    } );
                    
                }
            }
        } );
        
        } )( window.wp );
    }
});


document.addEventListener('DOMContentLoaded', function() {
    const dismissButton = document.querySelector('.notice-dismiss');
    
    if (dismissButton) {
        dismissButton.addEventListener('click', function() {
            
            this.closest('.notice-error').remove();
        });
    }
});

jQuery(document).ready(function ($) {


    var previousActiveTabIndex = 0;

    $(".tab-switcher").on('click keypress', function (event) {
        // event.which === 13 means the "Enter" key is pressed
        if ((event.type === "keypress" && event.which === 13) || event.type === "click") {
            
            var tabClicked = $(this).data("tab-index");

            if(tabClicked != previousActiveTabIndex) {
                if($(this).data("tab-index") == tabClicked) {
                    $(this).addClass('wpw-auto-poster-quick-active');
                }

                $("#allTabsContainer .tab-container").each(function () {

                    if($(this).data("tab-index") == tabClicked) {
                        
                        $(".tab-container").hide();
                        $(this).show();
                        previousActiveTabIndex = $(this).data("tab-index");
                        return;
                    }
                });
            }
        }
    });


    $(document).on("click", ".delete_quick_post", function () {

        var ans;
        ans = confirm(WpwAutoPosterSettings.deleteconfirmmsg);

        if (ans) {
            return true;
        } else {
            return false;
        }

    });

    $(document).on('click', '.wpw-auto-poster-quick-share-wrap a.nav-tab', function(){
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
    });

    $(document).on('keyup', 'textarea#wpw-qs-msg', function(){
        $('.wpw-qs-character span').text($(this).val().length);
    });

    $('.searchByGender').change(function(){
        var selected_val = $(this).val();

        if(selected_val == 'delete'){
            var id = [];
            $(':checkbox:checked').each(function (i) {
                id[i] = $(this).val();
            });
            if (id.length === 0) {
                alert("You must select at least one checkbox!");
            } else if (confirm("Are you sure you want to delete selected records?")) {
                $.ajax({
                    url: WpwAutoPosterSettings.ajaxurl,
                    method: 'POST',
                    data: {
                        'action':'wpw_auto_poster_quick_delete_multiple',
                        'id': id,
                        'action_remove':'delete',
                        'page':'wpw-auto-poster-posted-logs',
                        wpw_admin_setting_nonce: WpwAutoPosterAdmin.wpw_admin_setting_nonce,
                    },
                    success: function (result){
                        location.reload();
                    }
                });
            } else {
                return false;
            }
        }
    });     

    $("#wpw_auto_poster_reposter_set_submit").on('click', function (e) {
        
        var minimum_post_age = parseInt( $("#minimum_post_age").val() ) ;
        var maximum_post_age = parseInt( $("#maximum_post_age").val() ) ;

        if( maximum_post_age == 0 && minimum_post_age == 0 ) {
            $("#required_length_minimum").html("");
            $("#required_length").html("");
            return true;
        } 
        if( maximum_post_age > 0 && minimum_post_age == 0 ) {
            $("#required_length_minimum").html("");
            $("#required_length").html("");
            return true;
        }
        if( minimum_post_age > 0 && maximum_post_age == 0 ) {
            $("#required_length_minimum").html("");
            $("#required_length").html("");
            return true;
        }
        if( minimum_post_age > 0 && maximum_post_age == 0 ) {
            $("#required_length_minimum").html("");
            $("#required_length").html("");
            return true;
        }
        if( minimum_post_age > 0 && (maximum_post_age != 0 && maximum_post_age <= minimum_post_age) ) {
            $("#required_length_minimum").html("");
            $("#required_length").html("");
            $("#required_length").append("Maximum post age should not be less than Minimum post age");
            $("#required_length").css("color", "#ff0000");
            return false;
        }

        $("#required_length_minimum").html("");
        $("#required_length").html("");
        return true;
    });

    $(document).on('click', '.clear-date', function () {
        $('#_wpweb_select_hour').val('');
    });

    if( $('#_wpweb_select_hour').length ) {

        $('#_wpweb_select_hour').datetimepicker({
            dateFormat: WpwAutoPosterAdmin.date_format,
            minDate: new Date(WpwAutoPosterAdmin.current_date),
            timeFormat: WpwAutoPosterAdmin.time_format,
            showMinute: false,
            ampm: false,
            stepMinute: 60,
            showOn: 'focus',
            stepHour: 1,
            currentText: '',
        }).attr('readonly', 'readonly');
    }

    if( $('#wpw_auto_select_hour').length ) {
        $('#wpw_auto_select_hour').datetimepicker({
            dateFormat: WpwAutoPosterAdmin.date_format,
            minDate: new Date(WpwAutoPosterAdmin.current_date),
            timeFormat: WpwAutoPosterAdmin.time_format,
            showMinute: false,
            ampm: false,
            stepMinute: 60,
            stepHour: 1,
            currentText: 'Now',
            showOn: 'focus',
        });
    }

    if( $('#wpw-qs-schedule').length ) {
        $('#wpw-qs-schedule').datetimepicker({
            dateFormat: WpwAutoPosterAdmin.qs_date_format,
            minDate: new Date(WpwAutoPosterAdmin.qs_current_date),
            formatTime: WpwAutoPosterAdmin.qs_time_format,
            showMinute: true,
            ampm: true,
            stepMinute: 30,
            showOn: 'focus',
            stepHour: 1,
            defaultDate: null,
            setDate: null,
            onSelect: function(dateText) {
                if(dateText == ''){
                    $('.wpw-qs-reset-schedule').hide();
                    $('.wpw-qs-calander').show();
                } else{
                    $('.wpw-qs-reset-schedule').css('display','inline-block');
                    $('.wpw-qs-calander').hide();
                }
            }
           // currentText: '',
       }).attr('readonly', 'readonly');

       $(document).on('click', '.wpw-qs-calander', function(){
            $('#wpw-qs-schedule').datetimepicker('show');
       });
    }
    
    /*if( $('.wpw-qs-account-select').length ){
        $('.wpw-qs-account-select').select2({
            width       : '100%'
        });
    }*/

	if( $('.wpw-qs-account-select').length > 0 ) {
		$( '.wpw-qs-account-select' ).css('width','500px').chosen({search_contains:true, width: '100%'});
	}

    if( $('.wpw-qs-posting-type').length ){
        $('.wpw-qs-posting-type').select2({
            width       : '100%'
        });
    }

    $(document).on('click','.wpw-qs-reset-schedule', function(){
        $('#wpw-qs-schedule').datetimepicker('setDate', null);
        $(this).hide();
        $('.wpw-qs-calander').show();
    });

    if( $('.wpw-auto-poster-popup-content').length ) {
        $(document).on('click', '.schedule > a', function (event) {
            event.preventDefault();
            var scheduleurl = $(this).attr('href');
            $("input[name='schedule_url']").val(scheduleurl);

            $(".wpw-auto-poster-popup-content").show();
            $(".wpw-auto-poster-popup-overlay").show();
        });

        $(document).on('click', '.wpw-close-button', function (event) {
            $(".wpw-auto-poster-popup-content").hide();
            $(".wpw-auto-poster-popup-overlay").hide();
        });

        $(document).on('click', '.done', function (event) {
            var bulk_action = $('#bulk-action-selector-top').val();
            var select_hour = $("input[name='wpw_auto_select_hour']").val();

            if( bulk_action != '' && bulk_action == 'schedule' ) {

                $('<input />').attr('type', 'hidden')
                .attr('name', "bulk_select_hour")
                .attr('value', select_hour)
                .appendTo('#product-filter');

                $(".wpw-close-button").trigger("click");

            } else {

                var scheduleurl = $("input[name='schedule_url']").val();
                scheduleurl = scheduleurl + "&select_hour=" + select_hour;
                $(location).attr('href', scheduleurl);
            }
        });

        $(document).on('change', '#bulk-action-selector-top', function () {
         
            var action = $(this).val();
            if( action == 'schedule' ) {
                $(".wpw-auto-popup-content").show();
                $(".wpw-auto-popup-overlay").show();
            }
        });

    }

    $('.quickpost-select-all').click( function(e) {
      $(this).closest('table').find('th input:checkbox').prop('checked', this.checked);
      $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
  } );

	// initialize data table
	if( $('.wpw-auto-poster-datatable').length > 0 ) {
        // Add the necessary sorting plugins
        $.fn.dataTable.moment(WpwAutoPosterAdmin.$qs_format); // F j, Y g:i a
        $.fn.dataTable.moment('DD/MM/YYYY h:mm a');
        $.fn.dataTable.moment('DD/MM/YYYY hh:mm a');
        $.fn.dataTable.moment('DD/MM/YYYY HH:mm');
        $.fn.dataTable.moment('MMMM D, YYYY h:mm a');
        $.fn.dataTable.moment('MMMM D, YYYY hh:mm a');
        $.fn.dataTable.moment('YYYY-MM-DD h:mm a'); // For format like 2024-08-15 7:30 pm
        $.fn.dataTable.moment('YYYY-MM-DD hh:mm a'); // For format like 2024-08-15 09:30 am

		$( '.wpw-auto-poster-datatable' ).dataTable({
            "columnDefs": [
                { 'orderable': false, 'targets': [1] },
            ],
            "oLanguage": {
                "sEmptyTable": WpwAutoPosterAdmin.qs_empty_post,
                "info": "Showing _START_ to _END_ out of _TOTAL_",
                "lengthMenu": "Show _MENU_",
                "search": ""
            },
            "aLengthMenu": [[20, 30, 50, 100], [20, 30, 50, 100]],
            "aaSorting": [],

            "pagingType": "simple_numbers", // Enables full number pagination
            "language": {
                "paginate": {
                    "next": "Next",
                    "previous": "Previous"
                }
            },

            
            "drawCallback": function (settings) {
                var api = this.api();
                var pageInfo = api.page.info();
        
                // Limit the number of pagination buttons displayed
                var paginationButtons = $(settings.nTableWrapper).find('.paginate_button');
                paginationButtons.each(function () {
                    var pageNum = $(this).text();
                    if ($.isNumeric(pageNum)) {
                        var currentPage = pageInfo.page + 1;
                        var totalPages = pageInfo.pages;
        
                        // Show only 3 pages before and after the current page, and the first and last
                        if (Math.abs(pageNum - currentPage) > 3 && pageNum != 1 && pageNum != totalPages) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    }
                });
            },
            "initComplete": function () {
                // Move the search box before the Bulk Action dropdown
                var searchBox = $('.dataTables_filter');
                // var bulkActionDropdown = $('#searchByGender');
                // if (bulkActionDropdown.length) {
                //     searchBox.insertBefore(bulkActionDropdown);
                // }

                var searchInput = searchBox.find('input');
                searchInput.attr('placeholder', 'Search');
            }
        });
        
	}
    // initialize data table for schedule
    if( $('.wpw-auto-poster-schedule-datatable').length > 0 ) {
        // Add the necessary sorting plugins
        $.fn.dataTable.moment(WpwAutoPosterAdmin.$qs_format); // F j, Y g:i a
        $.fn.dataTable.moment('DD/MM/YYYY h:mm a');
        $.fn.dataTable.moment('DD/MM/YYYY hh:mm a');
        $.fn.dataTable.moment('DD/MM/YYYY HH:mm');
        $.fn.dataTable.moment('MMMM D, YYYY h:mm a');
        $.fn.dataTable.moment('MMMM D, YYYY hh:mm a');
        $.fn.dataTable.moment('YYYY-MM-DD h:mm a'); // For format like 2024-08-15 7:30 pm
        $.fn.dataTable.moment('YYYY-MM-DD hh:mm a'); // For format like 2024-08-15 09:30 am

        $( '.wpw-auto-poster-schedule-datatable' ).dataTable( {
            "columnDefs": [
            { 'orderable': false, 'targets': [1]  },
            ],
            "oLanguage": {
                "sEmptyTable": WpwAutoPosterAdmin.qs_empty_schedule
            },
            "aLengthMenu": [[20, 30, 50, 100], [20, 30, 50, 100]],
            "aaSorting": [],
            "pagingType": "full",

            "pagingType": "simple_numbers", // Enables full number pagination
            "language": {
                "paginate": {
                    "next": "Next",
                    "previous": "Previous"
                }
            },

            
            "drawCallback": function (settings) {
                var api = this.api();
                var pageInfo = api.page.info();
        
                // Limit the number of pagination buttons displayed
                var paginationButtons = $(settings.nTableWrapper).find('.paginate_button');
                paginationButtons.each(function () {
                    var pageNum = $(this).text();
                    if ($.isNumeric(pageNum)) {
                        var currentPage = pageInfo.page + 1;
                        var totalPages = pageInfo.pages;
        
                        // Show only 3 pages before and after the current page, and the first and last
                        if (Math.abs(pageNum - currentPage) > 3 && pageNum != 1 && pageNum != totalPages) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    }
                });
            },
            "initComplete": function () {
                // Move the search box before the Bulk Action dropdown
                var searchBox = $('.dataTables_filter');

                var searchInput = searchBox.find('input');
                searchInput.attr('placeholder', 'Search');
            }
        } );
    }

    // on load
    $( '.wpw-auto-poster-panel-title input[type="checkbox"]' ).each( function() {
        if( $(this).prop('checked') ) {
            $( this ).parents( '.wpw-auto-poster-panel' ).addClass('active');
            $( this ).parents( '.wpw-auto-poster-panel' ).find( '.wpw-auto-poster-panel-content' ).slideDown();
        } else {
            $( this ).parents( '.wpw-auto-poster-panel' ).find( '.wpw-auto-poster-panel-content' ).slideUp();
            $( this ).parents( '.wpw-auto-poster-panel' ).removeClass('active');
        }
    } );

    // on change
    $( document ).on( 'change', '.wpw-auto-poster-panel-title input[type="checkbox"]', function() {
        if( $(this).prop('checked') ) {
            $( this ).parents( '.wpw-auto-poster-panel' ).addClass('active');
            $( this ).parents( '.wpw-auto-poster-panel' ).find( '.wpw-auto-poster-panel-content' ).slideDown();
        } else {
            $( this ).parents( '.wpw-auto-poster-panel' ).find( '.wpw-auto-poster-panel-content' ).slideUp();
            $( this ).parents( '.wpw-auto-poster-panel' ).removeClass('active');
        }
    } );

});

jQuery(document).ready(function($){
    $(document).on('change', '#bulk-action-selector-top', function () {
        var action = $(this).val();
        if( action == 'schedule' ) {
            $(".wpw-auto-poster-popup-content").show();
            $(".wpw-auto-poster-popup-overlay").show();
        }
    });

    $(document).on('click', '.done', function () {
        var bulk_action = $('#bulk-action-selector-top').val();
      
        var select_hour = $("input[name='wpw_auto_select_hour']").val();

        if( bulk_action != '' && bulk_action == 'schedule' ) {
            console.log(bulk_action,"bulk_action");
            $('<input />').attr('type', 'hidden')
            .attr('name', "bulk_select_hour")
            .attr('value', select_hour)
            .appendTo('#product-filter');
            $(".wpw-auto-poster-popup-content").hide();
            $(".wpw-auto-poster-popup-overlay").hide();
            $(".wpw-auto-poster-popup-close").trigger("click");
            

        } else {

            var scheduleurl = $("input[name='schedule_url']").val();
            scheduleurl = scheduleurl + "&select_hour=" + select_hour;
            $(location).attr('href', scheduleurl);
        }
    });
})
