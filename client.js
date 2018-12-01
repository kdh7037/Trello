function createHiddenInput(name, value){
    var temp=document.createElement("input");
    temp.setAttribute("type","hidden");
    temp.setAttribute("name",name);
    temp.setAttribute("value",value);
  return temp;
}

    jQuery(function () {
        $(document).on("click", "#btn-new-list", function () {
            socket.send("add\nlist");
        })
        .on("click", ".btn-add-card", function () {
            var listnum = $(this).parent().attr("data-listindex");
            socket.send("add\ncard\n"+listnum);
        })
        .on("click", ".btn-remove-list", function () {
            var listnum = $(this).parent().parent().attr("data-listindex");
            socket.send("remove\nlist\n"+listnum);
        })
        .on("click", "#btn-remove-card", function () {
            //수정해야함
            var cardnum = $(this).parent().parent().attr("data-listindex");
            socket.send("remove\ncard\n"+cardnum);
        });
    });
    $(document).ready(function () {

        $('#listform')
        .on('click', 'button.btn-adjust-name', function () {
            $('#adjust').modal('show');
        })

        .on('click', 'span', function () {
            $('#myModal').modal('show');
        })

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
        var command = string.split('\n');
        switch(command[0]){
            case "swap":
                if(command[1] == "list"){
                    var temp = command[2];
                    command[2] = command[3];
                    command[3] = temp;
                }
                else if(command[1] == "card"){

                }
            break;
            case "remove":
                if(command[1]=="list"){
                    //[2]는 listnum
                    remove_card(command[2]);
                }
                else if(command[1]=="card"){
                    //[2]는 cardnum
                    remove_card(command[2]);
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

    function remove_list(listnum){
        $('[data-listindex='+listnum+']').remove();
    }

    function remove_card(cardnum){
        $('[data-cardindex='+cardnum+']').remove();
    }