    var socket = new WebSocket("ws://121.130.151.64:7867");

    var modal_listnum = 0;
    var modal_cardnum = 0;

    jQuery(function () {
        $(document).on("click", "#btn-new-list", function () {
            var name = $('#listname').val();
            socket.send("add\\list\\"+name);
        })
        .on("click", ".btn-add-card", function () {
            var listnum = $(this).parent().attr("data-listindex");
            socket.send("add\\card\\"+listnum);
        })
        .on("click", ".btn-delete-list", function () {
            var listnum = $(this).parent().parent().parent().attr("data-listindex");
            socket.send("delete\\list\\"+listnum);
        })
        .on("click", "#btn-delete-card", function () {
            var cardnum = $('form').attr("data-cardindex");
            socket.send("delete\\card\\"+cardnum);
        })
        .on("click", '#btn-adj', function () {
            var newname= $('.title').val();
            socket.send("modify\\card_name\\"+modal_cardnum+"\\"+newname);
        })
        .on("click", '#save-description', function () {
            var string= $('.input-comment').val();
            socket.send("modify\\description\\"+modal_cardnum+"\\"+string+"\\"+id);
        })
        .on("click", '#save-comment', function () {
            var string= $('.description-input').val();
            socket.send("add\\comment\\"+modal_cardnum+"\\"+string);
        })
    });


    $(document).ready(function () {

        $('#listform')
        .on('click', '.btn-adjust-name', function () {
            modal_cardnum = $(this).parent().parent().attr("data-cardindex");
            $('#adjust').modal('show');
        })
        .on('click', 'span', function () {
            modal_cardnum = $(this).parent().parent().attr("data-cardindex");
            $('#myModal').modal('show');
            var temp = $(this).text();
            $('#myModal').find('h4').text(temp);
            socket.send("load\\description\\"+modal_cardnum);
        })
        //수정요함
        .on("click", "#save-description", function () {
            var string = $('.description-input').val();
            socket.send("load\\card\\"+modal_cardnum);
        });

        $("#btn-add-list").on("click", function () {
            var temp = $("#newlist").removeClass('d-none');
            temp.appendTo("#listform");
        });

        $('[data-listindex]').change(function(){
            var listnum = $(this).attr("data-listindex");
            var name = $(this).find("input").val();
            socket.send("modify\\list_name\\"+listnum+"\\"+name);
        })
    });

    socket.onopen = function (event) {
        alert("연결 성공!");
    };

    socket.onmessage = function (event) {
        alert(event.data);
        // 스왑, 제거, 추가
        var string = event.data;
        var command = string.split('\\');
        switch(command[0]){
            case "load":
                if(command[1] == "workspace"){
                    load_workspace();
                }
                else if(command[1] == "description"){
                    //[2]는 cardnum, [3]은 description string
                    load_description(command[2],command[3]);
                } 
            case "modify":
                if(command[1] == "list_name"){
                    //[2]는 listnum, [3]은 new_name
                    modify_list_name(command[2],command[3]);
                }
                else if(command[1] == "list_place"){
                    //[2]는 listnum, [3]은 옮겨질 곳(왼쪽)
                    modify_list_place(command[2],command[3]);
                }
                else if(command[1] == "card_name"){
                    //[2]는 cardnum, [3]은 new_name
                    modify_card_name(command[2],command[3]);
                }
                else if(command[1] == "card_place"){
                    //[2]는 cardnum, [3]은 옮겨질 곳(위쪽), [4]는 listnum
                    modify_card_place(command[2],command[3],command[4]);
                }
                else if(command[1] == "description"){
                    //[2]는 cardnum, [3]은 string
                    modify_description(command[2],command[3]);
                }
            break;
            case "delete":
                if(command[1]=="card")
                    delete_card(command[2]);
                else if(command[1]=="list")
                    delete_list(command[2]);
            break;
            case "add":
                if(command[1]=="list"){
                    //[2]는 name, [3]은 listnum
                    add_list(command[2], command[3]);
                }
                else if(command[1]=="card"){
                    //[2]는 listnum [3]은 cardnum
                    add_card(command[2], command[3]);
                }
                else if(command[1]=="comment"){
                    //[2]는 cardnum [3]은 string [4]는 id
                    add_comment(command[2], command[3], command[4]);
                }
            break;
        }
    };

    function add_list(name, listnum){
        $("#newlist").addClass('d-none');
        var temp = $("#mylist").clone().removeClass('d-none').attr("id","").attr("data-listindex",listnum);
        temp.appendTo("#listform");
        temp.find(".listtitle").val(name);

        $( "#listform" ).sortable({
            items: $('.mylist'),
            update: function(event,ui){
                var list_index = ui.item.attr("data-listindex");
                var list_left = ui.item.prev().attr("data-listindex");
                socket.send("modify\\list_place\\"+list_index+"\\"+list_left);
            }
        }).disableSelection();
    }

    function add_card(listnum,cardnum){
        var temp = $("#mycard").clone().removeClass('d-none').attr("id","").attr("data-cardindex",cardnum);
        $('[data-listindex='+listnum+'] .drag-zone').append(temp);
       
        $( ".drag-zone" ).sortable({
            items: $('.inner-card'),
            connectWith: '.drag-zone',
            update: function(event,ui){
                var list_index = $(this).parent().attr("data-listindex");
                var card_index = ui.item.attr("data-cardindex");
                var card_up = ui.item.prev().attr("data-cardindex");
                socket.send("modify\\card_place\\"+card_index+"\\"+card_up+"\\"+list_index);
            }
        }).disableSelection();
    }

    function add_comment(cardnum, string, id){
        

        var temp = $("#mycomment").clone().removeClass('d-none');
        $("#mycomment p").text(id);
        temp.find(".comment-card").val(string);
        temp.appendTo("#comment");
        temp.find(".listtitle").val(name);
    }
    function delete_list(listnum){
        $('[data-listindex='+listnum+']').remove();
    }

    function delete_card(cardnum){
        $('[data-cardindex='+cardnum+']').remove();
    }

    function modify_list_name(listnum, new_name){
        $('[data-listindex='+listnum+']').find("input").val(new_name);
    }

    function modify_list_place(listnum, list_left){
        var list = $('[data-listindex='+listnum+']');
        $('[data-listindex='+list_left+']').after(list);
    }

    function modify_card_name(cardnum, new_name){
        $('[data-cardindex='+cardnum+']').find("span").text(new_name);
    }

    function modify_card_place(cardnum, card_up, listnum){
        var card = $('[data-cardindex='+cardnum+']');
        $('[data-cardindex='+card_up+']').after(card);
    }

    function modify_description(cardnum, string){
        $('[data-cardindex='+cardnum+']').find("description-input").val(string);
    }
    function load_workspace(){

    }
    function load_description(cardnum, string){
        modal_num = cardnum;
        $('#myModal').find(".description-input").val(string);
    }