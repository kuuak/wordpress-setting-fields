const SELECTOR_NICE_UI_DROPDOWN = '.wsfd-nice-ui-dropdown';

function formatLabel(data, ...rest) {
	return data.element && data.element.dataset && data.element.dataset.label
		? window.jQuery(
				`<p class="wsfd-dropdown-option-with-label"><span>${data.text}</span> <em>${data.element.dataset.label}</em></p>`
		  )
		: data.text;
}

function niceUiDropdown() {
	Array.from(document.querySelectorAll(SELECTOR_NICE_UI_DROPDOWN)).forEach(
		(el) => {
			window.jQuery(el).select2({
				templateResult: formatLabel,
				placeholder: el.dataset.placeholder ?? null,
			});
		}
	);
}

document.addEventListener('DOMContentLoaded', () => {
	niceUiDropdown();
});
