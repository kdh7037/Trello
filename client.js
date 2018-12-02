function createHiddenInput(name, value){
    var temp=document.createElement("input");
    temp.setAttribute("type","hidden");
    temp.setAttribute("name",name);
    temp.setAttribute("value",value);
  return temp;
}

    var modal_listnum = "0";
    var modal_cardnum = "0";

    jQuery(function () {
        $(document).on("click", "#btn-new-list", function () {
            socket.send("add\\list");
        })
        .on("click", ".btn-add-card", function () {
            var listnum = $(this).parent().attr("data-listindex");
            socket.send("add\\card\\"+listnum);
        })
        .on("click", ".btn-delete-list", function () {
            var listnum = $(this).parent().parent().attr("data-listindex");
            socket.send("delete\\list\\"+listnum);
        })
        .on("click", "#btn-delete-card", function () {
            //수정해야함
            var cardnum = $('form').attr("data-cardindex");
            socket.send("delete\\card\\"+cardnum);
        });
    });
    $(document).ready(function () {

        $('#listform')
        .on('click', 'button.btn-adjust-name', function () {
            $('#adjust').modal('show');
        })

        //수정필요
        $("#adjust").on('click', '.btn-adj', function () {
            var adj= $(".title").val();
            $("#mycard").find("span").val(adj);
        })

        $("#btn-add-list").on("click", function () {
            var temp = $("#newlist").removeClass('d-none');
            temp.appendTo("#listform");
        })
    });
    
    jQuery(function ($) {
        $(this).on("click", "#btn-add-card", function () {
            var temp = $("#mycard").clone().removeClass('d-none').attr("id","");
            $(this).parent().find(".card-body").first().append(temp);

            var form=document.createElement("form");
            form.setAttribute("method","post");
            form.setAttribute("action","/add-list.php");

            var listIdx=temp.parent().parent().getAttribute("data-listindex");
            var parentList=createHiddenInput("list",listIdx);
            form.appendchild(parentList);

            document.body.appendChild(form);
            form.submit();
        });
    }); 

    var socket ;
    socket = new WebSocket("ws://www.example.com/socketserver", "protocolOne");

    socket.onopen = function (event) {

    }

    socket.onmessage = function (event) {
        console.log(event.data);
        // 스왑, 제거, 추가
        var string = event.data;
        var command = string.split('\\');
        switch(command[0]){
            case "load":
                if(command[1] == "workspace"){
                    load_workspace();
                }
                else if(command[1] == "description"){
                    //[2]는 cardnum;
                    load_description(command[2]);
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
                    //[2]는 cardnum, [3]은 옮겨질 곳(위쪽)
                    modify_card_place(command[2],command[3]);
                }
                else if(command[1] == "description"){
                    //[2]는 cardnum, [3]은 string
                    modify_description(command[2],command[3]);
                }
            break;
            case "delete":
                if(command[1]=="list"){
                    //[2]는 listnum
                    delete_card(command[2]);
                }
                else if(command[1]=="card"){
                    //[2]는 cardnum
                    delete_card(command[2]);
                }
            break;
            case "add":
                if(command[1]=="list"){
                    //[2]는 listnum
                    add_list(command[2]);
                }
                else if(command[1]=="card"){
                    //[2]는 listnum [3]은 cardnum
                    add_card(command[2], command[3]);
                }
            break;
        }
    }

    function add_list(listnum){
        $("#newlist").addClass('d-none');
            var value = $("#listname").val();
            var temp = $("#mylist").clone().removeClass('d-none').attr("id","").attr("data-listindex",listnum);
            temp.appendTo("#listform");
            temp.find(".listtitle").val(value);

            return ;
              var form=document.createElement("form");
            form.setAttribute("method","post");
            form.setAttribute("action","/list_control.php");

              var idx=createHiddenInput("message","add_list");
            form.appendChild(idx);
            
            document.body.appendChild(form);
            form.submit();
    }

    function add_card(listnum, cardnum){
        var temp = $("#mycard").clone().removeClass('d-none').attr("id","").attr("data-cardindex",cardnum);
        $('[data-listindex='+listnum+'] .card-body').first().append(temp);
    }

    function delete_list(listnum){
        $('[data-listindex='+listnum+']').remove();
    }

    function delete_card(cardnum){
        $('[data-cardindex='+cardnum+']').remove();
    }
    //여기부터 작성해야합니다
    function modify_list_name(listnum, new_name){
    }

    function modify_list_place(listnum, list_left){
    }

    function modify_card_name(cardnum, new_name){
    }

    function modify_card_place(listnum, card_up){
    }

    function modify_description(cardnum, description){

    }
    function load_workspace(){

    }
    function load_description(cardnum){
        $('#listform').on('click', 'span', function () {
            modal_cardnum = $(this).parent().parent().attr("data-cardindex");
            $('#myModal').modal('show');
        })
    }