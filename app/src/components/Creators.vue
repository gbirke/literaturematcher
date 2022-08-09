<script setup>
	const props = defineProps({
		creators: Array
	})
</script>

<template>
	<template v-for="creator in creators" class="creator">
		<dt>{{creator.creatorType}}</dt>
		<dd>
			<span>{{creator.lastName}}</span>
			<span v-if="creator.firstName">, {{creator.firstName}}</span>
		</dd>
	</template>
</template>

<style scoped>

dt {
	font-weight: 500;
	padding-right: 1rem;
}

</style>
