import './bootstrap';
import.meta.glob(["../images/**"]);

import sprintf from 'sprintf-js';
window.sprintf = sprintf.sprintf;
window.vsprintf = sprintf.vsprintf;

import moment from 'moment/min/moment-with-locales';
window.moment = moment;
