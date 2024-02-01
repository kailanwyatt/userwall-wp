export default function InfiniteScroll(contentId, callback) {
    let page = 1;
    const content = document.getElementById(contentId);
    const loadingIndicator = document.getElementById('loading');
    const itemsPerPage = 10;
    let isLoading = false;

    function loadMoreItems() {
        if (isLoading) return;
        isLoading = true;
        loadingIndicator.style.display = 'block';

        // Use the provided callback function to load more items via AJAX
        callback(page, itemsPerPage, (newItems) => {
            for (let i = 0; i < newItems.length; i++) {
                const newItem = document.createElement('div');
                newItem.className = 'item';
                newItem.textContent = newItems[i];
                content.appendChild(newItem);
            }
            loadingIndicator.style.display = 'none';
            isLoading = false;
            page++;
        });
    }

    content.addEventListener('scroll', function () {
        if (
            content.scrollTop + content.clientHeight >= content.scrollHeight &&
            !isLoading
        ) {
            loadMoreItems();
        }
    });
}
