// Điều khiển đóng mở menu điều hướng trên màn hình nhỏ.
document.addEventListener("DOMContentLoaded", function () {
	// Lấy các phần tử menu
	const toggleBtn = document.querySelector(".mobile-menu-toggle");
	const mainMenu = document.getElementById("mainMenu");
	const mainNav = document.querySelector(".main-nav");
	const closeBtn = document.querySelector(".mobile-menu-close");
	const backdrop = document.querySelector(".mobile-menu-backdrop");
	const currentPath = window.location.pathname.replace(/\/$/, "") || "/";

	if (mainMenu) {
		mainMenu.querySelectorAll("a[href]").forEach(function (link) {
			const linkPath = new URL(link.href, window.location.href).pathname.replace(/\/$/, "") || "/";
			const isCurrent = currentPath === linkPath || (linkPath !== "/" && currentPath.startsWith(linkPath + "/"));

			if (isCurrent) {
				link.setAttribute("aria-current", "page");
			}
		});
	}

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
		toggleBtn.addEventListener("click", function () {
			setMenuOpen(!mainNav.classList.contains("is-open"));
		});

		if (closeBtn) closeBtn.addEventListener("click", () => setMenuOpen(false));
		if (backdrop) backdrop.addEventListener("click", () => setMenuOpen(false));

		mainNav.addEventListener("keydown", function (event) {
			if (event.key === "Escape" && mainNav.classList.contains("is-open")) {
				setMenuOpen(false);
			}
		});

		window.addEventListener("resize", function () {
			if (window.innerWidth > 900 && mainNav.classList.contains("is-open")) {
				setMenuOpen(false);
			}
		});
	}
});
