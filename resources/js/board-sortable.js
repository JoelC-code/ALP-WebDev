import Sortable from "sortablejs";

export function initListSortable(boardId) {
    const wrapList = document.getElementById("list-sortable");
    if (!wrapList) return;

    Sortable.create(wrapList, {
        animation: 150,
        ghostClass: "opacity-50",

        draggable: ".list-view",

        group: {
            name: "lists",
            pull: true,
            put: false,
        },

        onEnd() {
            const orderedIds = Array.from(
                wrapList.querySelectorAll(
                    ".list-view:not(.sortable-ghost):not(.sortable-chosen)"
                )
            ).map((el) => el.dataset.listId);

            Livewire.dispatch("lists-reordered", {
                boardId,
                orderedIds,
            });
        },
    });
}
