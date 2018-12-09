var socket = new WebSocket("ws://localhost:7867");

var modal_listnum = 0;
var modal_cardnum = 0;
var user_id=$('#user_id').val();
var user_email=$('#user_email').val();
var split_split = "dvia3Fivs2QQIV3v";

jQuery(function () {
    $(document).on("click", "#btn-new-list", function () {
        var name = $('#listname').val();
        $('#listname').val("");
        socket.send("add"+split_split+"list"+split_split+name);
    })
    .on("click", ".btn-add-card", function () {
        var listnum = $(this).parent().attr("data-listindex");
        socket.send("add"+split_split+"card"+split_split+listnum);
    })
    .on("click", ".btn-delete-list", function () {
        var listnum = $(this).parent().parent().parent().attr("data-listindex");
        socket.send("delete"+split_split+"list"+split_split+listnum);
    })
    .on("click", "#btn-delete-card", function () {
        var cardnum = $('form').attr("data-cardindex");
        socket.send("delete"+split_split+"card"+split_split+cardnum);
        $('#myModal').modal('hide');
    })
    .on("click", ".btn-delete-comment", function(){
        var commentnum = $(this).parent().parent().attr("data-commentindex");
        socket.send("delete"+split_split+"comment"+split_split+commentnum);
    })
    .on("click", '#btn-adj', function () {
        var newname= $('.title').val();
        socket.send("modify"+split_split+"card_name"+split_split+modal_cardnum+split_split+newname);
        $('#adjust').modal('hide');
    })
    .on("click", '#save-comment', function () {
        var string= $('.input-comment').val();
        var Now = new Date();
        var date = Now.getFullYear();
        date += '-' + Now.getMonth() + 1 ;
        date += '-' + Now.getDate();
        date += ' ' + Now.getHours();
        date += ':' + Now.getMinutes();
        date += ':' + Now.getSeconds();
        socket.send("add"+split_split+"comment"+split_split+modal_cardnum+split_split+user_id+split_split+date+split_split+user_email+split_split+string);
    });
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
        $('#myModal').find('.modal-title').text(temp);
        socket.send("load"+split_split+"card_detail"+split_split+modal_cardnum);
    });

    $("#save-description").on("click", function () {
        var string = $('.description-input').val();
        socket.send("modify"+split_split+"description"+split_split+modal_cardnum+split_split+string);
    });

    $("#btn-add-list").on("click", function () {
        var temp = $("#newlist").removeClass('d-none');
        temp.appendTo("#listform");
    });

    $('[data-listindex]').change(function(){
        var listnum = $(this).attr("data-listindex");
        var name = $(this).find("input").val();
        socket.send("modify"+split_split+"list_name"+split_split+listnum+split_split+name);
    });
});

socket.onopen = function (event) {
    socket.send("load"+split_split+"workspace");
    $('#main-id').find('h4').text(user_id);
    alert("연결 성공!");
};

var comment_card_id;
var comment_user_name;
var comment_user_email;
var comment_date;
var comment_id;
var server_comment = "";

socket.onmessage = function (event) {
    alert(event.data);
    // 스왑, 제거, 추가
    var string = event.data;
    var command = string.split(split_split);
    switch(command[0]){
        /*
        case "load":
            if(command[1] == "workspace"){
                var a=2;
                while(command[a] == "list_info"){
                    var listsplit = command[++a].split('GdwiSEoRfXJsyiw');
                     // $send_data.="split_split+"list_info"+split_split".$list_id[$i].$split_string.$list[$i].$split_string.$card_num[$i];
                    add_list(listsplit[1],listsplit[0]);
                    for(a=a+1; command[a] =="card_info" ; a++){
                        var cardsplit=command[++a].split('GdwiSEoRfXJsyiw');
                        //$send_data.="split_split+"\card_info"+split_split".$card_id[$i][$j].$split_string.$split_string.$card[$i][$j];
                        add_card(listsplit[0],cardsplit[0]);
                        modify_card_name(cardsplit[0],cardsplit[1]);
                    }
                }
            }
            else if(command[1] == "card_detail"){
                //card, card_description 순으로 send
                //user_name, user_email, date, comment_id, messsage 순으로 send
                modify_description(command[2], command[4]);
                for(var i = 5 ; command[i]!=undefined ; i++)
                {   // card_id string id date commentnum email
                    var comment_split=command[i].split("GdwiSEoRfXJsyiw");
                    add_comment(command[2],comment_split[0],comment_split[2],comment_split[3],comment_split[4],comment_split[1]);  
                }
            }
            break;
            */
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
            else if(command[1]=="comment")
                delete_comment(command[2]);
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
                comment_card_id = command[2];
            }
            break;
        case "comment_data":
            comment_user_name=command[1];
            comment_user_email=command[2];
            comment_user_date=command[3];
            comment_id=command[4];
        break;
        case "comment_string":
            server_comment += comment[1];
        break;
        case "comment_end":
            add_comment(comment_card_id, comment_user_name, comment_user_email, comment_user_date, comment_id, server_comment);
            server_comment = "";
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
            if (list_left === undefined)
                list_left = 0;
            socket.send("modify"+split_split+"list_place"+split_split+list_index+split_split+list_left);
        }
    }).disableSelection();
}

function add_card(listnum,cardnum){
    var temp = $("#mycard").clone().removeClass('d-none').attr("id","").attr("data-cardindex",cardnum);
    $('[data-listindex='+listnum+'] .drag-zone').append(temp);
   
    $( ".drag-zone" ).sortable({
        items: $(".inner-card"),
        connectWith: '.drag-zone',
        dropOnEmpty: true,
        update: function(event,ui){
            var list_index = $(this).parent().parent().attr("data-listindex");
            var card_index = ui.item.attr("data-cardindex");
            var card_up = ui.item.prev().attr("data-cardindex");
            if (card_up === undefined)
                card_up = 0;
            socket.send("modify"+split_split+"card_place"+split_split+card_index+split_split+card_up+split_split+list_index);
        }
    }).disableSelection();
}

function add_comment(cardnum, id, email, date, commentnum, string){
    if(cardnum == modal_cardnum){
        var temp = $("#mycomment").clone().removeClass('d-none').attr("data-commentindex",commentnum);
        $("[data-commentindex="+commentnum+"]").find("p").text(id+"("+email+")");
        temp.find(".comment-card").find("span").text(string);
        temp.appendTo("#comment");
        temp.find(".small").val(date);
    }
}
function delete_list(listnum){
    $('[data-listindex='+listnum+']').remove();
}

function delete_card(cardnum){
    $('[data-cardindex='+cardnum+']').remove();
    $('#myModal').find('input').val("");
}

function delete_comment(commentnum){
    $('[data-cardindex='+commentnum+']').remove();
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
    if(cardnum == modal_cardnum)
        $(".description-input").val(string);
}
function load_description(cardnum, string){
    modal_num = cardnum;
    $('#myModal').find(".description-input").val(string);
}