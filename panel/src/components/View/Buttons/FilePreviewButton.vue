<script>
import ModelPreviewButton from "./ModelPreviewButton.vue";

export default {
	extends: ModelPreviewButton,
	props: {
		preview: Object
	},
	computed: {
		isAvailable() {
			return true;
		},
		link() {
			return this.preview.url;
		}
	}
};
</script>
