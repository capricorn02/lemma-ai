export class SlideA {
/*
Абстрактный класс для записи (режим RECORD) и проигрывания (режим PLAY) слайдов разных типов.
Режим задается при создании объекта и потом не меняется
*/	
	recordMode = true // флаг определяет моду: RECORD или PLAY
	commands = [] // массив для команд управления слайдом
	
	constructor (atrArr) {
/*
Конструктор класса
получает от конструктора наследующего класса один параметр - массив длинны 1 или 2. Первый элемент массива - переменная типа blob в которой хранится тело демонстрации. Если есть второй элемент, то это список команд демонстрации. 
если режим 
*/
		console.log (atrArr.length)
		this.blob = atrArr[0]
		if (atrArr.length>1) {			
			this.recordMode = false
			this.commands = atrArr[1]
			this.index = 0
			this.length = this.commands.length
		}

		this.finish = this.finish.bind(this);
		this.pause = this.pause.bind(this);
		this.play = this.play.bind(this);
		this.next = this.next.bind(this);		
		this.fixate = this.fixate.bind(this);
		this.execute = this.execute.bind(this);
		this.toolsShowHide = this.toolsShowHide.bind(this);
	}

	render (slideElement) {
// метод вписывает слайд в HTML элемент
		this.slideBlock = slideElement
		this.slideBlock.innerHTML = ''
		this.slideBlock.style.backgroundColor = 'black' 
		let xx = Math.floor ( this.slideBlock.clientWidth/4)
		let yy = Math.floor ( this.slideBlock.clientHeight/3)
// размеры слайда в пикселях
		if (xx > yy) {
			this.width = yy*4
			this.height = yy*3
		} else {
			this.width = xx*4
			this.height = xx*3
		}
// организуем в элементе DIV контейнер
		this.slideBlock.insertAdjacentHTML('beforeEnd', `
<div id="slideDIV" style="position: relative" draggable="false"></div>`
		)
// ... и вставляем в него canvas 
		this.slideDIV = this.slideBlock.querySelector ('#slideDIV')
		this.slideDIV.insertAdjacentHTML('beforeEnd', `
<canvas id="canvas" draggable="false" style="position: absolute; top: 0px; left: 0px; z-index: 1000">`
		)
		this.canvas = this.slideBlock.querySelector('#canvas')
		this.canvas.width = this.width;
		this.canvas.height = this.height;
		this.context = this.canvas.getContext ('2d');
	}

	execute (command) {
//  метод выполняет одну команду управления слайдом
		let action = command[1]
		let options = command[2]
		this[action](options)
	}

	norm (XY){
		let xy = new Array ();
		xy[0] = Math.ceil (10000*XY[0]/this.width);
		xy[1] = Math.ceil (10000*XY[1]/this.height);
		return xy;
	}

	denorm (xy) {
		let XY = new Array ();
		XY[0] = Math.ceil (xy[0]*this.width/10000);
		XY[1] = Math.ceil (xy[1]*this.height/10000);
		return XY;
	}	

	getStream () {
		return this.canvas.captureStream()
	}	
		
//____ RECORD ____	
	renderTools (toolsElement) {
// Метод организует представление панели инструментов
		if (!this.recordMode) return
		if (toolsElement) {		
			this.toolsBlock = toolsElement;
			this.toolsBlock.innerHTML = '';
		} else {
			this.slideDIV.insertAdjacentHTML ('beforeEnd', `
<div id="toolsDIV" style="
	width: 200px; height: 200px;
	position: absolute; top:0px; left: 0px; z-index: 1003;
		background-color: rgba(200, 200, 200, 0.5);
	border: 1px double black;">
</div>`
			)
			this.toolsBlock = this.slideDIV.querySelector ('#toolsDIV')
			this.toolsBlock.innerHTML = ''
			this.toolsBlock.style.display = 'none'
			this.slideDIV.oncontextmenu = this.toolsShowHide
		}	
	}
	
	toolsShowHide(event) {
		event.preventDefault()
		event.stopImmediatePropagation()
		if (event.button != 2) {return} else {
			if (this.toolsBlock.style.display == 'none') {
				this.toolsBlock.style.display = 'block'
			} else {
				this.toolsBlock.style.display = 'none'
			}
		}
	}	
	
	start () {
// Метод начинает запись манипуляций со Слайдом 
		let date = new Date();
		this.t0 = date.getTime();
	}	

	fixate (action, options) {
// Метод фиксирует (добавляет в массив команд) одну команду манипуляции со слайдом 
		let command = [];
		let date = new Date();
		let t1 = date.getTime();
		command[0] = t1 - this.t0;
		command[1] = action;
		command[2] = options;
		this.commands.push (command);
		this.execute (command);
	}
	
	finish () { // завершить запись манипуляций со Слайдом
	}
	
	getCommands () {
// Метод отдает массив команд манипуляций со Слайдом
		return this.commands;
	}

// ____ PLAY ____	
  play(){
// Метод начинает или возобнавляет выполнение команд манипуляций
		if (this.recordMode) return
		if (this.index == 0) {
			let command = this.commands[this.index];
			this.interval = command[0];
			this.startTime = (new Date()).getTime();
			this.setTimeID = setTimeout (this.next, this.interval);
		}	else {
			this.interval = this.interval - (this.stopTime - this.startTime);		
			this.startTime = (new Date ()).getTime();
			this.setTimeID = setTimeout(this.next, this.interval)
		}	
	}	
		
	pause(){
// Метод останавливает выполнение команд манипуляций
		if (this.recordMode) return
		clearTimeout (this.setTimeID);
		this.stopTime = (new Date ()).getTime();
	}	
	
	next() {
// Метод выполняет следующую команду манипуляций
		let command = this.commands[this.index];
		let t0 = command[0];
		this.execute (command);
		this.index ++;
		if (this.index < this.length-1) {
			let command = this.commands[this.index];
			let t1 = command[0];
			this.interval = t1-t0;
			this.startTime = (new Date ()).getTime();
			this.setTimeID = setTimeout (this.next, this.interval);
		}
	}
}	