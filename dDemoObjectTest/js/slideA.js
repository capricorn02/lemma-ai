export class SlideA {
	recordMode = true;
	commands = [];
	
	// Явно принимаем blob и commands, убирая путаницу со вложенными массивами ...args
	constructor (blob, commands) {
		console.log('SlideA init. Is array of commands?', Array.isArray(commands));
		
		if (commands && Array.isArray(commands) && commands.length > 0) { 
			this.recordMode = false;
			this.blob = blob;
			this.commands = commands;
			this.index = 0;
			this.length = this.commands.length;
		} else {
			// Если пришёл массив, в котором первым элементом лежит блоб (из студии записи)
			if (Array.isArray(blob)) {
				this.blob = blob[0];
			} else {
				this.blob = blob;
			}
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
		this.slideBlock = slideElement;
		this.slideBlock.innerHTML = '';
		this.slideBlock.style.backgroundColor = 'black'; 
		let xx = Math.floor(this.slideBlock.clientWidth / 4);
		let yy = Math.floor(this.slideBlock.clientHeight / 3);
		if (xx > yy) { this.width = yy * 4; this.height = yy * 3; } else { this.width = xx * 4; this.height = xx * 3; }
		this.slideBlock.insertAdjacentHTML('beforeEnd', '<div id="slideDIV" style="position: relative; width: ' + this.width + 'px; height: ' + this.height + 'px; margin: 0 auto;" draggable="false"></div>');
		this.slideDIV = this.slideBlock.querySelector('#slideDIV');
		this.slideDIV.insertAdjacentHTML('beforeEnd', '<canvas id="canvas" draggable="false" style="position: absolute; top: 0px; left: 0px; z-index: 1000"></canvas>');
		this.canvas = this.slideBlock.querySelector('#canvas');
		this.canvas.width = this.width;
		this.canvas.height = this.height;
		this.context = this.canvas.getContext('2d');
	}
	
	execute (command) {
		let action = command[1];
		let options = command[2];
		if (typeof this[action] === 'function') { this[action](options); }
	}
	
	norm (XY){
		let xy = [];
		xy[0] = Math.ceil(10000 * XY[0] / this.width);
		xy[1] = Math.ceil(10000 * XY[1] / this.height);
		return xy;
	}
	
	denorm (xy) {
		let XY = [];
		XY[0] = Math.ceil(xy[0] * this.width / 10000);
		XY[1] = Math.ceil(xy[1] * this.height / 10000);
		return XY;
	}
	
	getStream () { return this.canvas.captureStream(); }
	
	renderTools (toolsElement) {
		if (!this.recordMode) return;
		if (toolsElement) { this.toolsBlock = toolsElement; this.toolsBlock.innerHTML = ''; } else {
			this.slideDIV.insertAdjacentHTML('beforeEnd', '<div id="toolsDIV" style="width: 200px; height: auto; position: absolute; top:0px; left: 0px; z-index: 1003; background-color: rgba(200, 200, 200, 0.8); border: 1px double black; padding: 5px;"></div>');
			this.toolsBlock = this.slideDIV.querySelector('#toolsDIV');
			this.toolsBlock.innerHTML = ''; this.toolsBlock.style.display = 'none';
			this.slideDIV.oncontextmenu = this.toolsShowHide;
		}
	}
	
	toolsShowHide(event) {
		event.preventDefault(); event.stopImmediatePropagation();
		if (event.button != 2) return;
		this.toolsBlock.style.display = (this.toolsBlock.style.display == 'none') ? 'block' : 'none';
	}
	
	start () { let date = new Date(); this.t0 = date.getTime(); }
	
	fixate (action, options) {
		let command = [];
		let date = new Date();
		let t1 = date.getTime();
		command[0] = t1 - this.t0;
		command[1] = action;
		command[2] = options;
		this.commands.push(command);
		this.execute(command);
	}
	
	finish () {}
	
	getCommands () { return this.commands; }
	
	play(){
		if (this.recordMode) return;
		if (this.index == 0) {
			let command = this.commands[this.index];
			this.interval = command[0];
			this.startTime = (new Date()).getTime();
			this.setTimeID = setTimeout(() => { this.next(); }, this.interval);
		} else {
			this.interval = this.interval - (this.stopTime - this.startTime);
			this.startTime = (new Date()).getTime();
			this.setTimeID = setTimeout(() => { this.next(); }, this.interval);
		}
	}
	
	pause(){
		if (this.recordMode) return;
		clearTimeout(this.setTimeID);
		this.stopTime = (new Date()).getTime();
	}
	
	next() {
		let command = this.commands[this.index];
		let t0 = command[0];
		this.execute(command);
		this.index++;
		if (this.index < this.length) {
			let nextCommand = this.commands[this.index];
			let t1 = nextCommand[0];
			this.interval = t1 - t0;
			this.startTime = (new Date()).getTime();
			this.setTimeID = setTimeout(() => { this.next(); }, this.interval);
		}
	}
}
