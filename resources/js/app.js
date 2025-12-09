import '@popperjs/core';
import 'bootstrap';
import '../css/app.css';
import './bootstrap';

console.log("App.js is loaded");

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

//This is where you executed the boards
window.Echo.channel('boards').listen('BoardDeleted', (e) => {
    console.log('Echo received BoardDeleted event:', e);
    Livewire.dispatch('board_deleted', { id: e.boardId })
});
