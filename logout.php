<?php
session_start();       //세션시작
session_unset();     // 현재 연결된 세션 삭제한다
session_destroy();  //세션 종료한다

echo('
		<script>
		alert("로그아웃 되었습니다.");
		location.href="http://localhost:7866/login.php";
		</script>
		');

?>
