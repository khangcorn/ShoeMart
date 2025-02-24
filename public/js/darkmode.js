function toggleDarkMode() {
  const html = document.documentElement;
  const isDarkMode = html.classList.toggle("dark");

  localStorage.setItem("darkMode", isDarkMode ? "enabled" : "disabled");

  updateDarkModeIcon(isDarkMode);

  html.style.transition = "background-color 0.5s, color 0.5s";
}


function updateDarkModeIcon(isDarkMode) {
  const svg = document.getElementById("darkModeToggleIcon");
  if (!svg) return;

 
  svg.innerHTML = `
    <circle cx="12" cy="12" r="6" stroke="#000000" stroke-width="1.5"></circle>
    <path d="M12 2V3" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M12 21V22" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M22 12L21 12" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M3 12L2 12" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M19.0708 4.92969L18.678 5.32252" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M5.32178 18.6777L4.92894 19.0706" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M19.0708 19.0703L18.678 18.6775" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M5.32178 5.32227L4.92894 4.92943" stroke="#000000" stroke-width="1.5" stroke-linecap="round"></path>
  `;
}

document.addEventListener("DOMContentLoaded", () => {
  const isDarkMode = localStorage.getItem("darkMode") === "enabled";

  if (isDarkMode) {
    document.documentElement.classList.add("dark");
  }

  updateDarkModeIcon(isDarkMode);
});
