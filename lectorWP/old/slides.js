import {w2popup, query, w2form, w2ui, w2alert, w2sidebar, w2grid, w2utils } from "/lib/w2ui-2.0.es6.js"
import {getClasses, getClass, newClass, setClass, deleteClass} from "/lib/dbm.js"

export let slidesGrid = new w2grid({
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

export async function getSlidesData () {
	let classes = await getClasses()
	let arr = []
	for (let i=0; i<slides.length; i++) {
		let obj = {};
		let mySlide = slides[i]
		obj.recid = i
		obj.Slide_ID = myClass.Slide_ID
		obj.Title = mySlide.Title
		obj.Notes = mySlide.Notes
		arr.push(obj);
	}	
	slidesGrid.records = arr;
	slidesGrid.refresh();
}
/*
async function onAddClass (event) {
	slideForm.header = 'Создание нового Класса'
	slideForm.unlock()
	slideForm.fields[0].disabled = false
	slideForm.clear()
	slideForm.recid = -1
  w2ui.slideForm.message('Для нового Класса введите уникальный код')
}

async function onEditClass (event) {
	slideForm.header = 'Редактирование Класса'
	slideForm.unlock()
	slideForm.fields[0].disabled = true; 
	slideForm.refresh()
	let sel = slidesGrid.getSelection () 
	let res = slidesGrid.records [sel[0]]
	slideForm.recid = res.recid	
}

async function onDeleteClass (event) {
	slideForm.header = 'Удаление Класса'
	slideForm.refresh()
	if (event.detail.force) {
		let sel = slidesGrid.getSelection () 
		if (sel.length == 1) {
			let record = slidesGrid.records[sel[0]];
			if (! await deleteClass (record.Slide_ID)) {	console.log ('error deleteClass');}
		}
		getClassesData ()
		slideForm.clear()} 
}
*/
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

export let slideForm = new w2form ({
	header: 'Просмотр Слайда',
	name: 'slideForm',
	fields: [
		{ field: 'Slide_ID', type: 'text', required: true, html: { label: 'Slide_ID', attr: 'size="40" maxlength="40"' } },
		{ field: 'Title', type: 'text', required: true, html: { label: 'Title', attr: 'size="40" maxlength="40"' } },						
		{ field: 'Notes', type: 'textarea', html: { label: 'Notes', attr: 'style="width: 300px; height: 60px"' } }
	],
	actions: {
		Reset() {
			this.clear();
			this.lock();
			this.fields[0].diabled = false
		},
		Save: onSaveClass
	}
});
/*
async function onSaveClass () {
	let errors = slideForm.validate()
	if (errors.length > 0) return
	let classObj = {}
	if (slideForm.recid < 0) { 
		let res = await newClass (slideForm.record.Slide_ID)
		let res2 = await setClass (slideForm.record)
		slidesGrid.add(w2utils.extend(slideForm.record, { recid: slidesGrid.records.length}))
	} else {
		let res = await setClass (slideForm.record)
		slidesGrid.set(slideForm.recid, slideForm.record)
	}
	slideForm.fields[0].disabled = false; 
	slidesGrid.selectNone()
	getClassesData ()
	slideForm.clear()
	slideForm.lock()
	slideForm.header = 'Просмотр Класса'
}
*/	

	


