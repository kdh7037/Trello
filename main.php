<html lang="ko">
 <head>
 	<meta charset="UTF-8">
 	<meta name="viewport" content="width=device-width, initial-scale=1.0">
 	<meta http-equiv="X-UA-Compatible" content="ie=edge">
 	<title>Document</title>
	<link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
 	<link rel="stylesheet" href="css/css.css">
  <link rel="shortcut icon" href="images/favicon.ico" type="image/favicon.ico"/>
  <link rel="icon" href="images/favicon.ico" type="image/favicon.ico"/>
 	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
 	<script src="css/bootstrap/js/bootstrap.bundle.js"></script>

  <script>
		var num = 0;

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
        		var temp = $("#mylist").clone().removeClass('d-none').attr("id","").attr("data-listindex",num+1);
				num++;
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

			$('.btn-adj').on("click", function () {
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
	</script>
 </head>
 

 <body class="bg-primary">
	<div class="modal fade" tabindex="-1" id="myModal" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">여기에 카드 이름 들어가야하는디</h4>
					<button type="button" class="close" data-dismiss="modal">×</button>
				</div>
				<div class="modal-body">
					<h6>여기에 디스크립트</h6>
					<input class="form-control">
					<h6>여기에 댓글</h6>
					<input class="form-control">
				</div>
				<div class="modal-footer">
					<button type="button" id='btn-remove-card' class="btn btn-default">Remove</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" tabindex="-1" id="adjust" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<input class="title form-control">
				<button id="btn-adj" type="button" class="btn btn-default" data-dismiss="modal">Adjust</button>
			</div>
		</div>
	</div>

	<nav class="navbar navbar-expand-lg navbar-light bg-info mb-3">
		<button type="button" class="btn btn-info btn-sm">home</button>
		<div class="col-4 mx-auto text-center">
			<h4>버튼 여기</h4>
		</div>
		<div class="col-4 mx-auto text-center">
			<h4>Trello</h4>
		</div>
	</nav>
	<div class="d-inline-flex">
		<div id="listform" class="d-inline-flex h-100">
		</div>
		<button id='btn-add-list' class="btn btn-secondary" style="width: 264px; height:40px; margin:4px">+ Add another list</button>
	</div>

	<!-- Don't remove it -->
	<div id="mylist" class="mylist card listcard m-1 p-2 bg-light d-none h-100" data-listindex="0">
		<div class="card-body p-0 w-100">
			<input type="text" class="listtitle form-control inv w-100 my-2">
			<form id="mycard" class="inner-card card mx-0 my-2 d-none w-100" data-cardindex="0">
					<div class="d-flex p-1">
						<span class="d-inline card-text w-100"></span>
						<button type="button" class="btn-adjust-name close my-auto">正</button>
					</div>
			</form>
		</div>
		<button id='btn-add-card' class="btn btn-secondary w-100">+ Add another card</button>
	</div>
	<div id="newlist" class=" card listcard d-none m-1 p-2 bg-light h-100">
		<input id='listname'>
			<div class="card-body p-0 w-100">
			</div>
		<button id='btn-new-list' class="btn btn-secondary w-100">Add list</button>
	</div>


</body>

</html>
