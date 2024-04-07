<template>
	<k-button-group>
		<component
			v-for="button in buttons"
			:key="button"
			:is="`k-${button}-button`"
			v-bind="props"
		/>
	</k-button-group>
</template>

<script>
export default {
	props: {
		buttons: Array,
		props: Object
	}
};
</script>
