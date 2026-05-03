import './bootstrap';

import 'bootstrap';

/* ===========================
   jQuery UI (AQUÍ va)
=========================== */

import 'jquery-ui-dist/jquery-ui';
import 'jquery-ui-dist/jquery-ui.css';

/* Alpine */

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/* DataTables */

import 'datatables.net';
import 'datatables.net-bs5';

/* Dropzone */

import Dropzone from 'dropzone';

window.Dropzone = Dropzone;

/* FullCalendar */

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin
};