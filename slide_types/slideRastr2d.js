// ________________class SlideRecord2D_____________________
import {SlideA} from "./slideA.js"

export default class  extends SlideA {
	constructor (...args) {
		console.log (args.length)
		super (args)
				
		this.onMouseDown = this.onMouseDown.bind(this);
		this.onMouseUp = this.onMouseUp.bind(this);
		this.onMouseMove = this.onMouseMove.bind(this);
		
		this.penColor = 'red';
		this.penWidth = '3';
	}
	
	render (slideElement) {
		super.render (slideElement)
		
		this.slideDIV.insertAdjacentHTML ('beforeEnd', `
<img src="${URL.createObjectURL(this.blob)}"	width="${this.width}"	height="${this.height}" draggable="false" style="z-index: 200; position: absolute;  top: 0px; left: 0px">`
		)
	}	
		
//____ COMMANDS ____	
	beginPath () {
		this.context.beginPath ()
	}

	moveTo (options) {
		let XY = this.denorm (options);
		this.context.moveTo (XY[0], XY[1]);
	}
	
	lineTo (options) {
		let XY = this.denorm (options)
		this.context.lineTo (XY[0], XY[1])
		this.context.stroke ()
	}
	
	closePath (options) {
		this.context.closePath ()
	}
	
	setPenColor (options) {
		this.context.strokeStyle = options
	}

	setPenWidth (options) {
		this.context.lineWidth = options
	}	
	
//____ RECORD ____
	renderTools(toolsElement) {
		super.renderTools (toolsElement)
		
		this.thinPen = document.createElement ('button');
		this.thinPen.textContent = '❘';
		this.thinPen.setAttribute ('title','thin pen');
		this.toolsBlock.appendChild (this.thinPen);
		this.thinPen.onclick = (function() {this.penWidth = 3}).bind(this);
		
		this.mediumPen = document.createElement ('button');
		this.mediumPen.textContent = '❙';
		this.mediumPen.setAttribute ('title','medium pen');
		this.toolsBlock.appendChild (this.mediumPen);
		this.mediumPen.onclick = (function() {this.penWidth = 5}).bind(this);
		
		this.thickPen = document.createElement ('button');
		this.thickPen.textContent = '❚';
		this.thickPen.setAttribute ('title','thick pen');
		this.toolsBlock.appendChild (this.thickPen);
		this.thickPen.onclick = (function() {this.penWidth = 7}).bind(this);
		
		this.redPen = document.createElement ('button');
		this.redPen.textContent = '🟥';
		this.redPen.setAttribute ('title','red color');
		this.toolsBlock.appendChild (this.redPen);
		this.redPen.onclick = (function() {this.penColor = 'red'}).bind(this);
		
		this.greenPen = document.createElement ('button');
		this.greenPen.textContent = '🟩';
		this.greenPen.setAttribute ('title','green color');
		this.toolsBlock.appendChild (this.greenPen);
		this.greenPen.onclick = (function() {this.penColor = 'green'}).bind(this);

		this.bluePen = document.createElement ('button');
		this.bluePen.textContent = '🟦';
		this.bluePen.setAttribute ('title','blue color');
		this.toolsBlock.appendChild (this.bluePen);
		this.bluePen.onclick = (function() {this.penColor = 'blue'}).bind(this);		
		this.penColor = 'red';
		this.penWidth = '3';
	}
	
	start () { // начать запись манипуляций со Слайдом 
		super.start ()
		this.slideDIV.onmousedown = this.onMouseDown // включаем реагирование на рисование заметок
	}	

	onMouseDown (event){
		event.stopPropagation()
		let XY = [event.offsetX, event.offsetY]; // записываем в объект начальное время и координаты старта
		this.fixate ('beginPath');
		this.fixate ('setPenColor', this.penColor); 
		this.fixate ('setPenWidth', this.penWidth);
		this.fixate ('moveTo', this.norm(XY));
		this.slideDIV.onmousemove = this.onMouseMove
		this.slideDIV.onmouseup = this.onMouseUp;	
	}

	onMouseUp (event) {
		event.stopPropagation()
		this.fixate ('closePath');
		this.slideDIV.onmousemove = null ;			}
	
	onMouseMove (event) {
		event.stopPropagation()
		let XY = [event.offsetX, event.offsetY];
		this.fixate ('lineTo', this.norm(XY));
	}
	
	finish () { // завершить запись манипуляций со Слайдом
		this.fixate ('closePath');
		this.slideDIV.onmousemove = null;		
		this.slideDIV.mousedown = null; 
	}

//____PLAY____
}	

