<?php
session_start();					//세션 시작
if(!isset($_SESSION["mem_email"])) {			//로그인이 안된 상태면 login.php로 이동
	echo('
		<script>
		alert("로그인이 필요합니다.");
		location.href="http://121.130.151.64:7866/login.php";
		</script>
		');
}

// $user_id=$_SESSION["mem_name"];
// $user_email=$_SESSION["mem_email"];
// echo "<script>console.log( 'Debug Objects: " . $user_email . "' );</script>";

?>

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
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<script src="client.js"  charset="UTF-8"></script>
 	<script src="css/bootstrap/js/bootstrap.bundle.js"></script>
 </head>
 

 <body class="bg-primary" style="overflow:auto">
	 <form>
		<input type="hidden" name="user_id" id="user_id" value="<?php session_start(); echo $_SESSION["mem_name"];?>">
		<input type="hidden" name="user_email" id="user_email" value="<?php session_start(); echo $_SESSION["mem_email"];?>">
	</form>
	<div class="modal fade" tabindex="-1" id="myModal" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title"></h4>
					<button type="button" class="close" data-dismiss="modal">×</button>
				</div>
				<div class="modal-body">
					<h6>Descript</h6>
					<input class="description-input form-control">
					<button type="button" id='save-description' class="btn btn-primary">Save</button>
					<h6>Add comment</h6>
					<div class="add-comment">
						<input class="input-comment form-control">
						<button type="button" id='save-comment' class="btn btn-primary">Save</button>
					</div>
				</div>
				<div id="comment" class="modal-footer" style="display:inline">
					<div id="mycomment" class="d-none mb-1" data-commentindex="0" style="width:460px">
						<div class="d-flex">
							<p class="mb-0"></p>
							<button type="button" class="btn-delete-comment btn btn-light">×</button>
						</div>
						<div class="card comment-card form-control">
							<span></span>
						</div>
						<small class="date text-muted"></small>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id='btn-delete-card' class="btn btn-default">Remove</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="modal fade" tabindex="-1" id="adjust" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h6>Adjust Name</h6>
					<button type="button" class="close" data-dismiss="modal">×</button>
				</div>
				<div class="modal-body">
					<input class="title form-control">
					<button type="button" id='btn-adj' class="btn btn-default">Adjust</button>
				</div>
			</div>
		</div>
	</div>

	<nav class="navbar fixed-top navbar-light bg-info mb-3">
		<div class=" mx-auto text-center">
			<button type="button" class="col btn btn-info btn-sm">home</button>
		</div>		
		<div class="col mx-auto text-center">
			<h4>Trello</h4>
		</div>
		<div id="main-id" class="col mx-auto text-center">
			<h4></h4>
		</div>
		<div class=" mx-auto text-center">
			<form class="form-logout" method="post" action="logout.php">
				<button type="submit" class="col btn btn-info btn-sm">Logout</button>
			</form>
		</div>
	</nav>

	<div class="d-inline-flex" style="margin-top:70px">
		<div id="listform" class="d-inline-flex">
		</div>
		<button id='btn-add-list' class="btn btn-secondary" style="width: 264px; height:40px; margin:4px">+ Add another list</button>
	</div>

	<!-- Don't delete it -->
	<!-- list의 본체 -->
	<div id="mylist" class="mylist card listcard m-1 mb-auto p-2 bg-light d-none">
		<div class="drag-body card-body p-0 w-100">
			<div class="d-flex p-1">
				<input type="text" class="d-inline listtitle form-control inv w-100 mt-0">
				<button type="button" class="btn-delete-list close my-auto">X</button>
			</div>
			<div class="drag-zone" style="min-height:40px">
			</div>
		</div>
		<button class="btn-add-card btn btn-secondary w-100">+ Add another card</button>
	</div>
	
	<!-- list를 만드는 폼-->
	<div id="newlist" class="card listcard d-none m-1 p-2 bg-light" style="height:100px">
		<input id='listname'>
			<div class="card-body p-0 w-100">
			</div>
		<button id='btn-new-list' class="btn btn-secondary w-100">Add list</button>
	</div>

	<!-- card의 본체 -->
	<form id="mycard" class="inner-card card mx-0 my-2 d-none w-100">
		<div class="d-flex p-1">
			<span class="d-inline card-text w-100"></span>
			<button type="button" class="btn-adjust-name close my-auto">正</button>
		</div>
	</form>
</body>

</html>
