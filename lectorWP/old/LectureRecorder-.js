import { w2grid, w2form, w2popup, query, w2ui, w2alert, w2utils} from "/lib/w2ui-2.0.es6.js"
import { connectDB, getScenario, getScenarioSlides, getSlide, getDemoType, newLecture, setLecture, addLectureSection,
setSection, setSectionVideo, setSectionControls, setSectionBody, getSlideBody} from "/lib/db2.js"

let scenario
let slides
let slide
let lecture = {}
let currentSlide
let nextSlide = 1
let slidesCount
let currentSection
let slideBlob 
let recordFLG = false

window.onload = init
async function init() {
	let recordSPN = document.querySelector ('#recordSPN')
	recordSPN.className = "fa fa-stop"
	let nextBTN = document.querySelector ('#nextBTN')
	nextBTN.addEventListener ('click', nextSection)
	let stopBTN = document.querySelector ('#stopBTN')
	stopBTN.addEventListener ('click', pauseLecture)
	let ejectBTN = document.querySelector ('#ejectBTN')
	ejectBTN.addEventListener ('click', finishLecture)

	let contentBTN = document.querySelector ('#contentBTN')
	contentBTN.addEventListener ('click', toggleContent)
	let webcamBTN = document.querySelector ('#webcamBTN')
	webcamBTN.addEventListener ('click', toggleWebcamControl)
	let cloneBTN = document.querySelector ('#cloneBTN')
	cloneBTN.addEventListener ('click', toggleDemoClone)	
	
	let contentDIV = document.querySelector ('#contentDIV')
	let iFrame = document.querySelector ('iframe')
	let slide1DIV = iFrame.contentWindow.document.querySelector ('#slide1DIV')
	let tools1DIV = document.querySelector ('#tools1DIV')
	let notes1DIV = document.querySelector ('#notes1DIV')

	await connectDB ('mysql-8.2', 'mml', 'root', '')
	scenario = await getScenario (ID) 
	slides = await getScenarioSlides (ID)
	slidesCount = slides.length
	loadContents ()
	await insertLecture ()
	startStream ()
}

async function toggleContent() {
  if (contentBTN.value == 0) {
		contentBTN.value = 1
		contentBTN.innerHTML = '<i class="fa fa-bars"</i>❌';
		contentBTN.title = "свернуть содержание"
  } else {
		contentBTN.value = 0
		contentBTN.innerHTML = '<i class="fa fa-bars"</i><span style="color: green"><b>✔</b></span>';
		contentBTN.title = "открыть содержание"
  }
}

async function toggleWebcamControl() {
  if (webcamBTN.value == 0) {
		webcamBTN.value = 1
		webcamBTN.innerHTML = '<i class="fa fa-video-camera"</i>❌'
		webcamBTN.title = "скрыть видеоконтроль"
  } else {
		webcamBTN.value = 0
		webcamBTN.innerHTML = '<i class="fa fa-video-camera"</i><span style="color: green"><b>✔</b></span>';
		webcamBTN.title = "контроль видеозаписи"
  }
}

async function toggleDemoClone() {
  if (cloneBTN.value == 0) {
		cloneBTN.value = 1
		cloneBTN.innerHTML = '<i class="fa fa-clone"</i>❌'
		cloneBTN.title = "закрыть клон окна демонстраций"
		openDemoClone ()
  } else {
		cloneBTN.value = 0
		cloneBTN.innerHTML = '<i class="fa fa-clone"</i><span style="color: green"><b>✔</b></span>';
		cloneBTN.title = "создать клон окна демонстраций"
		closeDemoClone ()
 }
}

let demoClonePOP
let cloneVideo
let cloneFLG
let cloneStream

function openDemoClone () {
	let params = `
scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,
width=600,height=300,left=100,top=100`
	demoClonePOP = window.open ('./demoClone.html', 'demoClone', params)
	cloneFLG = true
}

function initDemoClone () { 
	cloneVideo = demoClonePOP.document.querySelector ('#myVideo')
//	cloneVideo.srcObject = slide.getStream ()
//	cloneVideo.play();
}

function closeDemoClone () {
}	

async function nextSection () {
	if (recordFLG) {
		await stopSection () 
	} else {		
		recordFLG = true
		recordSPN.className = 'fa fa-dot-circle-o'
		stopBTN.disabled = false		
	}
	currentSlide = nextSlide
	if (currentSlide < slidesCount) {
		nextSlide = currentSlide + 1
	} else {
		nextSlide = 0
		nextBTN.disabled = true
	}
	slide = slides [currentSlide-1]
	slideBlob = await getSlideBody (slide.Slide_ID)
	startSection ()	
}

function startStream () {
	let faceDIV = document.querySelector ('#faceDIV')
	let myVideo = faceDIV.querySelector ('video')
	navigator.mediaDevices.getUserMedia({ audio: true,video: true})
  .then((stream) => {
		window.stream = stream;
    myVideo.srcObject = stream;
  })
  .catch((error) => {
    console.log('navigator.getUserMedia error: ', error);	  
  });
}	

function showContent () {}


async function finishLecture () {
	await stopSection ()
}

async function pauseLecture () {
	await stopSection ()
		recordFLG = true
		recordSPN.className = 'fa fa-stop'
		stopBTN.disabled = false	
}
	
async function insertLecture () {
	lecture = await newLecture (scenario.Title)
	lecture.Notes = scenario.Notes
	await setLecture (lecture)
	currentSlide = 0
	currentSection = 0
}

//____ start-stop record section
let recordedBlobs = []
let slideBodySRC
let demoObj = {}
let slideRecord
let	mediaRecorder
let cmdArr0

async function getDemoLib (ID) {
	let mySlide = await getSlide (ID)
	let slideType = await getDemoType (mySlide.DemoType_ID)
	let typeRef = await import ( slideType.URL)
	return typeRef
}	

async function startSection() { 
	let demoLib = await getDemoLib (slide.Slide_ID)
	
	slide = new demoLib.default (slideBlob)
	slide.render(slide1DIV)
	slide.renderTools(tools1DIV)
	recordedBlobs = []
	try {
		mediaRecorder = new MediaRecorder(window.stream)
	} catch (e) {
		console.error('Exception while creating MediaRecorder: ' + e)
		return
	}
	console.log('Created MediaRecorder', mediaRecorder)
	mediaRecorder.onstop = handleStop
	mediaRecorder.ondataavailable = handleDataAvailable
	mediaRecorder.start(10)
	slide.start()	
	if (cloneFLG) {
		cloneStream = slide.getStream ()
		demoClonePOP.mediaTarget.srcObject = cloneStream
		demoClonePOP.mediaTarget.play()
	}	
}

function handleDataAvailable(event) {
  if (event.data && event.data.size > 0) {
    recordedBlobs.push(event.data)
  }
}

function handleStop () {}

async function stopSection() { // останов записи видео и заметок
	mediaRecorder.stop ()
	if (cloneFLG) {
		let tracks = cloneStream.getTracks();
    tracks[0].stop();
		demoClonePOP.mediaTarget.pause()
	}	

	slide.finish ()
	cmdArr0 = slide.getCommands()
	await sectionSave ()
} 

async function sectionSave () { // сохраняем Секцию, Видео к Секции и Команды и др.
	let videoBLOB = new Blob(recordedBlobs, {type: 'video/webm'})
	let section = await addLectureSection (lecture.Lecture_ID, slide.Title)
	section.Notes = slide.Notes	
	section.DemoType_ID = slide.DemoType_ID
	await setSection (section)
	await setSectionVideo (section.Section_ID, videoBLOB)	
	await setSectionControls (section.Section_ID, cmdArr0)
	await setSectionBody (section.Section_ID, slideBlob)	
}	

//____ contents objects ____ 
function loadContents () {
	contentDIV.innerHTML = `
<div id="elem1" style="height: 300px; width: 50%"></div>
<div id="elem2" style="height: 300px; width: 50%"></div>
`	
	let arr = []
	for (let i=0; i<slides.length; i++) {
		let obj = {};
		let mySlide = slides[i]
		obj.recid = i
		obj.Slide_ID = mySlide.Slide_ID
		obj.Title = mySlide.Title
		obj.Notes = mySlide.Notes
		arr.push(obj);
	}	
	slidesGrid.records = arr;
	slidesGrid.render ('#elem1')
//	slidesGrid.refresh();
	slideForm.render ('#elem2')
	slideForm.lock ()
	contentDIV.style = 'display: none'
}

let slidesGrid = new w2grid({
	header: 'Слайды Сценария',
	name: 'slidesGrid',
	show: {
		header: true,
		toolbar: true,
    toolbarAdd       : false,   
    toolbarEdit      : false,   
    toolbarDelete    : false,   
		toolbarReload    : false,
	},
	multiSearch: false,
	searches: [
		{ field: 'Slide_ID', label: 'ID', type: 'text' },
		{ field: 'Title', label: 'Название', type: 'text' },
	],
	columns: [
		{ field: 'recid', text: '#', size: '50px' },
		{ field: 'Slide_ID', text: 'ID', size: '100px' },
		{ field: 'Title', text: 'Название' },
	],
	onClick(event) {event.done (()=> {onClickClass (this.getSelection())})},
})

async function getSlidesData () {
	let classes = await getClasses()
	let arr = []
	for (let i=0; i<slides.length; i++) {
		let obj = {}
		let mySlide = slides[i]
		obj.recid = i
		obj.Slide_ID = myClass.Slide_ID
		obj.Title = mySlide.Title
		obj.Notes = mySlide.Notes
		arr.push(obj)
	}	
	slidesGrid.records = arr
	slidesGrid.refresh()
}

async function onClickClass (sel) {
	slideForm.header = 'Просмотр Класса'
	if (sel.length == 1) {
		let record = slidesGrid.records[sel[0]]
		slideForm.recid = sel[0]		
		let obj = await getClass (record.Slide_ID)
		slideForm.record = obj	
		slideForm.refresh()
	} else {
		slideForm.clear()
	}
}	

let slideForm = new w2form ({
	header: 'Просмотр Слайда',
	name: 'slideForm',
	fields: [
		{ field: 'Slide_ID', type: 'text', required: true, html: { label: 'Slide_ID', attr: 'size="40" maxlength="40"' } },
		{ field: 'Title', type: 'text', required: true, html: { label: 'Title', attr: 'size="40" maxlength="40"' } },						
		{ field: 'Notes', type: 'textarea', html: { label: 'Notes', attr: 'style="width: 300px; height: 60px"' } }
	]
})