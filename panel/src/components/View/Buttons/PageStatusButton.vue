<template>
	<k-button
		v-if="status"
		v-bind="button"
		variant="filled"
		@click="$dialog(id + '/changeStatus')"
		class="k-page-status-button"
	/>
</template>

<script>
export default {
	inheritAttrs: false,
	props: {
		id: String,
		isLocked: Boolean,
		model: {
			type: Object,
			default: () => ({})
		},
		permissions: {
			type: Object,
			default: () => ({})
		},
		status: Object
	},
	computed: {
		button() {
			return {
				...this.$helper.page.status.call(
					this,
					this.model.status,
					!this.permissions.changeStatus || this.isLocked
				),
				responsive: true,
				size: "sm",
				text: this.status.label,
				variant: "filled"
			};
		}
	}
};
</script>

<style>
.k-page-status-button {
	--button-color-back: var(--color-gray-300);
	--button-color-icon: var(--theme-color-600);
	--button-color-text: initial;
}
</style>
