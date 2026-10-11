document.addEventListener("DOMContentLoaded", function () {
	// Lấy các phần tử menu
	const toggleBtn = document.querySelector(".mobile-menu-toggle");
	const mainMenu = document.getElementById("mainMenu");

	// Bắt sự kiện click
	if (toggleBtn && mainMenu) {
		toggleBtn.addEventListener("click", function () {
			// Thêm/Xóa class 'active' để ẩn/hiện menu
			mainMenu.classList.toggle("active");
		});
	}
});
