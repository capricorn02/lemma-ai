import { w2grid, w2form, w2popup, query, w2ui, w2alert, w2utils} from "/lib/w2ui-2.0.es6.js"
import { connectDB, getScenario, getScenarioSlides, getSlide, getDemoType, newLecture, setLecture, addLectureSection,
setSection, setSectionVideo, setSectionControls, setSectionBody, getSlideBody} from "/lib/db2.js"

let scenario
let slides
let slide
let demo
let lecture = {}
let currentSlide
let nextSlide = 1
let slidesCount
let currentSection
let slideBlob 
let recordFLG = false

let demoWND
let notesWND
let cloneWND

	const hellowDIV = document.querySelector ('#hellowDIV')
	const hellowBTN = document.querySelector ('#hellowBTN')
	hellowBTN.onclick = hellow

	const bodyDIV = document.querySelector ('#bodyDIV')
	hellowDIV.style.display = 'block'
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
	
	let focusBTN = document.querySelector ('#focusBTN')
	focusBTN.addEventListener ('click', focusOn)	
	let notesBTN = document.querySelector ('#notesBTN')
	notesBTN.addEventListener ('click', openNotes)
	let cloneBTN = document.querySelector ('#cloneBTN')
	cloneBTN.addEventListener ('click', openClone)
	
	let contentDIV = document.querySelector ('#contentDIV')

window.onload = windowOnload

async function windowOnload() {
	resizeW()
	scenario = await getScenario (ID) 
	slides = await getScenarioSlides (ID)
	slidesCount = slides.length
}

function resizeW () {
	let a = window.outerHeight
	let b = window.innerHeight
	let c = document.querySelector('body').offsetHeight
	let h = a - b + c + 6
	let w = window.outerWidth
	window.resizeTo(w, h)
}

async function hellow () {
	openDemo()
	openNotes()
	bodyDIV.style.display = 'block'
	hellowDIV.style.display = 'none'
	await insertLecture ()
	startStream ()	
}


function openClone() { //открыть окно заметок
	cloneWND = window.open ('LRLclone.html', 'LRLclone', 'top=300,left=750,scrollbars=no,status=no,toolbar=no,menubar=no,width=400,height=300')
}

async function toggleContent() { //показать/свернуть оглавление
  if (contentBTN.value == 0) {
		contentBTN.value = 1
		contentBTN.innerHTML = '<i class="fa fa-bars"</i>❌';
		contentBTN.title = "свернуть оглавление"
  } else {
		contentBTN.value = 0
		contentBTN.innerHTML = '<i class="fa fa-bars"</i><span style="color: green"><b>✔</b></span>';
		contentBTN.title = "показать оглавление"
  }
}

async function toggleWebcamControl() { //контроль видеозаписи / скрыть видеоконтроль
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

async function toggleDemoClone() { //создать клон окна демо / закрыть клон окна демонстраций
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

async function nextSection () { // завершить запись секции и начать запись следующей секции
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

async function pauseLecture () { // завершить запись секции без старта следующей
	await stopSection ()
		recordFLG = true
		recordSPN.className = 'fa fa-stop'
		stopBTN.disabled = false	
}

function startStream () { // старт потока с веб-камеры
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

async function insertLecture () {
	lecture = await newLecture (scenario.Title)
	lecture.Notes = scenario.Notes
	await setLecture (lecture)
	currentSlide = 0
	currentSection = 0
}

async function finishLecture () {
	await stopSection ()
	demoWND.close()
	notesWND.close()
	bodyDIV.style.display = 'none'
	exitDIV.style.display = 'block'
}	

//____ start-stop record section
let recordedBlobs = []
let slideBodySRC
let demoObj = {}
let slideRecord
let	mediaRecorder
let cmdArr0

async function getDemoLib (ID) { // по ID слайда получить ссылку на класс типа слайда
	let mySlide = await getSlide (ID)
	let slideType = await getDemoType (mySlide.DemoType_ID)
	let typeRef = await import ( slideType.URL)
	return typeRef
}	

async function startSection() { // стартует запись секции
	let demoLib = await getDemoLib (slide.Slide_ID)
	
	demo = new demoLib.default (slideBlob)
	demo.render(demoDIV)
	demo.renderTools()
	recordedBlobs = []
	window.notesDIV.innerHTML = slide.Notes.replace(/(\r\n|\r|\n)/g, "<br>")
	try {
		mediaRecorder = new MediaRecorder(window.stream)
	} catch (e) {
		console.error('Exception while creating MediaRecorder: ' + e)
		return
	}
	console.log('Created MediaRecorder', mediaRecorder)
	mediaRecorder.ondataavailable = handleDataAvailable
	mediaRecorder.start(10)
	demo.start()	
}

function handleDataAvailable(event) {
  if (event.data && event.data.size > 0) {
    recordedBlobs.push(event.data)
  }
}

async function stopSection() { // останов записи видео и заметок
	mediaRecorder.stop ()
	demo.finish ()
	cmdArr0 = demo.getCommands()
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
// 
function openDemo() { //открыть окно демо
	let top = window.outerHeight
	let params = `top=${top},left=0,,scrollbars=no,status=no,toolbar=no,menubar=no,width=700,height=600`
	demoWND = window.open ('LRLdemo.html', 'LRLdemo', params)	
	if (!demoWND) {console.log ('no demoWND')}
}

function focusOn() {
		demoWND.focus()
		notesWND.focus()
}	
	
function openNotes() { //открыть окно заметок
	let top = window.outerHeight
	let params = `top=${top},left=850,scrollbars=no,status=no,toolbar=no,menubar=no,width=700,height=600`
	notesWND = window.open ('LRLnotes.html', 'LRLnotes', params)
}	

