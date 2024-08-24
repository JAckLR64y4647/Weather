<script setup lang="ts">
import { WeatherCodeEnum } from '@/constants';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps<{ type: WeatherCodeEnum }>();

function t(key: string) {
	const { t } = useI18n();

	return t('components.weather-name.' + key);
}

const text = computed(() => {
	return {
		[WeatherCodeEnum.CLEAR_SKY]: t('clear_sky'),
		[WeatherCodeEnum.MAINLY_CLEAR]: t('mainly_clear'),
		[WeatherCodeEnum.PARTLY_CLOUDY]: t('partly_cloudy'),
		[WeatherCodeEnum.OVERCAST]: t('overcast'),
		[WeatherCodeEnum.FOG]: t('fog'),
		[WeatherCodeEnum.RIME_FOG]: t('rime_fog'),
		[WeatherCodeEnum.DRIZZLE_LIGHT]: t('drizzle_light'),
		[WeatherCodeEnum.DRIZZLE_MODERATE]: t('drizzle_moderate'),
		[WeatherCodeEnum.DRIZZLE_DENSE]: t('drizzle_dense'),
		[WeatherCodeEnum.FREEZING_DRIZZLE_LIGHT]: t('freezing_drizzle_light'),
		[WeatherCodeEnum.FREEZING_DRIZZLE_DENSE]: t('freezing_drizzle_dense'),
		[WeatherCodeEnum.RAIN_SLIGHT]: t('rain_slight'),
		[WeatherCodeEnum.RAIN_MODERATE]: t('rain_moderate'),
		[WeatherCodeEnum.RAIN_HEAVY]: t('rain_heavy'),
		[WeatherCodeEnum.SNOW_SLIGHT]: t('snow_slight'),
		[WeatherCodeEnum.SNOW_MODERATE]: t('snow_moderate'),
		[WeatherCodeEnum.SNOW_HEAVY]: t('snow_heavy'),
		[WeatherCodeEnum.SNOW_GRAINS]: t('snow_grains'),
		[WeatherCodeEnum.RAIN_SHOWERS_SLIGHT]: t('rain_showers_slight'),
		[WeatherCodeEnum.RAIN_SHOWERS_MODERATE]: t('rain_showers_moderate'),
		[WeatherCodeEnum.RAIN_SHOWERS_VIOLENT]: t('rain_showers_violent'),
		[WeatherCodeEnum.SNOW_SHOWERS_SLIGHT]: t('snow_showers_slight'),
		[WeatherCodeEnum.SNOW_SHOWERS_HEAVY]: t('snow_showers_heavy'),
		[WeatherCodeEnum.THUNDERSTORM]: t('thunderstorm'),
		[WeatherCodeEnum.THUNDERSTORM_WITH_HAIL_SLIGHT]: t('thunderstorm_with_hail_slight'),
		[WeatherCodeEnum.THUNDERSTORM_WITH_HAIL_HEAVY]: t('thunderstorm_with_hail_heavy'),
	}[props.type];
});
</script>

<template>
	<span>{{ text }}</span>
</template>
