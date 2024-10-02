import './bootstrap';

import sprintf from 'sprintf-js';
window.sprintf = sprintf.sprintf;
window.vsprintf = sprintf.vsprintf;

import moment from 'moment';
import 'moment/locale/id';
window.moment = moment;
