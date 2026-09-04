/**
 * Базовый абстрактный класс DemoA.
 * Служит фундаментом для всех типов Демо-объектов в системе ММ-Лекций.
 * Обеспечивает единый жизненный цикл, управление режимами записи (RECORD) и воспроизведения (PLAY),
 * а также нормировку/денормировку координат для корректного отображения на экранах с разным разрешением.
 */
export class DemoA {
	recordMode = true; // Флаг текущего режима: true - запись (RECORD), false - воспроизведение (PLAY)
	commands = [];     // Временной массив записей команд управления Демо-объектом

	/**
	 * Конструктор базового Демо-объекта.
	 * @param {Array} atrArr - Массив параметров.
	 *                         atrArr[0] (Blob) - бинарные данные демонстрации (картинка, видео и т.д.).
	 *                         atrArr[1] (Array, опционально) - список записанных команд для режима воспроизведения.
	 */
	constructor(atrArr) {
		console.log(atrArr.length);
		this.blob = atrArr[0];

		// Если переданы команды, переключаем объект в режим воспроизведения (PLAY)
		if (atrArr.length > 1) {
			this.recordMode = false;
			this.commands = atrArr[1];
			this.index = 0; // Индекс текущей исполняемой команды
			this.length = this.commands.length;
		}

		// Привязка контекста методов для безотказной работы в качестве обработчиков событий
		this.finish = this.finish.bind(this);
		this.pause = this.pause.bind(this);
		this.play = this.play.bind(this);
		this.next = this.next.bind(this);
		this.fixate = this.fixate.bind(this);
		this.execute = this.execute.bind(this);
		this.toolsShowHide = this.toolsShowHide.bind(this);
	}

	/**
	 * Отрисовывает и вписывает Демо-объект в указанный HTML-элемент родительского контейнера.
	 * Рассчитывает пропорциональные размеры (4:3) и подготавливает интерактивный холст (Canvas).
	 * @param {HTMLElement} demoElement - Родительский HTML-элемент для монтирования Демо-объекта.
	 */
	render(demoElement) {
		this.demoBlock = demoElement;
		this.demoBlock.innerHTML = '';
		this.demoBlock.style.backgroundColor = 'black';

		// Расчет размеров области демонстрации с сохранением пропорций 4:3
		let xx = Math.floor(this.demoBlock.clientWidth / 4);
		let yy = Math.floor(this.demoBlock.clientHeight / 3);

		if (xx > yy) {
			this.width = yy * 4;
			this.height = yy * 3;
		} else {
			this.width = xx * 4;
			this.height = xx * 3;
		}

		// Создаем обертку-контейнер для элементов демонстрации
		this.demoBlock.insertAdjacentHTML('beforeEnd', `
			<div id="demoDIV" style="position: relative" draggable="false"></div>
		`);

		// Вставляем слой холста Canvas для нанесения интерактивной графики
		this.demoDIV = this.demoBlock.querySelector('#demoDIV');
		this.demoDIV.insertAdjacentHTML('beforeEnd', `
			<canvas id="canvas" draggable="false" style="position: absolute; top: 0px; left: 0px; z-index: 1000"></canvas>
		`);

		this.canvas = this.demoBlock.querySelector('#canvas');
		this.canvas.width = this.width;
		this.canvas.height = this.height;
		this.context = this.canvas.getContext('2d');
	}

	/**
	 * Выполняет одну конкретную команду управления Демо-объектом.
	 * @param {Array} command - Массив команды формата: [timestamp, actionName, options]
	 */
	execute(command) {
		let action = command[1];
		let options = command[2];
		if (typeof this[action] === 'function') {
			this[action](options);
		}
	}

	/**
	 * Нормирует абсолютные пиксельные координаты экрана в относительную шкалу (0..10000).
	 * Это гарантирует одинаковое воспроизведение на устройствах с разным разрешением.
	 * @param {Array} XY - Массив [X, Y] в пикселях.
	 * @returns {Array} Массив [x, y] в относительных единицах.
	 */
	norm(XY) {
		let xy = new Array();
		xy[0] = Math.ceil((10000 * XY[0]) / this.width);
		xy[1] = Math.ceil((10000 * XY[1]) / this.height);
		return xy;
	}

	/**
	 * Переводит relative-координаты (0..10000) обратно в абсолютные пиксельные координаты текущего Canvas.
	 * @param {Array} xy - Массив [x, y] в относительных единицах.
	 * @returns {Array} Массив [X, Y] в пикселях.
	 */
	denorm(xy) {
		let XY = new Array();
		XY[0] = Math.ceil((xy[0] * this.width) / 10000);
		XY[1] = Math.ceil((xy[1] * this.height) / 10000);
		return XY;
	}

	/**
	 * Возвращает медиа-поток с Canvas (может использоваться для видеозаписи или трансляции).
	 * @returns {MediaStream} Поток данных Canvas.
	 */
	getStream() {
		return this.canvas.captureStream();
	}

	// ==========================================
	// БЛОК МЕТОДОВ ЗАПИСИ (RECORD MODE)
	// ==========================================

	/**
	 * Создает и отображает панель инструментов лектора для взаимодействия с Демо-объектом.
	 * @param {HTMLElement} toolsElement - Внешний элемент для размещения кнопок управления (опционально).
	 */
	renderTools(toolsElement) {
		if (!this.recordMode) return;

		if (toolsElement) {
			this.toolsBlock = toolsElement;
			this.toolsBlock.innerHTML = '';
		} else {
			this.demoDIV.insertAdjacentHTML('beforeEnd', `
				<div id="toolsDIV" style="
					width: 200px; height: 200px;
					position: absolute; top:0px; left: 0px; z-index: 1003;
					background-color: rgba(200, 200, 200, 0.5);
					border: 1px double black;">
				</div>
			`);
			this.toolsBlock = this.demoDIV.querySelector('#toolsDIV');
			this.toolsBlock.innerHTML = '';
			this.toolsBlock.style.display = 'none';
			this.demoDIV.oncontextmenu = this.toolsShowHide;
		}
	}

	/**
	 * Переключает видимость панели инструментов по правой кнопке мыши (контекстное меню).
	 * @param {MouseEvent} event - Событие клика мыши.
	 */
	toolsShowHide(event) {
		event.preventDefault();
		event.stopImmediatePropagation();
		if (event.button !== 2) {
			return;
		} else {
			if (this.toolsBlock.style.display === 'none') {
				this.toolsBlock.style.display = 'block';
			} else {
				this.toolsBlock.style.display = 'none';
			}
		}
	}

	/**
	 * Запускает таймер отсчета времени записи действий лектора с Демо-объектом.
	 */
	start() {
		let date = new Date();
		this.t0 = date.getTime();
	}

	/**
	 * Записывает действие пользователя в массив команд с фиксацией точного смещения по времени и сразу исполняет его.
	 * @param {string} action - Название метода/действия.
	 * @param {*} options - Параметры действия.
	 */
	fixate(action, options) {
		let command = [];
		let date = new Date();
		let t1 = date.getTime();
		command[0] = t1 - this.t0;
		command[1] = action;
		command[2] = options;
		this.commands.push(command);
		this.execute(command);
	}

	/**
	 * Завершает процесс записи действия над Демо-объектом.
	 */
	finish() {}

	/**
	 * Возвращает полный список записанных команд для сохранения в БД.
	 * @returns {Array} Массив команд.
	 */
	getCommands() {
		return this.commands;
	}

	// ==========================================
	// БЛОК МЕТОДОВ ВОСПРОИЗВЕДЕНИЯ (PLAY MODE)
	// ==========================================

	/**
	 * Запускает или возобновляет автоматическое воспроизведение сохраненного сценария команд.
	 */
	play() {
		if (this.recordMode) return;

		if (this.index === 0) {
			let command = this.commands[this.index];
			this.interval = command[0];
			this.startTime = new Date().getTime();
			this.setTimeID = setTimeout(this.next, this.interval);
		} else {
			this.interval = this.interval - (this.stopTime - this.startTime);
			this.startTime = new Date().getTime();
			this.setTimeID = setTimeout(this.next, this.interval);
		}
	}

	/**
	 * Приостанавливает воспроизведение сценария команд.
	 */
	pause() {
		if (this.recordMode) return;
		clearTimeout(this.setTimeID);
		this.stopTime = new Date().getTime();
	}

	/**
	 * Выполняет текущую команду из массива и планирует запуск следующей с нужным временным интервалом.
	 */
	next() {
		let command = this.commands[this.index];
		let t0 = command[0];
		this.execute(command);
		this.index++;
		if (this.index < this.length - 1) {
			let nextCommand = this.commands[this.index];
			let t1 = nextCommand[0];
			this.interval = t1 - t0;
			this.startTime = new Date().getTime();
			this.setTimeID = setTimeout(this.next, this.interval);
		}
	}
}
