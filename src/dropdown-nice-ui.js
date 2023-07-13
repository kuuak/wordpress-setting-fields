const SELECTOR_NICE_UI_DROPDOWN = '.wsfd-nice-ui-dropdown';

function niceUiDropdown() {
	Array.from(document.querySelectorAll(SELECTOR_NICE_UI_DROPDOWN)).forEach(
		(el) => {
			window.jQuery(el).select2({});
		}
	);
}

document.addEventListener('DOMContentLoaded', () => {
	niceUiDropdown();
});
