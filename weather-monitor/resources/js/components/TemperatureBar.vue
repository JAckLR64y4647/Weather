<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  min: number;
  max: number;
  values: Array<{ min: number, max: number }>
	isHorizontal: boolean;
}>();

const minTemp = computed(() => Math.min(...props.values.map(v => v.min)));
const maxTemp = computed(() => Math.max(...props.values.map(v => v.max)));

const marginLeft = computed(() => {
  return (props.min - minTemp.value) / (maxTemp.value - minTemp.value) * 100 + '%';
});

const marginRight = computed(() => {
  return (maxTemp.value - props.max) / (maxTemp.value - minTemp.value) * 100 + '%';
});

const maskStyle = computed(() => {
	if (!props.isHorizontal) {
		return {
			clipPath: `inset(${marginRight.value} 0% ${marginLeft.value} 0% round 10px)`,
		};
	}

  return {
    clipPath: `inset(0% ${marginRight.value} 0% ${marginLeft.value} round 10px)`,
  };
});
</script>

<template>
	<div
		class="relative bg-surface-300 dark:bg-surface-700 rounded-full overflow-hidden"
		:class="[isHorizontal ? 'w-full h-3' : 'h-full w-3']"
	>
		<div
			class="absolute inset-0 from-blue-400 via-yellow-400 to-red-400 rounded-full"
			:class="[!isHorizontal ? 'bg-gradient-to-t' : 'bg-gradient-to-r']"
			:style="maskStyle"
		/>
	</div>
</template>
