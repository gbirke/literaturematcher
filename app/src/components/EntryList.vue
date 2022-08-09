<script setup>
import Creators from './Creators.vue';
	const props = defineProps({
		entry: Object
	});

	const skipKeys = {
		key: 1,
		version: 1,
		abstractNote: 1,
		collections: 1,
		relations: 1,
		dateAdded: 1,
		dateModified: 1,
		tags: 1,
	}

</script>

<template>
	<dl>
		<template v-for="(value, key) in entry" :key="key">
			<template v-if="value && !skipKeys[key]">
				<dt>{{key}}</dt>
				<dd v-if="key !== 'creators'">{{value}}</dd>
				<Creators v-else :creators="value" />
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
