<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title id="title">Lecture Recorder LIVE: Приложение для записи Мультимедиалекции в течение лекционного занятия v.3.0</title>
	<link href="/assets/favicon.ico" rel="icon">
	<link rel="stylesheet" href="/assets/w2ui-2.0.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="module" src="./LectureRecorder.js"> </script>
<script>
<?php	
$scenarioID = $_GET ['ID'];
$str = "window.ID = $scenarioID;\n";
echo $str;	
?>
</script>	
</head>
<body style="margin: 3px">
	<div id="hellowDIV"> <button id="hellowBTN" style="height: 2em" > START </button></div>
	<div id="bodyDIV" style="display: none">
		<div id="headerDIV" style="height: 5vh">
			<span>&nbsp;<span id="recordSPN"></span>&nbsp;</span>
			<span id="slidesSPN"></span>
			<span id="sectionsSPN"></span>
			<button id="nextBTN" value="0" style="height: 2em" title="запись следующей секции">
				<i class="fa fa-dot-circle-o" style="color: red"></i>
				<i class="fa fa-step-forward"></i>
				<span id="xxx">:1</span>
			</button>				
			<button id="stopBTN" value="0" style="width: 2em; height: 2em" title="остановить запись" disabled>
				<i class="fa fa-stop" ></i>
			</button>			
			<button id="contentBTN" value="0" style="width: 3em; height: 2em" title="открыть содержание">
				<i class="fa fa-bars"> </i><span style="color: green"><b>✔</b></span>
			</button>
			<button id="ejectBTN" title="завершить лекцию" style="width: 3em; height: 2em">
				<i class="fa fa-eject" ></i>	
			</button>			
				|	
			<button id="focusBTN" value="0" style="width: 2em; height: 2em" title="востановить фокус">
				<i class="fa fa-file-picture-o"></i>
			</button>
			<button id="notesBTN" value="0" style="width: 2em; height: 2em" title="открыть окно заметок">
				<i class="fa fa-file-text-o"></i>
			</button>
			<button id="cloneBTN" value="0" style="width: 3em; height: 2em" title="клон окна демо">
				<i class="fa fa-clone"></i>
			</button>
			<button id="webcamBTN" value="1" style="width: 4em; height: 2em" title="скрыть видеоконтроль">
				<i class="fa fa-video-camera"></i>❌
			</button>				
				|	
			<button id="broadcastBTN" value="0" style="width: 2em; height: 2em" title="начать трансляцию">
				<i class="fa fa fa-bullhorn"></i>
			</button>
				|	
			<span id="sectionSPN"></span>
				|	
			<span id="timeSPN"></span>
			<hr>
		</div>
		<div id="exitDIV" style="display: none">
			Запись лекции завершена
		</div>
		<div id="contentDIV" style="display: none"></div>
		<div id="faceDIV" style="position: absolute; top: 10px; right: 10px; ">
			<video height="60px" width="80px" autoplay style="border: 1px solid black; " ></video>
		</div>
	</div>	
</body>
</html>
