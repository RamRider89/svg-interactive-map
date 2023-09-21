// config.env.js
import messages from './messages.json' assert {type: 'json'};
import menu from './menu.json' assert {type: 'json'};

const MINS_DELY_FST_SEARCH = 15;
const URL_API_CAMPUS = 'http://localhost/diseno/svg-interactive-map/backend/campusdigitalrac-v2/';
const URL_CAMPUS = 'http://localhost/diseno/svg-interactive-map/';
//const URL_API_CAMPUS = 'https://sistdesarrollo04.coppel.io/campusdigital/backend/campusdigitalrac-v2/';
//const URL_CAMPUS = 'https://sistdesarrollo04.coppel.io/campusdigital/';

const MENU_ITEMS = Object.freeze(menu);
const MESSAGES_LIST = Object.freeze(messages);

export { MINS_DELY_FST_SEARCH, URL_API_CAMPUS, URL_CAMPUS, MENU_ITEMS, MESSAGES_LIST };