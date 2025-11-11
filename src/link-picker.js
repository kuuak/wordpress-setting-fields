(function ($) {
	"use strict";

	const SELECTOR_LINK_FIELD = ".wsfd-link-field";
	const LINK_TEXTAREA_ID = "wsfd_link_textarea";

	/**
	 * Update the link block display
	 */
	function updateLinkDisplay($field, url, text, target) {
		const $buttonWrapper = $field.find(".wsfd-link-field__button-wrapper");
		const $linkBlock = $field.find(".wsfd-link-field__link-block");
		const $linkText = $field.find(".wsfd-link-field__link-text");
		const $linkUrl = $field.find(".wsfd-link-field__link-url");
		const $urlInput = $field.find('input[name$="[url]"]');
		const $textInput = $field.find('input[name$="[text]"]');
		const $targetInput = $field.find('input[name$="[target]"]');

		if (url) {
			// Update hidden inputs
			$urlInput.val(url);
			$textInput.val(text);
			$targetInput.val(target);

			// Update display
			$linkText.text(text || url);
			$linkUrl.attr("target", target === "_blank" ? "_blank" : "").text(url);

			// Show link block, hide button
			$buttonWrapper.hide();
			$linkBlock.show();
		} else {
			// Clear inputs
			$urlInput.val("");
			$textInput.val("");
			$targetInput.val("");

			// Hide link block, show button
			$linkBlock.hide();
			$buttonWrapper.show();
		}
	}

	/**
	 * Initialize link picker for a single field
	 */
	function initLinkPicker($field) {
		const $urlInput = $field.find('input[name$="[url]"]');
		const $textInput = $field.find('input[name$="[text]"]');
		const $targetInput = $field.find('input[name$="[target]"]');
		const $picker = $field.find(".wsfd-link-field__button-wrapper button");
		const $editBtn = $field.find(".wsfd-link-field__action.-edit");
		const $removeBtn = $field.find(".wsfd-link-field__action.-remove");

		// Need at least the picker button or action buttons to initialize
		if (!$picker.length && !$editBtn.length) {
			return;
		}

		/**
		 * Open the link picker modal
		 */
		function openLinkPicker() {
			// Open the modal
			if (typeof wpLink !== "undefined") {
				const $linkTextarea = $(
					`<textarea id="${LINK_TEXTAREA_ID}" style="display:none;" data-field-id="${$field.attr(
						"data-field-id"
					)}"></textarea>`
				);
				$("body").append($linkTextarea);

				// Store current values before opening
				const currentUrl = $urlInput.val() || "";
				const currentText = $textInput.val() || "";
				const currentTarget = $targetInput.val() === "_blank";

				try {
					wpLink.open(LINK_TEXTAREA_ID, currentUrl, currentText);

					// Prefill the modal with current values after a short delay
					setTimeout(function () {
						const $urlField = $("#wp-link-url");
						const $textField = $("#wp-link-text");
						const $targetField = $("#wp-link-target");

						if ($urlField.length) $urlField.val(currentUrl);
						if ($textField.length) $textField.val(currentText);
						if ($targetField.length)
							$targetField.prop("checked", currentTarget);
					}, 100);
				} catch (e) {
					console.error("Error opening wpLink:", e);
				}
			}
		}

		// Open link picker when clicking the button or edit icon
		if ($picker.length) {
			$picker.on("click", function (e) {
				e.preventDefault();
				openLinkPicker();
			});
		}

		if ($editBtn.length) {
			$editBtn.on("click", function (e) {
				e.preventDefault();
				openLinkPicker();
			});
		}

		// Remove link when clicking the remove icon
		if ($removeBtn.length) {
			$removeBtn.on("click", function (e) {
				e.preventDefault();
				updateLinkDisplay($field, "", "", "");
			});
		}
	}

	// Set up global event handlers for the link modal (only once, outside initLinkPicker)
	// When the user clicks "Submit" in the modal, copy the values back to our inputs
	$(document).on("click", "#wp-link-submit", function (e) {
		e.preventDefault();

		const $textareaRef = $(`#${LINK_TEXTAREA_ID}`);

		if (!$textareaRef) {
			console.error("No textarea reference found!");
			return;
		}

		const $currentField = $(
			`[data-field-id="${$textareaRef.data("fieldId")}"]`
		);

		if (!$currentField || !$currentField.length) {
			console.error(
				"No field reference found!",
				"Module var:",
				$currentField,
				"Modal data:",
				$("#wp-link-wrap").data("wsfd-link-field")
			);
			return;
		}

		const url = $("#wp-link-url").val() || "";
		const text = $("#wp-link-text").val() || "";
		const target = $("#wp-link-target").is(":checked") ? "_blank" : "";

		// Use updateLinkDisplay to properly update both hidden inputs and visual display
		updateLinkDisplay($currentField, url, text, target);

		if (typeof wpLink !== "undefined") {
			wpLink.close();
		}
	});

	// Handle cancel button click
	$(document).on("click", "#wp-link-cancel button", function (e) {
		e.preventDefault();
		if (typeof wpLink !== "undefined") {
			wpLink.close();
		}

		const $textareaRef = $(`#${LINK_TEXTAREA_ID}`);
		if (!$textareaRef) {
			console.error("No textarea reference found!");
			return;
		}
	});

	// Also handle the modal close event
	$(document).on("wplink-close", function () {
		const $textareaRef = $(`#${LINK_TEXTAREA_ID}`);
		if (!$textareaRef) {
			console.error("No textarea reference found!");
			return;
		}

		// Clear textarea reference,
		// run after a short delay to ensure we correctly processed the data before removing the textarea.
		setTimeout(() => $textareaRef.remove(), 200);
	});

	/**
	 * Initialize all link picker fields on the page
	 */
	function initAllLinkPickers() {
		$(SELECTOR_LINK_FIELD).each(function () {
			initLinkPicker($(this));
		});
	}

	// Initialize on DOM ready
	$(document).ready(function () {
		initAllLinkPickers();
	});

	// Also initialize dynamically added fields (for AJAX-loaded content)
	$(document).on("wsfd-link-picker-init", function () {
		initAllLinkPickers();
	});
})(jQuery);
