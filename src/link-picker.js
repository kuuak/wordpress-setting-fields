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
		const $externalBtn = $field.find(".wsfd-link-field__action.-external");
		const $linkUrl = $field.find(".wsfd-link-field__link-url");

		// Need at least the picker button or action buttons to initialize
		if (!$picker.length && !$editBtn.length) {
			return;
		}

		/**
		 * Ensure wpLink is initialized
		 * The modal HTML is output via PHP in admin_footer, but wpLink.init() needs to be called
		 * to set up the event handlers and input references
		 */
		function ensureWpLinkInitialized() {
			if (typeof wpLink === "undefined") {
				return false;
			}

			// Check if modal HTML exists (should be output by PHP)
			if (!$("#wp-link-wrap").length || !$("#wp-link").length) {
				return false;
			}

			// If wpLink hasn't been initialized yet, initialize it
			// wpLink.init() sets up inputs object and event handlers
			if (!wpLink.inputs || !wpLink.inputs.url || !wpLink.inputs.url.length) {
				if (typeof wpLink.init === "function") {
					wpLink.init();
				} else {
					return false;
				}
			}

			return true;
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

				// Ensure wpLink is properly initialized before opening
				let attempts = 0;
				const maxAttempts = 20;
				const tryOpen = function () {
					if (ensureWpLinkInitialized()) {
						// wpLink is initialized, open the modal using the hidden editor textarea
						// wpLink.open() requires a textarea element (not an input) because it needs
						// selectionStart, selectionEnd, and focus() methods
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
					} else if (attempts < maxAttempts) {
						// wpLink not ready yet, try again
						attempts++;
						setTimeout(tryOpen, 50);
					} else {
						console.error(
							"wpLink modal failed to initialize after multiple attempts"
						);
					}
				};

				// Start trying to open
				tryOpen();
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

		// Open link in new tab when clicking external icon
		if ($externalBtn.length && $linkUrl.length) {
			$externalBtn.on("click", function (e) {
				e.preventDefault();
				const url = $linkUrl.attr("href");
				if (url) {
					window.open(url, "_blank", "noopener,noreferrer");
				}
			});
		}

		// Prevent navigation when clicking the link URL itself - use external button instead
		if ($linkUrl.length) {
			$linkUrl.on("click", function (e) {
				e.preventDefault();
				// Optionally trigger the external button click
				if ($externalBtn.length) {
					$externalBtn.trigger("click");
				}
			});
		}
	}

	// Set up global event handlers for the link modal (only once, outside initLinkPicker)
	// When the user clicks "Submit" in the modal, copy the values back to our inputs
	$(document).on("click", "#wp-link-submit", function (e) {
		e.preventDefault();

		const currentTextareaReference = $(`#${LINK_TEXTAREA_ID}`);

		if (!currentTextareaReference) {
			console.error("No textarea reference found!");
			return;
		}

		const $currentField = $(
			`[data-field-id="${currentTextareaReference.data("fieldId")}"]`
		);

		if (!$currentField || !$currentField.length) {
			console.error(
				"No field reference found!",
				"Module var:",
				currentFieldReference,
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

		// Clear textarea reference
		$("body").remove(currentTextareaReference);
	});

	// Handle cancel button click
	$(document).on("click", "#wp-link-cancel button", function (e) {
		e.preventDefault();
		if (typeof wpLink !== "undefined") {
			wpLink.close();
		}

		const currentTextareaReference = $(`#${LINK_TEXTAREA_ID}`);
		if (!currentTextareaReference) {
			console.error("No textarea reference found!");
			return;
		}

		// Clear textarea reference
		$("body").remove(currentTextareaReference);
	});

	// Also handle the modal close event
	$(document).on("wplink-close", function () {
		const currentTextareaReference = $(`#${LINK_TEXTAREA_ID}`);
		if (!currentTextareaReference) {
			console.error("No textarea reference found!");
			return;
		}

		// Clear textarea reference
		$("body").remove(currentTextareaReference);
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
