document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main');

    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('open');

        // Adjust the margin of the main content based on the sidebar state
        if (sidebar.classList.contains('open')) {
            mainContent.style.marginLeft = '400px'; // Adjust based on sidebar width
        } else {
            mainContent.style.marginLeft = '0';
        }
    });

    document.addEventListener('click', function(event) {
        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
            sidebar.classList.remove('open');
            mainContent.style.marginLeft = '0'; // Reset margin when closing the sidebar
        }
    });

    // Prevent sidebar from closing when clicking inside it
    sidebar.addEventListener('click', function(event) {
        event.stopPropagation();
    });

    // Prevent sidebar from closing when clicking the toggle button
    sidebarToggle.addEventListener('click', function(event) {
        event.stopPropagation();
    });
});
