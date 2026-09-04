/**
 * Класс DemoRastr2d — Демо-объект для отображения 2D растровых изображений
 * с поддержкой интерактивного рисования заметок поверх картинки.
 * Наследует базовый класс DemoA.
 */
import { DemoA } from "./DemoA.js";

export default class DemoRastr2d extends DemoA {
	/**
	 * Конструктор Демо-объекта 2D графики.
	 * @param {...any} args - Аргументы, передаваемые в базовый класс DemoA.
	 */
	constructor(...args) {
		console.log(args.length);
		super(...args);

		// Привязка контекста обработчиков событий рисования
		this.onMouseDown = this.onMouseDown.bind(this);
		this.onMouseUp = this.onMouseUp.bind(this);
		this.onMouseMove = this.onMouseMove.bind(this);

		// Настройки пера по умолчанию
		this.penColor = 'red';
		this.penWidth = '3';
	}

	/**
	 * Отрисовывает Демо-объект 2D графики в указанном DOM-элементе,
	 * добавляя фоновое изображение из переданного Blob.
	 * @param {HTMLElement} demoElement - Элемент контейнера.
	 */
	render(demoElement) {
		super.render(demoElement);

		// Вставляем фоновую растровую картинку под холст Canvas
		this.demoDIV.insertAdjacentHTML(
			'beforeEnd',
			`<img src="${URL.createObjectURL(this.blob)}" width="${this.width}" height="${this.height}" draggable="false" style="z-index: 200; position: absolute; top: 0px; left: 0px">`
		);
	}

	// ==========================================
	// БЛОК ИСПОЛНЕНИЯ КОМАНД РИСОВАНИЯ
	// ==========================================

	/** Начинает новый контур рисования на Canvas */
	beginPath() {
		this.context.beginPath();
	}

	/**
	 * Перемещает курсор рисования в нормированные координаты.
	 * @param {Array} options - Нормированные координаты [x, y].
	 */
	moveTo(options) {
		let XY = this.denorm(options);
		this.context.moveTo(XY[0], XY[1]);
	}

	/**
	 * Проводит линию до указанных нормированных координат.
	 * @param {Array} options - Нормированные координаты [x, y].
	 */
	lineTo(options) {
		let XY = this.denorm(options);
		this.context.lineTo(XY[0], XY[1]);
		this.context.stroke();
	}

	/** Замыкает текущий контур */
	closePath(options) {
		this.context.closePath();
	}

	/**
	 * Устанавливает цвет пера.
	 * @param {string} options - Цвет пера (например, 'red', 'blue').
	 */
	setPenColor(options) {
		this.context.strokeStyle = options;
	}

	/**
	 * Устанавливает толщину пера.
	 * @param {number|string} options - Толщина пера в пикселях.
	 */
	setPenWidth(options) {
		this.context.lineWidth = options;
	}

	// ==========================================
	// БЛОК МЕТОДОВ ЗАПИСИ И ИНСТРУМЕНТОВ
	// ==========================================

	/**
	 * Формирует панель инструментов для выбора цвета и толщины пера.
	 * @param {HTMLElement} toolsElement - Внешний элемент панели инструментов.
	 */
	renderTools(toolsElement) {
		super.renderTools(toolsElement);

		// Кнопка: Тонкое перо
		this.thinPen = document.createElement('button');
		this.thinPen.textContent = '❘';
		this.thinPen.setAttribute('title', 'Тонкое перо');
		this.toolsBlock.appendChild(this.thinPen);
		this.thinPen.onclick = function () {
			this.penWidth = 3;
		}.bind(this);

		// Кнопка: Среднее перо
		this.mediumPen = document.createElement('button');
		this.mediumPen.textContent = '❙';
		this.mediumPen.setAttribute('title', 'Среднее перо');
		this.toolsBlock.appendChild(this.mediumPen);
		this.mediumPen.onclick = function () {
			this.penWidth = 5;
		}.bind(this);

		// Кнопка: Толстое перо
		this.thickPen = document.createElement('button');
		this.thickPen.textContent = '❚';
		this.thickPen.setAttribute('title', 'Толстое перо');
		this.toolsBlock.appendChild(this.thickPen);
		this.thickPen.onclick = function () {
			this.penWidth = 7;
		}.bind(this);

		// Кнопки выбора цвета (Красный, Зеленый, Синий)
		this.redPen = document.createElement('button');
		this.redPen.textContent = '🟥';
		this.redPen.setAttribute('title', 'Красный цвет');
		this.toolsBlock.appendChild(this.redPen);
		this.redPen.onclick = function () {
			this.penColor = 'red';
		}.bind(this);

		this.greenPen = document.createElement('button');
		this.greenPen.textContent = '🟩';
		this.greenPen.setAttribute('title', 'Зеленый цвет');
		this.toolsBlock.appendChild(this.greenPen);
		this.greenPen.onclick = function () {
			this.penColor = 'green';
		}.bind(this);

		this.bluePen = document.createElement('button');
		this.bluePen.textContent = '🟦';
		this.bluePen.setAttribute('title', 'Синий цвет');
		this.toolsBlock.appendChild(this.bluePen);
		this.bluePen.onclick = function () {
			this.penColor = 'blue';
		}.bind(this);

		this.penColor = 'red';
		this.penWidth = '3';
	}

	/**
	 * Запускает режим записи манипуляций и навешивает обработчик нажатия мыши.
	 */
	start() {
		super.start();
		this.demoDIV.onmousedown = this.onMouseDown;
	}

	/** Обработчик начала рисования (нажатие кнопки мыши) */
	onMouseDown(event) {
		event.stopPropagation();
		let XY = [event.offsetX, event.offsetY];
		this.fixate('beginPath');
		this.fixate('setPenColor', this.penColor);
		this.fixate('setPenWidth', this.penWidth);
		this.fixate('moveTo', this.norm(XY));
		this.demoDIV.onmousemove = this.onMouseMove;
		this.demoDIV.onmouseup = this.onMouseUp;
	}

	/** Обработчик завершения рисования линии (отпускание кнопки мыши) */
	onMouseUp(event) {
		event.stopPropagation();
		this.fixate('closePath');
		this.demoDIV.onmousemove = null;
	}

	/** Обработчик движения мыши при рисовании */
	onMouseMove(event) {
		event.stopPropagation();
		let XY = [event.offsetX, event.offsetY];
		this.fixate('lineTo', this.norm(XY));
	}

	/** Завершает режим записи и снимает обработчики событий */
	finish() {
		this.fixate('closePath');
		this.demoDIV.onmousemove = null;
		this.demoDIV.onmousedown = null;
	}
}
