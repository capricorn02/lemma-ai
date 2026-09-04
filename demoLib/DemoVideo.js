/**
 * Класс DemoVideo — Демо-объект для воспроизведения и управления видео-контентом.
 * Поддерживает фиксацию команд воспроизведения (play) и паузы (pause) во времени.
 * Наследует базовый класс DemoA.
 */
import { DemoA } from "./DemoA.js";

export default class DemoVideo extends DemoA {
	/**
	 * Конструктор Демо-объекта видео.
	 * @param {...any} args - Аргументы, передаваемые в базовый класс DemoA.
	 */
	constructor(...args) {
		console.log(args.length);
		super(...args);

		// Привязка контекста для методов управления видеопотоком
		this.vPlay = this.vPlay.bind(this);
		this.vPause = this.vPause.bind(this);
	}

	/**
	 * Отрисовывает Демо-объект видео в указанном DOM-элементе,
	 * создавая HTML5 тег <video> с источноком из Blob.
	 * @param {HTMLElement} demoElement - Элемент контейнера.
	 */
	render(demoElement) {
		super.render(demoElement);

		this.demoDIV.insertAdjacentHTML(
			'beforeEnd',
			`<video src="${URL.createObjectURL(this.blob)}" width="${this.width}" height="${this.height}" draggable="false" style="z-index: 200; position: absolute; top: 0px; left: 0px"></video>`
		);
		this.VideoEl = this.demoDIV.querySelector('video');
	}

	// ==========================================
	// БЛОК ИСПОЛНЕНИЯ КОМАНД УПРАВЛЕНИЯ ВИДЕО
	// ==========================================

	/** Запускает воспроизведение видео элемента */
	vPlay() {
		this.VideoEl.play();
	}

	/** Приостанавливает воспроизведение видео элемента */
	vPause() {
		this.VideoEl.pause();
	}

	// ==========================================
	// БЛОК ИНСТРУМЕНТОВ ЛЕКТОРА
	// ==========================================

	/**
	 * Создает кнопки управления воспроизведением видео (Play / Pause).
	 * @param {HTMLElement} toolsElement - Внешний элемент панели инструментов.
	 */
	renderTools(toolsElement) {
		super.renderTools(toolsElement);

		// Кнопка воспроизведения
		this.playBTN = document.createElement('button');
		this.playBTN.textContent = 'play';
		this.playBTN.setAttribute('title', 'Воспроизведение');
		this.toolsBlock.appendChild(this.playBTN);
		this.playBTN.onclick = this.vPlay;

		// Кнопка паузы
		this.pauseBTN = document.createElement('button');
		this.pauseBTN.textContent = 'pause';
		this.pauseBTN.setAttribute('title', 'Пауза');
		this.toolsBlock.appendChild(this.pauseBTN);
		this.pauseBTN.onclick = this.vPause;
	}
}
