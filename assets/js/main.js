jQuery(document).ready(function ($) {
    $( "body" ).on(
        "click",
        "#create-space-form #space-submit",
        function () {
            create_space( this );
            return false;
        }
    );
    function create_space(e) {
        let form = $( e ).closest('#create-space-form');
        let title    = $( form ).find('#space-name').val();
        let nonce   = $( form ).attr( "data-nonce" );

        $.ajax(
            {
                type: "POST",
                dataType: "JSON",
                url: spaces_engine_main.ajaxurl,
                data: {
                    title: title,
                    nonce: nonce,
                    action: "create_space",
                },
                success: function (response) {
                    if ( ! response.success) {
                        console.log( response );
                    } else {
                        $('#space-visit').attr('href', response.data);
                        $('#create-space-result').show();
                        $('#create-space-form').hide();
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