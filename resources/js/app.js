
import { renderAsync } from 'docx-preview';
window.renderDocx = renderAsync;
import * as docx from 'docx-preview';
window.docx = docx;


import 'flowbite';

import $ from 'jquery';
window.$ = window.jQuery = $

import 'sweetalert2';
import 'preline';
import Swal from 'sweetalert2';
import { initFlowbite } from 'flowbite';
window.Swal = Swal;

document.addEventListener('livewire:navigated', () => {
    initFlowbite();

    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});
