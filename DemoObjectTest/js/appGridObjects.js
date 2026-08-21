import { query, w2ui, w2grid, w2popup, w2confirm, w2alert } from '/w2ui-2.0.es6.js';

export function initGridObjects(openStudioWindow, openUploadDialog) {
    new w2grid({
        box: w2ui.mainLayout.el('left'), name: 'gridObjects', url: 'api.php?action=get_objects', method: 'GET',
        show: { toolbar: true, footer: true, toolbarAdd: true, toolbarDelete: true },
        columns: [
            { field: 'slide_id', text: 'slide_id', size: '80px', sortable: true },
            { field: 'name', text: 'Название объекта', size: '100%', sortable: true },
            { field: 'demo_type', text: 'Тип демо', size: '120px', sortable: true }
        ],
        toolbar: {
            items: [{ type: 'button', id: 'btn-record', text: '🎙 Записать ЖД', icon: 'w2ui-icon-pencil', disabled: true }],
            onClick(ev) {
                if (ev.target === 'btn-record') {
                    const sel = w2ui.gridObjects.getSelection();
                    if (sel.length > 0) {
                        const selectedId = sel[0]; 
                        const rowData = w2ui.gridObjects.get(selectedId); 
                        openStudioWindow(rowData);
                    }
                }
            }
        },
        onSelect() { setTimeout(() => w2ui.gridObjects.toolbar.enable('btn-record'), 10); },
        onUnselect() { setTimeout(() => { if (w2ui.gridObjects.getSelection().length === 0) w2ui.gridObjects.toolbar.disable('btn-record'); }, 10); },
        onAdd() { openUploadDialog(); },
        onDelete(ev) {
            ev.preventDefault(); const sel = w2ui.gridObjects.getSelection(); if (sel.length === 0) return;
            w2confirm('Удалить объект?').yes(() => {
                query('api.php', { action: 'delete_object', slide_id: sel[0] }).then(() => { w2ui.gridObjects.reload(); w2ui.gridObjects.toolbar.disable('btn-record'); });
            });
        }
    });
}

