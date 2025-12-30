import "../css/app.css";
import "./bootstrap";
import { initListSortable } from "./board-sortable";
import { initCardSortable } from "./card-sortable";
import { initInboxSortable } from "./inbox-sortable";


function bootSortables(boardId) {
    initInboxSortable();
    initCardSortable(boardId);
    initListSortable(boardId)
}

Livewire.hook("message.processed", () => {
    bootSortables(window.boardId);
})

//This is where you executed the boards
window.Echo.channel("boards")
    .listen("BoardDeleted", (e) => {
        Livewire.dispatch("board_deleted", { id: e.boardId });
    })
    .listen(".BoardRenamed", (e) => {
        Livewire.dispatch("global-board-renamed", { board: e.board });
    })
    .listen(".BoardInvited", (e) => {
        Livewire.dispatch("member_added", { board: e.board });
    });

document.addEventListener("livewire:init", () => {
    bootSortables(window.boardId);
});
//WRAP DRAGGABLE
//Ini lebih ke untuk ngasih tahu kalau ini pake
//livewire:navigated
document.addEventListener("livewire:navigated", () => {
    if (!window.boardId) return;
    subscribedToBoard(window.boardId);
    bootSortables(window.boardId);
});

const subscribedBoard = new Set();
//Ngak usah sentuh yang listener -> if(!window.boardId), hanya untuk tes di console web mu
function subscribedToBoard(boardId) {
    if (subscribedBoard.has(boardId)) return;
    subscribedBoard.add(boardId);
    // Nanti bakal dibawa kesini hasilnya, terus check web mu habis ini
    window.Echo.private(`board.${window.boardId}`)
        .listen(".CardCreated", (e) => {
            Livewire.dispatch("card-created", { card: e.card });
        })

        .listen(".CardDeleted", (e) => {
            Livewire.dispatch("card-deleted", { id: e.cardId });
        })

        .listen(".ListCreated", (e) => {
            Livewire.dispatch("list-created", { list: e.list });
        })

        .listen(".ListDeleted", (e) => {
            Livewire.dispatch("list-deleted", { listId: e.listId });
        })

        //NON GLOBAL EVENTS
        //hanya diwindow sedang terbuka, tidak bisa keluar
        .listen(".BoardRenamed", (e) => {
            Livewire.dispatch("board-renamed", { board: e.board });
        })

        .listen(".ListRenamed", (e) => {
            Livewire.dispatch("list-renamed", { list: e.list });
        })

        .listen(".CardReordered", (e) => {
            Livewire.dispatch("card-refreshed", { listId: e.toListId });
        })

        .listen(".ListReordered", (e) => {
            Livewire.dispatch("list-refreshed");
        })

        .listen(".LabelSetting", (e) => {
            Livewire.dispatch("label-setting", { boardId: e.boardId });
        })

        .listen(".LabelDeleted", (e) => {
            Livewire.dispatch("label-deleted", { boardId: e.boardId });
        })

        .listen(".BoardInvited", (e) => {
            Livewire.dispatch("member_added", { board: e.board });
        })

        .listen(".BoardMemberActions", (e) => {
            console.log(
                "member action event received & exited to previous menu",
                e
            );
            Livewire.dispatch("member_action", { member: e.member });
            Livewire.dispatch("member_action_done");
        });
}

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("toggleSidebar");
    if(!btn) return;
    const key = 'sidebar-collapsed';

    if(localStorage.getItem(key) === '1') {
        document.body.classList.add('sidebar-collapsed');
    }

    btn.addEventListener("click", () => {
        document.body.classList.toggle("sidebar-collapsed");
        localStorage.setItem(
            key, document.body.classList.contains("sidebar-collapsed") ? '1' : '0'
        );
    });
});
