import { w2ui, w2grid } from '/w2ui-2.0.es6.js';

export function initGridScenarios(openPlayerWindow) {
    new w2grid({
        box: w2ui.mainLayout.el('main'), name: 'gridScenarios', url: 'api.php?action=get_scenarios', method: 'GET',
        show: { toolbar: true, footer: true },
        columns: [
            { field: 'section_id', text: 'section_id', size: '90px', sortable: true },
            { field: 'slide_id', text: 'slide_id', size: '80px', sortable: true },
            { field: 'name', text: 'Название сценария ЖД', size: '100%', sortable: true }
        ],
        toolbar: {
            items: [{ type: 'button', id: 'btn-play', text: '▶ Просмотр ЖД', icon: 'w2ui-icon-search', disabled: true }],
            onClick(ev) {
                if (ev.target === 'btn-play') {
                    const sel = w2ui.gridScenarios.getSelection();
                    if (sel.length > 0) {
                        const selectedId = sel; 
                        const rowData = w2ui.gridScenarios.get(selectedId); 
                        openPlayerWindow(rowData); // Передаем ВСЮ строку, как в студии!
                    }
                }
            }
        },
        onSelect() { setTimeout(() => w2ui.gridScenarios.toolbar.enable('btn-play'), 10); },
        onUnselect() { setTimeout(() => { if (w2ui.gridScenarios.getSelection().length === 0) w2ui.gridScenarios.toolbar.disable('btn-play'); }, 10); }
    });
}
