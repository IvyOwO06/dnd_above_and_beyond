function showTabFromHash() {
    const hash = window.location.hash || '#general';
    const tabs = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.classList.remove('active');
    });

    const activeTab = document.querySelector(hash);
    if (activeTab) {
        activeTab.classList.add('active');
    }
}

window.addEventListener('DOMContentLoaded', () => {
    showTabFromHash();
    // Prevent scroll on hash change
    document.querySelectorAll('.tab-links a').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            history.pushState(null, '', link.getAttribute('href'));
            showTabFromHash();
        });
    });
});

window.addEventListener('hashchange', showTabFromHash);