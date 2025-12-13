import "@popperjs/core";
import "bootstrap";
import "../css/app.css";
import "./bootstrap";

console.log("App.js is loaded");

import Alpine from "alpinejs";
import { list } from "postcss";
import { initListSortable } from "./board-sortable";

window.Alpine = Alpine;

Alpine.start();

//This is where you executed the boards
window.Echo.channel("boards").listen("BoardDeleted", (e) => {
    console.log("Echo received BoardDeleted event:", e);
    Livewire.dispatch("board_deleted", { id: e.boardId });
});

//GLOBAL EVENTS
//Muncul jika misalnya dikaitkan di halaman lain
//Misal board di /dashboard & /board/{id}
window.Echo.channel(`boards`).listen(".BoardRenamed", (e) => {
    console.log("Name from a board has been changed:", e);
    Livewire.dispatch("global-board-renamed", { board: e.board });
})

//WRAP DRAGGABLE
//Ini lebih ke untuk ngasih tahu kalau ini pake
//livewire:navigated
document.addEventListener("livewire:navigated", () => {
    initListSortable(window.boardId);
});

//Ngak usah sentuh yang listener -> if(!window.boardId), hanya untuk tes di console web mu
document.addEventListener("DOMContentLoaded", () => {
    console.log("boardId:", window.boardId);

    if (!window.boardId) {
        console.error("No boardId found!");
        return;
    }
    
    // Nanti bakal dibawa kesini hasilnya, terus check web mu habis ini
    window.Echo.private(`board.${window.boardId}`).listen(".CardCreated", (e) => {
        console.log("Echo received, CardCreated event:", e);
        Livewire.dispatch("card-created", { card: e.card });
    });

    window.Echo.private(`board.${window.boardId}`).listen(".CardDeleted", (e) => {
        console.log("Echo received, CardDeleted event:", e);
        Livewire.dispatch("card-deleted", { id: e.cardId });
    });

    window.Echo.private(`board.${window.boardId}`).listen(".ListCreated", (e) => {
        console.log("List has been added:", e);
        Livewire.dispatch("list-created", { list: e.list });
    });

    window.Echo.private(`board.${window.boardId}`).listen(".ListDeleted", (e) => {
        console.log("List has been DELETED:", e);
        Livewire.dispatch("list-deleted", { listId: e.listId });
    });

    //NON GLOBAL EVENTS
    //hanya diwindow sedang terbuka, tidak bisa keluar
    window.Echo.private(`board.${window.boardId}`).listen(".BoardRenamed", (e) => {
        console.log("Name has been changed for board:", e);
        Livewire.dispatch("board-renamed", { board: e.board });
    });

    window.Echo.private(`board.${window.boardId}`).listen(".ListRenamed", (e) => {
        console.log("Name change on a list has been detected:", e);
        Livewire.dispatch("list-renamed", { list: e.list });
    });
    //Ini jika kau mau munculin dengan non private (Channel)
});

//! KRITERIA KEMUNCULAN
//* ke tinker terus tulis
//* event(new \App\Events\Arah\Broadcast(param))
//* Jika muncul = [] AMAN (TES 1)

//* cek browser mu, inspect element dan buka console
//* pasangin console.log() SEBELUM livewire.lister
//* panggil lagi pake tinker kayak yang awal
//* JIKA di console muncul & dan di tinker muncul = [] AMAN (TES 2)
//* berarti kamu udah aman, cek duplicatenya pake duplicate window
