<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { Location } from '@/types';
import echarts from '@/plugins/echarts';
import { useLocationsStore } from '@/stores/locationsStore';
import { format } from 'date-fns';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useEventListener } from '@vueuse/core';

const props = defineProps<{
	selectedLocation: Location;
}>();

const locationsStore = useLocationsStore();

const weather = computed(() => props.selectedLocation.weather ?? []);

onMounted(async () => {
	await locationsStore.locationsPromise;
	await locationsStore.weatherPromise;

	const chart = echarts.init(document.getElementById('hourlyForecastChart'));

	useEventListener(window, 'resize', () => chart.resize());

	chart.setOption({
		tooltip: { trigger: 'axis', className: 'dark:!bg-surface-900 dark:!border-surface-800' },
		legend: {
			data: ['Temperature', 'Humidity', 'Wind Speed', 'Pressure'],
			// With pressure enabled - data is offset too much, so it is disabled by default.
			selected: { 'Pressure': false },
		},
		grid: {
			left: '2%',
			right: '4%',
			bottom: '12%',
			containLabel: true
		},
		toolbox: { feature: { saveAsImage: {} } },
		xAxis: {
			type: 'category',
			boundaryGap: false,
			data: weather.value.map(w => format(w.forecasted_at, 'dd MMM HH:mm')),
		},
		yAxis: { type: 'value' },
		series: [
			{
				name: 'Temperature',
				type: 'line',
				data: weather.value.map(w => w.temperature),
			},
			{
				name: 'Humidity',
				type: 'line',
				data: weather.value.map(w => w.humidity),
			},
			{
				name: 'Wind Speed',
				type: 'line',
				data: weather.value.map(w => w.wind_speed),
			},
			{
				name: 'Pressure',
				type: 'line',
				data: weather.value.map(w => w.pressure),
			},
		],
	});
});
</script>

<template>
	<div class="card h-96">
		<div
			v-if="locationsStore.isLocationsLoading || locationsStore.isWeatherLoading"
			class="size-full flex items-center justify-center"
		>
			<font-awesome-icon
				icon="spinner"
				spin
				size="2xl"
				class="text-primary-500"
			/>
		</div>

		<div id="hourlyForecastChart" class="h-96" />
	</div>
</template>
