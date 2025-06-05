document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.live-search').forEach(searchInput => {
        // Get the container section this input is in
        const parent = searchInput.closest('.tab-content') || document;
        const items = parent.querySelectorAll('.filter-item');

        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();

            items.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const source = item.getAttribute('data-source') || '';

                if (name.includes(query) || source.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
