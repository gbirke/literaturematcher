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

function onReloadEntry(lineNumber, entryIndex) {
	entries.value[entryIndex].reloading = true;
	fetch(`/api/entry/${lineNumber}`).then(response => {
		response.json().then(data => {
			entries.value[entryIndex] = data
			entries.value[entryIndex].reloading = false;
		});
	})
}

</script>

<template>
	<main>
		<div v-if="loading">
			Lade Einträge ....
		</div>
		<div class="intro">{{entries.length}} Einträge ({{zoteroCount}} in Zotero) <button @click="fetchEntries">Reload All</button></div>

		<template v-for="(entry, index) in entries" :key="entry.lineNumber">
			<Entry :entry="entry" v-on:reloadEntry="onReloadEntry" :entryIndex="index" />
		</template>

  </main>
</template>

<style scoped>
	.intro {
		margin-bottom: 1rem;
	}

	table {
		width: 100%;
	}
	td {
		vertical-align: top;
		width: 50%;
		max-width: 50%;
	}
</style>
