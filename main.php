<?php

  function EchoCard()
  {
    echo '<div class="inner-card card my-2">
            <div class="card-body p-1" id="myBtn">
              <p class="card-text">Some quick examp</p>
            </div>
          </div>';
  }

  function EchoList()
  {
    echo '<div class="card listcard m-1 ml-2 bg-light">
			    <div class="card-body p-1 m-1">
				     <input type="text" class="form-control inv" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter list title...">';
    for($i=0; $i<3; $i++)
    {
      EchoCard();
    }
    echo '<p class="card-text">This card h</p>
          <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
          </div>
          </div>';
  }
 ?>

<html lang="en">
 <head>
 	<meta charset="UTF-8">
 	<meta name="viewport" content="width=device-width, initial-scale=1.0">
 	<meta http-equiv="X-UA-Compatible" content="ie=edge">
 	<title>Document</title>
 	<link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
 	<link rel="stylesheet" href="css/css.css">
 	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
 	<script src="css/bootstrap/js/bootstrap.bundle.js"></script>
 	<script>
 		$(document).ready(function(){
 			$('.inner-card').on('click', function () {
 				$('#myModal').modal('show');
 			});
 		});
 	</script>
 </head>

 <body class=bg-primary>
 	<div class="modal fade" tabindex="-1" id="myModal" role="dialog" aria-hidden="true">
 		<div class="modal-dialog">
 			<div class="modal-content">
 				<div class="modal-header">
 					<h4 class="modal-title">여기에 카드 이름 들어가야하는디</h4>
 					<button type="button" class="close" data-dismiss="modal">×</button>
 				</div>
 				<div class="modal-body">
 					<h6>여기에 디스크립트</h6>
 					<input type="email" class="form-control">
 					<h6>여기에 댓글</h6>
 					<input type="email" class="form-control">
 				</div>
 				<div class="modal-footer">
 					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
 				</div>
 			</div>
 		</div>
 	</div>

 	<nav class="navbar navbar-expand-lg navbar-light bg-info">
 		<button type="button" class="btn btn-info btn-sm">home</button>
 		<div class="col-4 mx-auto text-center">
 			<h4>버튼 여기</h4>
 		</div>
 		<div class="col-4 mx-auto text-center">
 			<h4>Trello</h4>
 		</div>
 	</nav>
 	<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
 		<button type="button" class="btn btn-primary btn-sm">제목</button>
 		<button type="button" class="btn btn-primary btn-sm">별</button>
 		<button type="button" class="btn btn-primary btn-sm">Personal</button>
 		<button type="button" class="btn btn-primary btn-sm">private</button>
 		<div class="col-4 mx-auto text-center">
 			<h4>버튼 여기</h4>
 		</div>
 		<div class="col-4 mx-auto text-center">
 			<h4>Trello</h4>
 		</div>
 	</nav>
 	<div class="d-inline-flex">
    <?php
      for($i=0; $i<3; $i++)
      {
        EchoList();
      }
    ?>
  </div>
</body>

</html>
