<script setup>
import entries from './literature.json';

</script>

<template>
	<main>
		{{entries.length}} Einträge
		<table>
			<tr v-for="entry in entries" :key="entry.lineNumber">
				<td>{{entry.manualEntry}}
				</td>
				<td>{{entry.zoteroEntry}}</td>
			</tr>
		</table>

  </main>
</template>

<style scoped>
	tr {
		vertical-align: top;
	}
</style>
