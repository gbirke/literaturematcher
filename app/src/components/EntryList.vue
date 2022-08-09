<script setup>
	const props = defineProps({
		entry: Object
	});

	const skipKeys = {
		key: 1,
		version: 1
	}

</script>

<template>
	<dl>
		<template v-for="(value, key) in entry" :key="key">
			<template v-if="value && !skipKeys[key]">
				<dt>{{key}}</dt>
				<dd>{{value}}</dd>
			</template>
		</template>
	</dl>
</template>

<style scoped>
	dl {
		display: grid;
		grid-template-columns: max-content 1fr;
	}

	dt {
		font-weight: 500;
		padding-right: 1rem;
	}

</style>
