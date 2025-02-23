// Hàm để chuyển đổi giữa chế độ sáng và tối
function toggleDarkMode() {
  const html = document.documentElement;
  const isDarkMode = html.classList.toggle("dark");

  // Lưu trạng thái vào localStorage
  localStorage.setItem("darkMode", isDarkMode ? "enabled" : "disabled");

  // Cập nhật icon SVG
  updateDarkModeIcon(isDarkMode);

  // Thêm hiệu ứng chuyển màu (CSS transition)
  html.style.transition = "background-color 0.3s, color 0.3s";
}

// Hàm để cập nhật icon của dark mode
function updateDarkModeIcon(isDarkMode) {
  const svg = document.getElementById("darkModeToggleIcon");
  if (!svg) return;

  const strokeColor = isDarkMode ? "#FFFFFF" : "#000000";

  // Cập nhật nội dung SVG
  svg.innerHTML = `
    <circle cx="12" cy="12" r="6" stroke="${strokeColor}" stroke-width="1.5"></circle>
    <path d="M12 2V3" stroke="${strokeColor}" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M12 21V22" stroke="${strokeColor}" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M22 12L21 12" stroke="${strokeColor}" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M3 12L2 12" stroke="${strokeColor}" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M19.0708 4.92969L18.678 5.32252" stroke="${strokeColor}" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M5.32178 18.6777L4.92894 19.0706" stroke="${strokeColor}" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M19.0708 19.0703L18.678 18.6775" stroke="${strokeColor}" stroke-width="1.5" stroke-linecap="round"></path>
    <path d="M5.32178 5.32227L4.92894 4.92943" stroke="${strokeColor}" stroke-width="1.5" stroke-linecap="round"></path>
  `;
}

// Khi tải trang, kiểm tra trạng thái dark mode từ localStorage
document.addEventListener("DOMContentLoaded", () => {
  // Kiểm tra trạng thái dark mode từ localStorage
  const isDarkMode = localStorage.getItem("darkMode") === "enabled";

  // Nếu dark mode được bật, thêm class 'dark' vào HTML
  if (isDarkMode) {
    document.documentElement.classList.add("dark");
  }

  // Cập nhật icon nếu cần thiết
  updateDarkModeIcon(isDarkMode);
});
