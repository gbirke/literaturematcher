<script setup>
	import EntryList from "./EntryList.vue";
	const props = defineProps({
		entry: Object
	});


	const zoteroEntryCount = Object.keys(props.entry.zoteroEntry).length;
	const manualEntryCount = Object.keys(props.entry.manualEntry).length;

	const classNames = [
		'entry',
		zoteroEntryCount>0 ? 'has-zotero-entry':'no-zotero-entry',
		manualEntryCount<zoteroEntryCount ? 'bad-quality': ''
	];


</script>

<template>
	<div :class="classNames.join(' ')">
		<div class="manual-entry" :title="entry.line">
			<EntryList :entry="entry.manualEntry" />
		</div>

		<div class="zotero-entry" v-if="zoteroEntryCount>0">
			<EntryList :entry="entry.zoteroEntry" />
		</div>
	</div>
</template>

<style scoped>

.entry {
	margin-bottom: 1rem;
	border-bottom: 1px dotted #366;
}

.entry > div {
	padding: 0.5rem;
}

	.has-zotero-entry {
		display: grid;
		grid-template-columns: 50% 50%;
	}

	.zotero-entry {
		background-color: lightgreen;
	}

	.bad-quality {
		background-color: #ffffaa;
	}


</style>
