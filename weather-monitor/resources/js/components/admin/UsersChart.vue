<script setup lang="ts">
import { onMounted } from 'vue';
import echarts from '@/plugins/echarts';
import { format } from 'date-fns';
import { useEventListener } from '@vueuse/core';

const props = defineProps<{
	data: { date: string; count: number }[];
}>();

onMounted(async () => {
	const chart = echarts.init(document.getElementById('hourlyForecastChart'));

	useEventListener(window, 'resize', () => chart.resize());

	chart.setOption({
		tooltip: { trigger: 'axis', className: 'dark:!bg-surface-900 dark:!border-surface-800' },
		legend: { data: ['Users'] },
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
			data: props.data.map(w => format(new Date(w.date), 'dd MMM HH:mm'))
		},
		yAxis: { type: 'value' },
		series: [
			{
				name: 'Users',
				type: 'line',
				data: props.data.map(w => w.count),
			},
		],
	});
});
</script>

<template>
	<div id="hourlyForecastChart" class="h-96" />
</template>
