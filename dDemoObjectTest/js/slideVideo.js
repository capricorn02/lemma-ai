import {SlideA} from "./slideA.js";
export default class extends SlideA {
	constructor (...args) { super(...args); this.vPlay = this.vPlay.bind(this); this.vPause = this.vPause.bind(this); }
	render (slideElement) {
		super.render(slideElement);
		this.slideDIV.insertAdjacentHTML("beforeEnd", '<video src="' + URL.createObjectURL(this.blob) + '" width="' + this.width + '" height="' + this.height + '" draggable="false" style="z-index: 200; position: absolute; top: 0px; left: 0px"></video>');
		this.VideoEl = this.slideDIV.querySelector("video");
	}	
	vPlay () { this.VideoEl.play(); }
	vPause () { this.VideoEl.pause(); }    
	renderTools(toolsElement) {
		super.renderTools(toolsElement);
		this.playBTN = document.createElement("button"); this.playBTN.textContent = "▶ play"; this.playBTN.style.margin = "2px"; this.toolsBlock.appendChild(this.playBTN);
		this.playBTN.onclick = () => this.fixate("vPlay");
		this.pauseBTN = document.createElement("button"); this.pauseBTN.textContent = "⏸ pause"; this.pauseBTN.style.margin = "2px"; this.toolsBlock.appendChild(this.pauseBTN);
		this.pauseBTN.onclick = () => this.fixate("vPause");
	}    
	pause() { super.pause(); this.vPause(); }
}