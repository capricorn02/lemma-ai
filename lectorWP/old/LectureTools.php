<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title id="title">Lecture Recorder LIVE: Приложение для записи Мультимедиалекции в течение лекционного занятия v.3.0</title>
	<link href="/assets/favicon.ico" rel="icon">
	<link rel="stylesheet" href="/assets/w2ui-2.0.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	<script type="module">
//import { w2layout, w2grid, w2sidebar, w2popup, query, w2ui, w2alert, w2utils} from "/lib/w2ui-2.0.es6.js"
//import { getSlidesData, slidesGrid, setTopPanel} from "./LectureRecorder.js"
import {connectDB, getScenarioList} from "/lib/db2.js"

let scenarios

window.onload = init

async function init () {
		await connectDB ('mysql-8.2', 'mml', 'root', '')
		scenarios = await getScenarioList()
		scenDIV.insertAdjacentHTML ('beforeEnd', 'Список сценариев')
		for (let i=0; i<scenarios.length; i++) {
			scenDIV.insertAdjacentHTML ('beforeEnd', `
<hr><p>ID=${scenarios[i].Scenario_ID}:  ${scenarios[i].Title}		
<a href="LectureRecorder.php?ID=${scenarios[i].Scenario_ID}">Записать</a></p>	
`			
		)}		
}	
	</script>
</head>
<body>
	<div id="scenDIV"></div>
</body>
</html>
