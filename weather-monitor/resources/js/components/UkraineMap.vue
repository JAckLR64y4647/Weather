<script setup lang="ts">
import cities from '@/assets/cities.json';
import { useLocationsStore } from '@/stores/locationsStore';
import router from '@/plugins/router';

const locationsStore = useLocationsStore();
</script>

<template>
	<svg
		xmlns="http://www.w3.org/2000/svg"
		viewBox="0 0 612 408"
		class="h-auto w-full"
	>
		<path
			v-for="city in cities"
			:id="city.id"
			:key="city.id"
			:d="city.d"
			fill="currentColor"
			stroke="#FFFFFF"
			class="cursor-pointer hover:text-primary-500/50 text-surface-500 duration-200 ease-in-out"
			@click="router.push('/forecast/' + locationsStore.locations.find((location) => location.code === city.id).slug)"
		/>
	</svg>
</template>
