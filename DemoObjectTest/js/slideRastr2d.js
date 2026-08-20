import {SlideA} from "./slideA.js";
export default class extends SlideA {
	constructor (...args) {
		super(...args);
		this.onMouseDown = this.onMouseDown.bind(this);
		this.onMouseUp = this.onMouseUp.bind(this);
		this.onMouseMove = this.onMouseMove.bind(this);
		this.penColor = "red"; this.penWidth = "3";
	}
	render (slideElement) {
		super.render(slideElement);
		this.slideDIV.insertAdjacentHTML("beforeEnd", 
		// Правильный вариант без пропущенных знаков доллара и обратных слэшей:
'<img src="' + URL.createObjectURL(this.blob) + '" width="' + this.width + '" height="' + this.height + '" draggable="false" style="z-index: 200; position: absolute; top: 0px; left: 0px">');
	}	
	beginPath () { this.context.beginPath(); }
	moveTo (options) { let XY = this.denorm(options); this.context.moveTo(XY[0], XY[1]); }
	lineTo (options) { let XY = this.denorm(options); this.context.lineTo(XY[0], XY[1]); this.context.stroke(); }
	closePath () { this.context.closePath(); }
	setPenColor (options) { this.context.strokeStyle = options; this.penColor = options; }
	setPenWidth (options) { this.context.lineWidth = options; this.penWidth = options; }	
	renderTools(toolsElement) {
		super.renderTools(toolsElement);
		const tools = [
			{ text: "❘", title: "thin pen", cb: () => this.penWidth = 3 },
			{ text: "❙", title: "medium pen", cb: () => this.penWidth = 5 },
			{ text: "❚", title: "thick pen", cb: () => this.penWidth = 7 },
			{ text: "🟥", title: "red color", cb: () => this.penColor = "red" },
			{ text: "🟩", title: "green color", cb: () => this.penColor = "green" },
			{ text: "🟦", title: "blue color", cb: () => this.penColor = "blue" }
		];
		tools.forEach(t => {
			let btn = document.createElement("button"); btn.textContent = t.text; btn.setAttribute("title", t.title);
			btn.style.margin = "2px"; btn.style.padding = "2px 5px"; btn.onclick = t.cb; this.toolsBlock.appendChild(btn);
		});
	}
	start () { super.start(); this.slideDIV.onmousedown = this.onMouseDown; document.addEventListener("mouseup", this.onMouseUp); }	
	onMouseDown (event){
		event.stopPropagation(); let XY = [event.offsetX, event.offsetY];
		this.fixate("beginPath"); this.fixate("setPenColor", this.penColor); this.fixate("setPenWidth", this.penWidth);
		this.fixate("moveTo", this.norm(XY)); this.slideDIV.onmousemove = this.onMouseMove;
	}
	onMouseUp (event) { if (!this.slideDIV.onmousemove) return; this.fixate("closePath"); this.slideDIV.onmousemove = null; }
	onMouseMove (event) { event.stopPropagation(); let XY = [event.offsetX, event.offsetY]; this.fixate("lineTo", this.norm(XY)); }
	finish () { this.fixate("closePath"); this.slideDIV.onmousemove = null; this.slideDIV.onmousedown = null; document.removeEventListener("mouseup", this.onMouseUp); }
}