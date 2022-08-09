<script setup>
import {ref, computed} from "vue";
import Entry from './components/Entry.vue';

const loading = ref(true);
const entries = ref([]);

function fetchEntries() {
	loading.value = true;
	// TODO avoid hard-coding
	fetch('/api/entries').then(response => {
		response.json().then(data => {
			entries.value = data
			loading.value = false;
		});
	})
}

fetchEntries();

const zoteroCount = computed(() => entries.value.filter(entry => Object.keys(entry.zoteroEntry).length > 0).length )

</script>

<template>
	<main>
		<div v-if="loading">
			Lade Einträge ....
		</div>
		<div>{{entries.length}} Einträge ({{zoteroCount}} in Zotero)</div>
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
