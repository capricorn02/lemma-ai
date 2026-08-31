import {connectDB} from '/lib/db2.js'

const hostINPT = document.querySelector ('#hostINPT')
const dbINPT = document.querySelector ('#dbINPT')
const dbUserINPT = document.querySelector ('#dbUserINPT')
const dbPasswordINPT = document.querySelector ('#dbPasswordINPT')
const dbAnswerP = document.querySelector ('#dbAnswerP')
startLectureRecBTN.onclick = lectureLiveRecorder
setConnectionBTN.onclick = setConnection
	let DB_HOST = 'mysql-8.2';
	let DB_NAME = 'mml';
	let DB_USER = 'root';
	let DB_PASSWORD = '';

window.onload = windowOnload;

async function windowOnload() {
	hostINPT.value = DB_HOST;
	dbINPT.value = DB_NAME;
	dbUserINPT.value = DB_USER;
	dbPasswordINPT.value = DB_PASSWORD;	
}

async function setConnection () {
	DB_HOST = hostINPT.value;
	DB_NAME = dbINPT.value;
	DB_USER = dbUserINPT.value;
	DB_PASSWORD = dbPasswordINPT.value;
	try {
		await connectDB (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
		dbAnswerP.innerHTML = '<i> Подключение к БД выполнено </i>';
	} catch {
		dbAnswerP.innerHTML = '<i> Подключиться к БД не удалось </i>';
	}	
}

function lectureLiveRecorder (){
	let top = 100
	let params = `top={top}, left=0, 'top={top},left=0,scrollbars=no,status=no,toolbar=no,menubar=no,width=600,height=600`
//let	demoWND = window.open ('LRLdemo.html', 'LRLdemo', params)	


	let scenarioID = scenarioINPT.value;
	let hrefString = `LectureRecorder.php?ID=${scenarioID}`;
//	let params = 'scrollbars=no,resizable=no,status=no,toolbar=no,menubar=no,width=600,height=321'
//	let params = 'width=600,height=321'
//let params = `scrollbars=no,resizable=no,status=no,toolbar=no,menubar=no,height=300,width=1000,left=100,top=100`
	let popup1 = window.open(hrefString, 'LRLive5', params);
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
