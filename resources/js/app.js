console.log("app.js is on")
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
        if (window.boardId && Number(window.boardId) === Number(e.boardId)) {
            window.location.reload();
        }
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
            Livewire.dispatch("card-refreshed", { card: e.card });
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
        
        .listen(".card.template.updated", (e) => {
            console.log('Template update received:', e.action, e.template);
            Livewire.dispatch("template-action", { 
                action: e.action, 
                template: e.template 
            });
        })

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
            Livewire.dispatch("member_action", { member: e.member });
            Livewire.dispatch("member_action_done");

            if (
                e.member?.id ===
                Number(document.querySelector('meta[name="user-id"]')?.content)
            ) {
                window.location.reload();
            }
        })

        .listen(".CustomFieldBoard", (e) => {
            Livewire.dispatch("field-updated");
            Livewire.dispatch("refresh-fields");
        })

        .listen(".CardTemplateUpdated", (e) => {
            console.log("Card template has been changed ",e);
            Livewire.dispatch('template-saved');
        });
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

            el.textContent = toast.message;

            el.className = `sb-toast sb-toast-${toast.type}`;

            container.classList.remove("hidden");

            setTimeout(() => {
                container.classList.add("hidden");
            }, 5000);
        }
    );
}

let currentCardChannel = null;

Livewire.on("card-entering", ({ cardId }) => {

    if (!cardId) {
        return;
    }

    const channel = `card.${cardId}`;

    if (currentCardChannel === channel) return;

    if (currentCardChannel) {
        window.Echo.leave(currentCardChannel);
    }

    currentCardChannel = channel;

    window.Echo.private(`card.${cardId}`)
        .listen(".CommentActions", (e) => {
            Livewire.dispatch("comment-action");
            currentCardChannel = null;
        })

        .listen(".CustomFieldCard", (e) => {
            Livewire.dispatch("refresh-fields");
            currentCardChannel = null;
        })
        
        .listen(".LabelCardsAction", (e) => {
            Livewire.dispatch("label-saved");
            currentCardChannel = null;
        })
});

Livewire.on("card-leaving", () => {
    if (!currentCardChannel) return;

    window.Echo.leave(currentCardChannel);
    currentCardChannel = null;
});

Livewire.on("open-card-from-sidebar", ({ cardId }) => {
    Livewire.dispatch("open-card-modal", { cardId: cardId });
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

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("toggleRightSidebar");
    if (!btn) return;

    const key = "right-sidebar-collapsed";

    if (localStorage.getItem(key) === "1") {
        document.body.classList.add("right-sidebar-collapsed");
    }

    btn.addEventListener("click", () => {
        document.body.classList.toggle("right-sidebar-collapsed");
        localStorage.setItem(
            key,
            document.body.classList.contains("right-sidebar-collapsed")
                ? "1"
                : "0"
        );
    });
});
