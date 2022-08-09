<script setup>
	const props = defineProps({
		creators: Array
	})
</script>

<template>
	<dd>
		<div v-for="(creator, index) in creators" class="creator">
			<span>{{creator.lastName}}</span>
			<span v-if="creator.firstName">, {{creator.firstName}}</span>
		</div>
	</dd>
</template>
