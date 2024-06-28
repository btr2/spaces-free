jQuery(document).ready(function ($) {


    if ( $('#create-space-form-wrapper').length ) {
        const steps = Object.keys(spaces_engine_main.creation_steps);
        function update_create_space_screen( current, action ) {

            let first_step = steps[0];
            let last_step = steps[steps.length - 1];

            let wrapper = $('#create-space-form-wrapper');

            let form = wrapper.find('#create-space-form');
            let start_button = wrapper.find('.prev-next .start');
            let end_button = wrapper.find('.prev-next .end');

            console.log(current);
            console.log(action);
            $('.space-create-buttons li').removeClass('current');

            if ( 'forward' === action ) {
                wrapper.attr('data-current', getNext(current))
                // new_step = getNext(current)
            } else if ( 'back' === action ) {
                wrapper.attr('data-current', getPrev(current))
                // new_step = getPrev(current)
            } else if ( 'create' === action ) {
                start_button.hide();
            }


            let active = $('.space-create-buttons').find("[data-id='" + wrapper.attr('data-current') + "']").addClass('current');

            console.log(active);
            // let current = wrapper.attr('data-current');
            if ( wrapper.attr('data-current') === first_step ) {
                start_button.hide();
                end_button.removeClass('final');
            } else if ( wrapper.attr('data-current') === last_step ) {
                start_button.text(spaces_engine_main.previous);
                end_button.text(spaces_engine_main.create);
                end_button.addClass('final');
            } else {
                start_button.show();
                end_button.text(spaces_engine_main.next);
                end_button.removeClass('final');
            }
        }

        const getPrev = (current) => {
            let index = steps.indexOf(current);
            const prev = steps[index - 1]
            if (!prev) {
                return undefined
            }

            return prev;
        }
        const getNext = (current) => {
            index = steps.indexOf(current)
            const next = steps[index + 1]
            if (!next) {
                return undefined
            }

            return next;
        }


        $( "body" ).on(
            "click",
            '#create-space-form-wrapper .prev-next button',
            function (e) {


                if ($(this).hasClass('final')) {
                    let current = $('#create-space-form-wrapper').attr('data-current');
                    update_create_space_screen( current, 'create' );
                    create_space( this );
                } else {
                    let current = $('#create-space-form-wrapper').attr('data-current');
                    let action = $(this).attr('data-action');
                    update_create_space_screen( current, action );
                }
            }
        );

    }

    function create_space(e) {
        window.tinyMCE.triggerSave();

        let form = $( e ).closest('#create-space-form');
        let title    = $( form ).find('#space-name').val();
        let description    = $( form ).find('#space-description').val();
        let category    = $( form ).find('#wpe-wps-category-dropdown').val();
        let nonce   = $( form ).attr( "data-nonce" );

        $.ajax(
            {
                type: "POST",
                dataType: "JSON",
                url: spaces_engine_main.ajaxurl,
                data: {
                    title: title,
                    description: description,
                    category: category,
                    nonce: nonce,
                    action: "create_space",
                },
                success: function (response) {
                    if ( ! response.success) {
                        console.log( response );
                        $('.bp-feedback p').text( response.data[0].message);
                        $('.bp-feedback').removeClass('success');
                        $('.bp-feedback').addClass('error');
                        $('.bp-feedback').show();
                    } else {
                        $('.bp-feedback p').text( response.data.message);
                        $('.bp-feedback').removeClass('error');
                        $('.bp-feedback').addClass('success');
                        $('#space-visit').attr('href', response.data.url);
                        $('#create-space-form')[0].reset();
                        $('#create-space-form :input').prop('disabled', true);
                        tinymce.activeEditor.setMode('readonly');
                        $('#space-submit').hide();
                        $('.bp-feedback').show();
                        $('#create-space-result').show();
                    }
                },
            }
        );
    }

    if ( $('#space-archive-container').length > 0) {
        function get_posts(params = null) {
            $container  = $( "#space-archive-container" );

            nonce = $container.data('nonce');

            search_terms = $container.find( "#wpe-wps-spaces-search" ).val();
            category = $container.find( "#wpe-wps-category-dropdown" ).val();
            pagination = $container.data('pagination');

            order = $('#wpe-wps-index-ordering').val();

            if ($("#wpe-wps-index-personal").hasClass("selected")) {
                scope = "personal";
            } else if ($("#wpe-wps-index-all").hasClass("selected")) {
                scope = "all";
            }

            // Get the current page. This is passed via the params parameter.
            if (params && params.paged) {
                page = params.paged;
            } else {
                page = 1;
            }

            params = {
                nonce: nonce,
                page: page,
                scope: scope,
                category: category,
                order: order,
                search_terms: search_terms,
            };

            $content    = $container.find( ".space-archive-wrapper" );
            $action_bar = $container.find( ".spaces-type-navs" );
            $status     = $container.find( ".status" );

            $.ajax(
                {
                    url: spaces_engine_main.ajaxurl,
                    data: {
                        action: 'filter_spaces',
                        nonce: nonce,
                        params: params,
                        pagination: pagination,
                    },
                    type: "post",
                    dataType: "json",
                    success: function (response) {
                        if ( ! response.success) {
                            console.log(response.data[0].code);
                            $status.html(response.data[0].message);
                        } else {
                            $status.hide();
                            $content.html( response.data.content );
                            $content.show();
                        }
                    },
                    error: function (response, textStatus) {
                        console.log(response.data.message);
                        $status.html( textStatus );
                    },
                    complete: function (data, textStatus) {
                        msg = textStatus;

                        $action_bar
                            .find( "#wpe-wps-index-all a .count" )
                            .html( data.responseJSON.found );
                        $action_bar
                            .find( "#wpe-wps-index-personal a .count" )
                            .html( data.responseJSON.found_author );
                    },
                }
            );
        }

        $( '#space-archive-container' ).on(
            "click",
            "#wpe-wps-index-reset",
            function (e) {
                e.preventDefault();

                reset_filters();

                get_posts();

                $('#space-archive-container').removeClass('filters-open');
            }
        );

        $( '#space-archive-container' ).on(
            "click",
            ".pagination a",
            function (e) {
                e.preventDefault();

                // creates a object from the array, one of the properies (search) contains the query
                let url = new URL($(this).attr( "href" ));
                // will create a object of all availalble query properites
                const urlSearchParams = new URLSearchParams(url.search);
                const params = Object.fromEntries(urlSearchParams.entries());

                get_posts(params);
            }
        );

        $( document ).on(
            "click",
            "#wpe-wps-index-filter",
            function (e) {
                e.preventDefault();

                get_posts();

                $('#space-archive-container').removeClass('filters-open');
            }
        );

        $('#wpe-wps-spaces-search').on('keyup change', function () {
            get_posts();
            return false;
        });


        $( document ).on(
            "click",
            ".wpe-wps-index-scope-link",
            function (e) {
                e.preventDefault();

                if ($( this ).attr( "id" ) == "wpe-wps-index-all") {
                    $( "#wpe-wps-index-personal" ).removeClass( "selected" );
                    $( "#wpe-wps-index-all" ).addClass( "selected" );
                } else if ($( this ).attr( "id" ) == "wpe-wps-index-personal") {
                    $( "#wpe-wps-index-all" ).removeClass( "selected" );
                    $( "#wpe-wps-index-personal" ).addClass( "selected" );
                }

                get_posts();
            }
        );

        $( document ).on(
            "change",
            "#wpe-wps-index-ordering",
            function (e) {
                e.preventDefault();

                get_posts();
            }
        );

        $( document ).on(
            "change",
            "#wpe-wps-category-dropdown",
            function (e) {
                e.preventDefault();

                get_posts();
            }
        );

        $( document ).ready(
            function () {
                get_posts();
            }
        );
    }
});