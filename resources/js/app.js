console.log("🔥 app.js loaded");

import "../css/app.css";
import "./bootstrap";
import { initListSortable } from "./board-sortable";
import { initCardSortable } from "./card-sortable";
import { initInboxSortable } from "./inbox-sortable";

function bootSortables(boardId) {
    initInboxSortable();
    initCardSortable(boardId);
    initListSortable(boardId);
}

Livewire.hook("message.processed", () => {
    bootSortables(window.boardId);
});

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
    })
    .listen(".BoardMemberActions", (e) => {
        Livewire.dispatch("member_disconnect", { board: e.board });
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
        .listen(".CardActions", (e) => {
            console.log("Card Does Something ", e);
            Livewire.dispatch("card-refreshed", { card: e.card });
            Livewire.dispatch("card-inside-refresh");
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
        })

        .listen(".CustomFieldBoard", (e) => {
            console.log("Testing for the board's update on the custom fields ", e)
            Livewire.dispatch('field-updated');
        })
}

const userId = document
    .querySelector('meta[name="user-id"]')
    ?.getAttribute("content");
const root = document.getElementById("toast-root");

if (userId && window.Echo && root) {
    window.Echo.private(`user.${userId}`).listen(
        ".BoardMemberToast",
        (toast) => {
            const container = document.querySelector(
                "#toast-root .toast-container"
            );
            const el = document.querySelector(".sb-toast");
            console.log("TOAST RECEIVED: ", toast);

            el.textContent = toast.message;
            console.log(el.textContent);

            el.className = `sb-toast sb-toast-${toast.type}`;
            console.log("Adding class to the toast");

            container.classList.remove("hidden");
            console.log("Toast should be shown now");

            setTimeout(() => {
                container.classList.add("hidden");
            }, 5000);
        }
    );
}

let currentCardChannel = null;

Livewire.on("card-entering", ({ cardId }) => {
    console.log("Received card id in JS:", cardId);

    if(!cardId) {
        console.log("The id for card is null ", cardId);
        return;
    }

    const channel = `card.${cardId}`;

    if (currentCardChannel === channel) return;

    if (currentCardChannel) {
        window.Echo.leave(currentCardChannel);
    }

    currentCardChannel = channel;

    window.Echo.private(`card.${cardId}`).listen(".CommentActions", (e) => {
        Livewire.dispatch("comment-action");
        currentCardChannel = null;
    })
    
    .listen('.CustomFieldCard', (e) => {
        console.log("Changed, delete or make a new custom field inside a card");
        Livewire.dispatch("refresh-fields");
        currentCardChannel = null;
    });
});

Livewire.on("card-leaving", () => {
    if (!currentCardChannel) return;

    window.Echo.leave(currentCardChannel);
    currentCardChannel = null;
});

Livewire.on('open-card-from-sidebar', ({ cardId }) => {
    Livewire.dispatch('open-card-modal', { cardId: cardId });
});

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("toggleSidebar");
    if (!btn) return;
    const key = "sidebar-collapsed";

    if (localStorage.getItem(key) === "1") {
        document.body.classList.add("sidebar-collapsed");
    }

    btn.addEventListener("click", () => {
        document.body.classList.toggle("sidebar-collapsed");
        localStorage.setItem(
            key,
            document.body.classList.contains("sidebar-collapsed") ? "1" : "0"
        );
    });
});
