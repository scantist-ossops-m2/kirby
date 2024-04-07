<template>
	<div>
		<k-button
			:disabled="isLocked"
			:dropdown="true"
			:title="$t('settings')"
			icon="cog"
			size="sm"
			variant="filled"
			class="k-file-view-options"
			@click="$refs.settings.toggle()"
		/>
		<k-dropdown-content
			ref="settings"
			:options="$dropdown(id)"
			align-x="end"
			@action="action"
		/>
	</div>
</template>

<script>
export default {
	inheritAttrs: false,
	props: {
		id: String,
		isLocked: Boolean,
		preview: Object
	},
	methods: {
		action(action) {
			switch (action) {
				case "replace":
					return this.$panel.upload.replace({
						...this.preview,
						...this.model
					});
			}
		}
	}
};
</script>
