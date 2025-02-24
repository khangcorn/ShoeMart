document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");

    if (!toggleBtn || !sidebar || !mainContent) return; 

    toggleBtn.addEventListener("click", function () {
        sidebar.classList.toggle("hidden");

        if (sidebar.classList.contains("hidden")) {
            mainContent.classList.remove("w-4/5");
            mainContent.classList.add("w-full");
        } else {
            mainContent.classList.remove("w-full");
            mainContent.classList.add("w-4/5");
        }
    });
});
