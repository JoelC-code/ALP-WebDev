import "@popperjs/core";
import "bootstrap";
import "../css/app.css";
import "./bootstrap";

console.log("App.js is loaded");

import Alpine from "alpinejs";
import { initListSortable } from "./board-sortable";
import { initCardSortable } from "./card-sortable";

window.Alpine = Alpine;

Alpine.start();

//This is where you executed the boards
window.Echo.channel("boards")
    .listen("BoardDeleted", (e) => {
        console.log("Echo received BoardDeleted event:", e);
        Livewire.dispatch("board_deleted", { id: e.boardId });
    })
    .listen(".BoardRenamed", (e) => {
        console.log("Name from a board has been changed:", e);
        Livewire.dispatch("global-board-renamed", { board: e.board });
    })

//WRAP DRAGGABLE
//Ini lebih ke untuk ngasih tahu kalau ini pake
//livewire:navigated
document.addEventListener("livewire:navigated", () => {
    if (!window.boardId) { console.log("No board id"); return }
    subscribedToBoard(window.boardId)
    initListSortable(window.boardId);
    initCardSortable(window.boardId);
});

const subscribedBoard = new Set();
//Ngak usah sentuh yang listener -> if(!window.boardId), hanya untuk tes di console web mu
function subscribedToBoard(boardId) {
    if(subscribedBoard.has(boardId)) { console.log("no board id"); return }
    subscribedBoard.add(boardId);
    // Nanti bakal dibawa kesini hasilnya, terus check web mu habis ini
    window.Echo.private(`board.${window.boardId}`).listen(".CardCreated", (e) => {
        console.log("Echo received, CardCreated event:", e);
        Livewire.dispatch("card-created", { card: e.card });
    })

    .listen(".CardDeleted", (e) => {
        console.log("Echo received, CardDeleted event:", e);
        Livewire.dispatch("card-deleted", { id: e.cardId });
    })

    .listen(".ListCreated", (e) => {
        console.log("List has been added:", e);
        Livewire.dispatch("list-created", { list: e.list });
    })

    .listen(".ListDeleted", (e) => {
        console.log("List has been DELETED:", e);
        Livewire.dispatch("list-deleted", { listId: e.listId });
    })

    //NON GLOBAL EVENTS
    //hanya diwindow sedang terbuka, tidak bisa keluar
    .listen(".BoardRenamed", (e) => {
        console.log("Name has been changed for board:", e);
        Livewire.dispatch("board-renamed", { board: e.board });
    })

    .listen(".ListRenamed", (e) => {
        console.log("Name change on a list has been detected:", e);
        Livewire.dispatch("list-renamed", { list: e.list });
    })

    .listen(".CardReordered", (e) => {
        console.log("Card position change (type-2):", e);
        Livewire.dispatch("card-refreshed" , { listId: e.toListId })
    })

    .listen(".ListReordered", (e) => {
        console.log("List position change (type-1):", e)
        Livewire.dispatch("list-refreshed")
    })
    //Ini jika kau mau munculin dengan non private (Channel)
}

//! KRITERIA KEMUNCULAN
//* ke tinker terus tulis
//* event(new \App\Events\Arah\Broadcast(param))
//* Jika muncul = [] AMAN (TES 1)

//* cek browser mu, inspect element dan buka console
//* pasangin console.log() SEBELUM livewire.lister
//* panggil lagi pake tinker kayak yang awal
//* JIKA di console muncul & dan di tinker muncul = [] AMAN (TES 2)
//* berarti kamu udah aman, cek duplicatenya pake duplicate window
