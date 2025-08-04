document.addEventListener('DOMContentLoaded', function() {
    let currentPath = window.location.pathname.replace(/\/$/, '');

    // Loop semua link sidebar
    document.querySelectorAll('.sidebar-link').forEach(function(link) {
        let linkHref = link.getAttribute('href');
        if (!linkHref) return;

        // Handle absolute dan relative href
        let hrefPath = linkHref.replace(/\/$/, '');
        // Cek jika akhir url path sama dengan akhir href
        if (currentPath.endsWith(hrefPath.split('/').filter(Boolean).pop())) {
            link.classList.add('active');
            // Buka parent submenu kalau ada
            let parentTree = link.closest('.has-sub');
            if (parentTree) {
                parentTree.classList.add('submenu-open');
                let parentLink = parentTree.querySelector(':scope > .sidebar-link');
                if(parentLink) parentLink.classList.add('active');
            }
        }
    });
});
