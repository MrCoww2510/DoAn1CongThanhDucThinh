// Điều khiển đóng mở menu điều hướng trên màn hình nhỏ.
document.addEventListener("DOMContentLoaded", function () {
	// Lấy các phần tử menu
	const toggleBtn = document.querySelector(".mobile-menu-toggle");
	const mainMenu = document.getElementById("mainMenu");
	const mainNav = document.querySelector(".main-nav");
	const closeBtn = document.querySelector(".mobile-menu-close");
	const backdrop = document.querySelector(".mobile-menu-backdrop");
	/**
	 * Cập nhật trạng thái mở/đóng menu điều hướng và các thuộc tính trợ năng liên quan.
	 */
	function setMenuOpen(isOpen) {
		if (!toggleBtn || !mainMenu || !mainNav) return;

		mainMenu.classList.toggle("active", isOpen);
		mainNav.classList.toggle("is-open", isOpen);
		document.body.classList.toggle("menu-open", isOpen);
		toggleBtn.setAttribute("aria-expanded", String(isOpen));
		toggleBtn.setAttribute("aria-label", isOpen ? "Đóng menu" : "Mở menu");

		if (isOpen && closeBtn) {
			closeBtn.focus();
		} else if (toggleBtn && window.innerWidth <= 900) {
			toggleBtn.focus();
		}
	}

	if (toggleBtn && mainMenu && mainNav) {
		// Đảo trạng thái menu khi khách kích hoạt nút menu di động.
		toggleBtn.addEventListener("click", function () {
			setMenuOpen(!mainNav.classList.contains("is-open"));
		});

		// Đóng menu từ nút đóng hoặc lớp nền phủ.
		if (closeBtn) closeBtn.addEventListener("click", () => setMenuOpen(false));
		if (backdrop) backdrop.addEventListener("click", () => setMenuOpen(false));

        /** Đóng menu khi người dùng nhấn Escape trong vùng điều hướng đang mở. */
		mainNav.addEventListener("keydown", function (event) {
			if (event.key === "Escape" && mainNav.classList.contains("is-open")) {
				setMenuOpen(false);
			}
		});

		// Thu menu lại khi cửa sổ đổi sang kích thước màn hình lớn.
		window.addEventListener("resize", function () {
			if (window.innerWidth > 900 && mainNav.classList.contains("is-open")) {
				setMenuOpen(false);
			}
		});
	}
});
