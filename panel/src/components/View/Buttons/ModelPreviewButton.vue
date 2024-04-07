<template>
	<k-button
		v-if="isAvailable"
		:link="link"
		:title="$t('open')"
		class="k-model-preview-button"
		icon="open"
		size="sm"
		target="_blank"
		variant="filled"
	/>
</template>

<script>
export default {
	props: {
		model: {
			type: Object,
			default: () => ({})
		},
		permissions: Object
	},
	computed: {
		isAvailable() {
			if (!this.model.previewUrl) {
				return false;
			}

			if (Object.hasOwn(this.permissions, "preview") === false) {
				return true;
			}

			return this.permissions.preview;
		},
		link() {
			return this.model.previewUrl;
		}
	}
};
</script>
