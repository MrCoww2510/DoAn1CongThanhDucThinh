document.addEventListener("DOMContentLoaded", function () {
	/*
    |--------------------------------------------------------------------------
    | MOBILE MENU
    |--------------------------------------------------------------------------
    */

	const mobileMenuButton = document.getElementById("mobileMenuButton");

	const mobileMenuClose = document.getElementById("mobileMenuClose");

	const mobileNavigation = document.getElementById("mobileNavigation");

	if (mobileMenuButton && mobileNavigation) {
		mobileMenuButton.addEventListener("click", function () {
			mobileNavigation.classList.add("active");
		});
	}

	if (mobileMenuClose && mobileNavigation) {
		mobileMenuClose.addEventListener("click", function () {
			mobileNavigation.classList.remove("active");
		});
	}

	/*
    |--------------------------------------------------------------------------
    | CLOSE MOBILE MENU WHEN CLICK LINK
    |--------------------------------------------------------------------------
    */

	if (mobileNavigation) {
		const mobileLinks = mobileNavigation.querySelectorAll("a");

		mobileLinks.forEach(function (link) {
			link.addEventListener("click", function () {
				mobileNavigation.classList.remove("active");
			});
		});
	}

	/*
    |--------------------------------------------------------------------------
    | PRODUCT DROPDOWN
    |--------------------------------------------------------------------------
    */

	const productMenuButton = document.getElementById("productMenuButton");

	const productDropdown = document.getElementById("productDropdown");

	if (productMenuButton && productDropdown) {
		productMenuButton.addEventListener("click", function (event) {
			event.stopPropagation();

			const isOpen = productDropdown.style.display === "grid";

			if (isOpen) {
				productDropdown.style.display = "none";
			} else {
				productDropdown.style.display = "grid";
			}
		});

		document.addEventListener("click", function () {
			productDropdown.style.display = "none";
		});

		productDropdown.addEventListener("click", function (event) {
			event.stopPropagation();
		});
	}

	/*
    |--------------------------------------------------------------------------
    | SEARCH FORM
    |--------------------------------------------------------------------------
    */

	const searchForm = document.querySelector(".search-form");

	if (searchForm) {
		searchForm.addEventListener("submit", function (event) {
			const input = searchForm.querySelector("input[name='q']");

			if (input && input.value.trim() === "") {
				event.preventDefault();

				input.focus();
			}
		});
	}
});
