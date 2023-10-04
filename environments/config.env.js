// config.env.js
import messages from './messages.json' assert {type: 'json'};
import menu from './menu.json' assert {type: 'json'};

/** definir el ambiente 
 *  desarrollo || tester || produccion
 * */
const AMBIENTE = 'desarrollo';
const MINS_DELY_FST_SEARCH = 15;
const URLS = {
    "desarrollo": { 
        campus: 'http://localhost/diseno/svg-interactive-map/', 
        api: 'http://localhost/diseno/svg-interactive-map/backend/campusdigitalrac-v2/'
    },
    "tester": {
        campus: 'http://localhost/diseno/svg-interactive-map/',
        api: 'http://localhost/diseno/svg-interactive-map/backend/campusdigitalrac-v2/'
    },
    "produccion": {
        campus: 'https://sistdesarrollo04.coppel.io/campusdigital/', 
        api: 'https://sistdesarrollo04.coppel.io/campusdigital/backend/campusdigitalrac-v2/'
    }
}
const URL_API_CAMPUS = URLS[AMBIENTE].api;
const URL_CAMPUS = URLS[AMBIENTE].campus;

const MENU_ITEMS = Object.freeze(menu);
const MESSAGES_LIST = Object.freeze(messages);

export { MINS_DELY_FST_SEARCH, URL_API_CAMPUS, URL_CAMPUS, MENU_ITEMS, MESSAGES_LIST };