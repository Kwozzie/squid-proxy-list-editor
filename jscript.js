// *** jquery code *** //
$(document).ready(function() {
    if($("#growl div").html().length > 0){
        growl($("#growl div").html());
    }

    $(".chzn-select").chosen();

    function growl(msg){
        $("#growl div").html(msg);
        $("#growl").fadeIn();

        setTimeout(function(){
            $("#growl div").html('');
            $("#growl").fadeOut();
        }, 5000);
    }

    function load_proxy_list(this_list_id){
        $.ajax({
            type: "GET",
            url: "functions.php",
            data: { request: "get_proxy_list", list_id: this_list_id }
        }).done(function( msg ) {

            if(msg.substr(0,6) == "error:"){
                growl(msg.substring(6));
            } else {
                // check if error supplied?
                $("#list_container").html(msg);

                $("#list_container tr:even").addClass("even");
                $("#list_container tr:odd").addClass("odd");
            }
        });
    }

    $("nav").on("change", "select",function(){
        load_proxy_list($(this).val());
    });
    if($("nav select").length){
        load_proxy_list($("nav select").val());
    }

    // on select all, select all tickboxes
    $(document).on("click","input[name=select_all_domains]",function(e){
        $("#list_container td input[type=checkbox]").prop('checked',$(this).prop('checked'));
    });

    // if list item checkbox unticked and check all ticked, untick check all
    $(document).on("click","#list_container td input[type=checkbox]",function(){
        if(!$(this).prop('checked')) $("input[name=select_all_domains]").prop('checked',false);
    });

    $(document).on("click","#add_domain",function(e){
        e.preventDefault();
        var submit_btn = $(this);
        var input_image = $(this).attr("src");
        var new_domain = $("input[name=new_domain]").val();
        var list = $("select[name=access_list]").val();

        $(this).attr("src","images/ajax-loader.gif");
        if(new_domain.length > 0) {

            $.ajax({
                type: "GET",
                url: "functions.php",
                data: { request: "add_new_domain", new_domain: new_domain, list: list }
            }).done(function( msg ) {
                if(msg == "ok"){
                    submit_btn.attr("src","images/tick_green.png");
                    setTimeout(function(){
                        submit_btn.attr("src",input_image);
                        load_proxy_list($("nav select").val());
                    }, 2000);
                } else {
                    submit_btn.attr("src",input_image);
                    growl(msg.substring(6));
                }
            });
        } else {
            growl("Please enter a domain.");
            submit_btn.attr("src",input_image);
        }
    });

    $(document).on("click",".delete_domain",function(e){
        e.preventDefault();
        var confirmed = confirm($(this).attr('title'));
        if(confirmed){
            var o_img = $("img",this).attr('src');

            $.ajax({
                type: "GET",
                url: $(this).attr("href")
            }).done(function( msg ) {

                if(msg == "ok"){
                    load_proxy_list($("nav select").val());
                } else {
                    growl(msg.substring(6));
                }
            });
        }
    });

    $(document).on("click",".move_domain",function(e){
        e.preventDefault();
        var this_domain = $(this).data('domain');
        $("#change_list_input").val(this_domain);
        $("#progress_container").html("");
        $('#change_list_form').jOverlay({success:function(){
            $(".chzn-select").chosen();
        }});
    });

    $(document).on("click","#change_list_save ",function(e){
        e.preventDefault();
        var input_image = $(this).attr("src");
        $(this).attr("src","images/ajax-loader.gif");

        $.ajax({
            type: "GET",
            url: "functions.php",
            data:{request: "move_domain", domain:$("#change_list_input").val(), list:$("#change_list_select").val()}
        }).done(function( msg ) {

            if(msg == "ok"){
                $("#change_list_save").attr("src","images/tick_green.png");
                    setTimeout(function(){
                        load_proxy_list($("nav select").val());
                        $.closeOverlay();
                        $("#change_list_save").attr("src",input_image);
                    }, 2000);
            } else {
                $("#change_list_save").attr("src",input_image);
                $.closeOverlay();
                growl(msg.substring(6));
            }
        });
    });

    $(document).on("click","#change_multi_list_save",function(e){
        e.preventDefault();
        var input_image = $(this).attr("src");
        $(this).attr("src","images/ajax-loader.gif");

        var this_list = $("#change_multi_list_select").val();

        // get all checked items
        var move_domains = new Array();
        $("input[name='domain[]']:checked","#list_table").each(function(){
            move_domains.push($(this).val());
        });

        $.ajax({
            type: "GET",
            url: "functions.php",
            data:{request: "move_multi_domains", domains:move_domains, list:$("#change_multi_list_select").val()}
        }).done(function( msg ) {

            if(msg == "ok"){
                $("#change_multi_list_save").attr("src","images/tick_green.png");
                    setTimeout(function(){
                        load_proxy_list($("nav select").val());
                        $("#change_multi_list_save").attr("src",input_image);
                    }, 2000);
            } else {
                $("#change_multi_list_save").attr("src",input_image);
                growl(msg.substring(6));
                load_proxy_list($("nav select").val());
            }
        });


    });

    $(document).on("click","#delete_selected_domains",function(e){
        e.preventDefault();
        var input_image = $(this).attr("src");
        $(this).attr("src","images/ajax-loader.gif");

        // get all checked items
        var delete_domains = new Array();
        $("input[name='domain[]']:checked","#list_table").each(function(){
            delete_domains.push($(this).val());
        });

        $.ajax({
            type: "GET",
            url: "functions.php",
            data:{request: "delete_multi_domains", domains: delete_domains}
        }).done(function( msg ) {
            if(msg == "ok"){
                $("#delete_selected_domains").attr("src","images/tick_green.png");
                    setTimeout(function(){
                        load_proxy_list($("nav select").val());
                        $("#delete_selected_domains").attr("src",input_image);
                    }, 2000);
            } else {
                $("#delete_selected_domains").attr("src",input_image);
                growl(msg.substring(6));
                load_proxy_list($("nav select").val());
            }
        });
    });
});