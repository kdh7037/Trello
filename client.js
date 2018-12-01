function createHiddenInput(name, value){
    var temp=document.createElement("input");
    temp.setAttribute("type","hidden");
    temp.setAttribute("name",name);
    temp.setAttribute("value",value);
  return temp;
}

    jQuery(function ($) {
        $(this).on("click", "#btn-new-list", function () {
            $("#newlist").addClass('d-none');
            var value = $("#listname").val();
            var temp = $("#mylist").clone().removeClass('d-none').attr("id","").attr("data-listindex",lis+1);
            lis++;
            temp.appendTo("#listform");
            temp.find(".listtitle").val(value);

              var form=document.createElement("form");
            form.setAttribute("method","post");
            form.setAttribute("action","/list_control.php");

              var idx=createHiddenInput("message","add_list");
            form.appendChild(idx);
            
            document.body.appendChild(form);
            form.submit();
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

        .on("click", "#btn-remove-card", function () {
            var temp = $("#mycard").clone().removeClass('d-none').attr("id","");
            $(this).parent().find(".card-body").first().append(temp);
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
    jQuery(function ($) {
        $(document).on("click", "#btn-add-card", function () {
            var temp = $("#mycard").clone().removeClass('d-none').attr("id","");
            $(this).parent().find(".card-body").first().append(temp);
        });
    }); 

    var socket = new WebSocket("ws://www.example.com/socketserver", "protocolOne");

    socket.onopen = function (event) {

    }

    socket.onmessage = function (event) {
        console.log(event.data);
        // 리스트 번호, 카드번호, 스왑, 제거, 추가
        var string = event.data;
        var command = string.split('\n');
        switch(command[0]){
            case "list":
                var listnum = command[1];
            break;
            case "card":
                var cardnum = command[1];
            break;
            case "swap":
                if(command[1] == list)
                    var temp = command[2];
                    command[2] = command[3];
                    command[3] = temp;
                elseif(command[1] == card);
            break;
            case "remove":

            break;
            case "add":

            break;
        }
    }