import Sortable from "sortablejs";

export function initInboxSortable() {
    const inbox = document.getElementById("inboxSortable");
    if(!inbox || inbox._sortable) return;

    inbox._sortable = Sortable.create(inbox, {
        group: {
            name: "cards",
            pull: "clone",
            put: false
        },
        sort: false,
        draggable: ".inbox-card"
    })
}