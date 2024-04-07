<template>
	<div>
		<k-button
			:disabled="isLocked === true"
			:dropdown="true"
			:title="$t('settings')"
			icon="cog"
			variant="filled"
			size="sm"
			class="k-page-view-options"
			@click="$refs.settings.toggle()"
		/>
		<k-dropdown-content ref="settings" :options="$dropdown(id)" align-x="end" />
	</div>
</template>

<script>
export default {
	inheritAttrs: false,
	props: {
		id: String,
		isLocked: Boolean
	}
};
</script>
