import AccountThemeButton from "./AccountThemeButton.vue";
import FilePreview from "./FilePreviewButton.vue";
import FileSettings from "./FileSettingsButton.vue";
import ModelLanguages from "./ModelLanguagesButton.vue";
import ModelPreview from "./ModelPreviewButton.vue";
import ModelSettings from "./ModelSettingsButton.vue";
import PageStatus from "./PageStatusButton.vue";

import ModelViewButtons from "./ModelViewButtons.vue";

export default {
	install(app) {
		app.component("k-account-theme-button", AccountThemeButton);
		app.component("k-file-preview-button", FilePreview);
		app.component("k-file-settings-button", FileSettings);
		app.component("k-model-languages-button", ModelLanguages);
		app.component("k-model-preview-button", ModelPreview);
		app.component("k-model-settings-button", ModelSettings);
		app.component("k-page-status-button", PageStatus);

		app.component("k-model-view-buttons", ModelViewButtons);

		// @deprecated
		app.component("k-languages-dropdown", ModelLanguages);
	}
};
