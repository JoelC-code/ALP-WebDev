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

            //Event object, evt untuk dapat mindah mindah datanya
            onEnd(evt) {
                const fromListId = evt.from.dataset.listId;
                const toListId = evt.to.dataset.listId;
                const cardId = evt.item.dataset.cardId;

                const orderedIds = Array.from(
                    evt.to.querySelectorAll(
                        ".card-items:not(.sortable-ghost):not(.sortable-chosen)"
                    )
                ).map((el) => el.dataset.cardId);

                console.log('Card is moved: ', orderedIds)

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
        console.log("Card has been moved: ", e);
        Livewire.dispatch("card-ordered");
    });
}
