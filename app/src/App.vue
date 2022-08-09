<script setup>
import entries from './literature.json';
import Entry from './components/Entry.vue';

</script>

<template>
	<main>
		{{entries.length}} Einträge
		<template v-for="entry in entries" :key="entry.lineNumber">
			<Entry :entry="entry"/>
		</template>

  </main>
</template>

<style scoped>
	table {
		width: 100%;
	}
	td {
		vertical-align: top;
		width: 50%;
		max-width: 50%;
	}
</style>
