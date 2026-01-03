import Sortable from "sortablejs";

export function initCardSortable(boardId) {
    const wrapCards = document.querySelectorAll(".card-sortable");
    if (!wrapCards.length) return;

    wrapCards.forEach((wrapCards) => {
        //init card sortable tidak terpanggil 2x
        if (wrapCards._sortable) return;

        const listId = wrapCards.dataset.listId;

        listenToList(listId);

        wrapCards._sortable = Sortable.create(wrapCards, {
            animation: 150,
            ghostClass: "opacity-50",

            draggable: ".card-items",
            filter: ".no-sort",

            group: {
                name: "cards",
                pull: true,
                put: ["cards"],
            },

            onAdd(evt) {
                const item = evt.item;

                if(item.dataset.inboxId) {
                    const title = item.querySelector("p")?.innerText.trim();
                    const toListId = evt.to.dataset.listId;

                    if(!title || !toListId) {
                        item.remove();
                        return;
                    }

                    Livewire.dispatch("inbox-dropped", {
                        inboxId: item.dataset.inboxId,
                        title,
                        listId: toListId
                    });

                    item.remove();
                }
            },

            //Event object, evt untuk dapat mindah mindah datanya
            onEnd(evt) {
                const item = evt.item;

                if(item.dataset.inboxId) return;

                const fromListId = evt.from.dataset.listId;
                const toListId = evt.to.dataset.listId;
                const cardId = item.dataset.cardId;

                const orderedIds = Array.from(
                    evt.to.querySelectorAll(
                        ".card-items:not(.sortable-ghost):not(.sortable-chosen)"
                    )
                ).map((el) => el.dataset.cardId);

                Livewire.dispatch("cards-reordered", {
                    cardId,
                    fromListId,
                    toListId,
                    orderedIds,
                });
            },
        });
    });
}

function listenToList(listId) {
    window.Echo.private(`list.${listId}`).listen(".CardReordered", (e) => {
        Livewire.dispatch("card-ordered");
    });
}
