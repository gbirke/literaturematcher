<script setup>
	import EntryList from "./EntryList.vue";
	const props = defineProps({
		entry: Object,
		entryIndex: Number,
		baseUrl: String
	});


	const zoteroEntryCount = Object.keys(props.entry.zoteroEntry).length;

	const classNames = [
		'entry',
		zoteroEntryCount>0 ? 'has-zotero-entry':'no-zotero-entry',
			props.entry.reloading ? 'is-reloading' : ''
	];


</script>

<template>
	<div :class="classNames.join(' ')">
		<div class="raw-line" :data-line-number="entry.lineNumber">
			<div class="reload-container">
				<button @click="$emit('reloadEntry', entry.lineNumber, entryIndex)" :disabled="entry.reloading">Reload</button>
			</div>
			{{entry.line}}
		</div>
		<div class="error" v-if="entry.error">{{entry.error}}</div>
		<div class="manual-entry" :title="entry.line">
			<EntryList :entry="entry.manualEntry" :comparison-entry="{}" />
			<p><a :href="`${baseUrl}/bibtex-entry/${entry.lineNumber}`">Download</a></p>
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

	.has-zotero-entry .raw-line {
		grid-column-start: span 2;
	}

	.error {
		background-color: red;
	}

	.raw-line {
		background-color: #eee;
	}

	.zotero-entry {
		background-color: #dfd;
	}

	.reload-container {
		float: right;
		padding: 0 0.5rem;
	}

	.is-reloading {
		opacity: 0.5;
	}

</style>
