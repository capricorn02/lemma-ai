<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title id="title">LectorWP: стартер инструментов лектора</title>
	<link href="/favicon.ico" rel="icon">
	<script type="module" src="./LectorWP.js"> </script>
</head>
<body>
	<header>
		<h2>LectorWP: стартер инструментов лектора</h2>
	</header>
	<div>	
		<p><b> Введите праметры подключения к БД и подключитесь: </b> </p>
		<p> HOST:<input id="hostINPT" type="text" size="10" value="mysql-8.2">
		DB:<input id="dbINPT" type="text" size="10" value="mml">
		USER: <input id="dbUserINPT" type="text"size="10" value="root">
		PASSWORD: <input id="dbPasswordINPT" type="text" size="10" value=""></p>
		<p>Подключиться к БД: <button id="setConnectionBTN"> Выполнить</button></p>
		<p id="dbAnswerP">Подключение не выполнено</p>		
	</div>
<!--____________________-->
	<div>
		<p><b> Lecture Live Recorder </b></p>
		<p> Введите ID сценария и перейдите к работе: </p>
		<p> ID лекции:<input id="scenarioINPT" type="text" size="10" value="1">
		<button id="startLectureRecBTN"> START </button></p>
	</div>
</body>
</html>