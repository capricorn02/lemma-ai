// ________________class SlideRecordVideo_____________________
import {SlideA} from "./slideA.js"

export default class  extends SlideA {
	constructor (...args) {
		console.log (args.length)
		super (args)
		
		this.vPlay = this.vPlay.bind(this);
		this.vPause = this.vPause.bind(this);

	}

	render (slideElement) {
		super.render (slideElement)
		
		this.slideDIV.insertAdjacentHTML ('beforeEnd', `
<video src="${URL.createObjectURL(this.blob)}"	width="${this.width}"	height="${this.height}" draggable="false" style="z-index: 200; position: absolute;  top: 0px; left: 0px">`
		)
		this.VideoEl = this.slideDIV.querySelector ('video')
	}	

//____ COMMANDS ____	
	vPlay () {
		this.VideoEl.play()
	}

	vPause () {
		this.VideoEl.pause()
	}	
	
//____ RECORD ____
	renderTools(toolsElement) {
		super.renderTools (toolsElement)
		
		this.playBTN = document.createElement ('button');
		this.playBTN.textContent = 'play';
		this.playBTN.setAttribute ('title','play');
		this.toolsBlock.appendChild (this.playBTN);
		this.playBTN.onclick = this.vPlay;
		
		this.pauseBTN = document.createElement ('button');
		this.pauseBTN.textContent = 'pause';
		this.pauseBTN.setAttribute ('title','pause');
		this.toolsBlock.appendChild (this.pauseBTN);
		this.pauseBTN.onclick = this.vPause;
	}	

//____PLAY____
}	

