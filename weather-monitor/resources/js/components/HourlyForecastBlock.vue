<script setup lang="ts">
import { Location, Weather } from '@/types';
import { computed } from 'vue';
import { format, isToday } from 'date-fns';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps<{ selectedLocation: Location | null }>();

const selectedDate = defineModel<Date>({ default: Date.now });

const weatherToday = computed<Weather[]>(() => {
	if (!props.selectedLocation || !props.selectedLocation.weather) {
		return [];
	}

	return props.selectedLocation.weather
		.filter((w) => format(new Date(w.forecasted_at), 'yyyy-MM-dd')
			=== format(selectedDate.value, 'yyyy-MM-dd'));
});
</script>

<template>
	<div class="card flex items-stretch gap-4 overflow-x-auto text-center">
		<div
			v-for="weather in weatherToday"
			:key="weather.id"
			class="card !p-2 flex flex-col items-center gap-2 w-28 flex-none"
			:class="{
				'outline outline-primary-500':
					isToday(selectedDate) && selectedDate.getHours() === new Date(weather.forecasted_at).getHours()
			}"
		>
			<p class="text-sm text-surface-600">
				{{ format(weather.forecasted_at, 'HH:mm') }}
			</p>
			<p class="font-black">
				{{ weather.temperature }}°C
			</p>

			<WeatherIcon
				:type="weather.type"
				:date="weather.forecasted_at"
				class="text-primary-500"
				size="xl"
			/>
			<WeatherName :type="weather.type" class="h-12 md:text-base text-sm" />

			<p>
				{{ weather.humidity }}%
				<font-awesome-icon icon="droplet" class="text-primary-500" size="sm" />
			</p>
			<p>
				{{ weather.pressure }}hPa
			</p>
			<p>
				{{ weather.wind_speed }}m/s
				<font-awesome-icon icon="wind" class="text-primary-500" size="sm" />
			</p>
			<p>
				{{ weather.wind_direction }}°
				<font-awesome-icon
					icon="location-arrow"
					class="text-primary-500"
					size="sm"
					:style="{ transform: `rotate(${weather.wind_direction - 45}deg)` }"
				/>
			</p>
		</div>
	</div>
</template>
