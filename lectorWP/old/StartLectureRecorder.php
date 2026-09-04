<!DOCTYPE html>
<?php
/*
Отладочная программа для стартов программы SlideRecorder, предназначенной для разработки модулей слайд-объектов
*/
?>
<html>
<head>
	<meta charset="utf-8">
	<title id="title">StartLectureRecorder: Стартуем Рекордер Лекций</title>
	<link href="/favicon.ico" rel="icon">
</head>
<body>
	<header>
		<h2>StartLectureRecorder: Стартуем Рекордер Лекций</h2>
	</header>
	<body>
<!--____________________'mysql-8.2', 'mml', 'root', ''-->
	<div>	
		<p><b> Введите праметры подключения к БД и подключитесь: </b> </p>
		<p> HOST:<input id="hostINPT" type="text" size="10" value="mysql-8.2">
		DB:<input id="dbINPT" type="text" size="10" value="mml">
		USER: <input id="dbUserINPT" type="text"size="10" value="root">
		PASSWORD: <input id="dbPasswordINPT" type="text" size="10" value=""></p>
		<p>Подключиться к БД: <button onclick="setConnection()"> Выполнить</button></p>
		<p id="database_answer">Подключение не выполнено</p>		
	</div>
<!--____________________-->
	<div>
		<p><b> Lecture Live Recorder </b></p>
		<p> Введите ID сценария и перейдите к работе: </p>
		<p> ID лекции:<input id="scenarioINPT" type="text" size="10" value="1">
		<button onclick="lectureLiveRecord()"> Play </button></p>
	</div>
<!--____________________-->
	<div id="StudentLogin">
		<hr>
		<p>Регистрация<button onclick="login()"> Выполнить </button>
		<hr
	</div>
<!--____________________-->
	<div id="ScenarioList">
		<hr>
		<p> К списку сценариев<button onclick="scenarioList()"> Выполнить </button>
		<hr
	</div>
<!--____________________-->
	<div id="LectureList">
		<hr>
		<p> К списку лекций<button onclick="lectureList()"> Выполнить </button>
		<hr
	</div>
<!--____________________-->
	<div id="StudentLogon">
		<hr>
		<p> регистация студента <button onclick="studentLogon()"> Выполнить </button>
		<hr
	</div>

<script src="/lib/dbq.js"></script>
<script src="/lib/settings.js"></script>	
<script>
let databaseAnswerP = document.getElementById ('database_answer');

async function setConnection () {
	DB_HOST = hostINPT.value;
	DB_NAME = dbINPT.value;
	DB_USER = dbUserINPT.value;
	DB_PASSWORD = dbPasswordINPT.value;
	try {
		await connectDB (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
		databaseAnswerP.innerHTML = '<i> Подключение к БД выполнено </i>';
	} catch {
		databaseAnswerP.innerHTML = '<i> Подключиться к БД не удалось </i>';
	}	
}

function lectureLiveRecord (){
	scenarioID = scenarioINPT.value;
//	login = loginINPT.value;
	let hrefString = `LectureRecorder.php?ID=${scenarioID}`;
		popup1 = window.open(hrefString, 'LRLive', 'scrollbars=no,resizable=no,status=no,toolbar=no,menubar=no,width=1200,height=100');
}

function lectureEdit (){
	lectureID = lectureINPT.value;
	login = loginINPT.value;
	let hrefString = `LectureEditor.php?ID=${lectureID}`;
		popup1 = window.open(hrefString, 'demo', 'width=200,height=100');
}

function studentLogon (){
	let hrefString = `StudentLogon.php`;
	window.location = hrefString;	
}

function scenarioList (){
	let hrefString = 'ScenarioList.php?';
	window.location = hrefString;	
}
function lectureList (){
	let hrefString = '/authorWP/LectureList.php?';
	window.location = hrefString;	
}
//___ ____
window.onload = InitPage;

async function InitPage () {
	hostINPT.value = DB_HOST;
	dbINPT.value = DB_NAME;
	dbUserINPT.value = DB_USER;
	dbPasswordINPT.value = DB_PASSWORD;	
//	lectureListDIV.style.display = 'none';
}	
</script>
</body>
</html>