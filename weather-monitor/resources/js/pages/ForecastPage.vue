<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import router from '@/plugins/router';
import { useLocationsStore } from '@/stores/locationsStore';
import HourlyForecastChart from '@/components/HourlyForecastChart.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faFacebook, faTelegram } from '@fortawesome/free-brands-svg-icons';
import { faFileCsv, faShare } from '@fortawesome/free-solid-svg-icons';
import { useShare } from '@vueuse/core';
import { useUserStore } from '@/stores/userStore';
import api from '@/plugins/api';
import { useToast } from 'primevue/usetoast';
import { useI18n } from 'vue-i18n';

const { t: trans } = useI18n();

function t(key: string) {
	return trans('pages.forecast.' + key);
}

const userStore = useUserStore();
const locationsStore = useLocationsStore();

const toast = useToast();

const location = computed(() => {
	return locationsStore.locations.find((location) => location.slug === router.currentRoute.value.params.slug);
});

watch(location,() => {
	if (location.value?.id && !location.value?.weather) {
		locationsStore.fetchWeather(location.value);
	}
}, { immediate: true });

const selectedDate = ref<Date>(new Date());

const shareTitle = computed(() => t('share_title') + ' ' + location.value.name);

const shareNetworks = computed(() => [
	{
		name: 'facebook',
		url: `https://www.facebook.com/sharer/sharer.php?u=${window.location.href}&quote=${shareTitle.value}`,
		icon: faFacebook,
	},
	{
		name: 'telegram',
		url: `https://t.me/share/url?url=${window.location.href}&text=${shareTitle.value}`,
		icon: faTelegram,
	},
]);

const { share, isSupported } = useShare();

function defaultShare() {
	share({
		title: 'Weather Monitor',
		text: shareTitle.value,
		url: window.location.href,
	});
}

const isExportWeatherLoading = ref(false);

async function exportWeather() {
	isExportWeatherLoading.value = true;

	const { status, data } = await api.get(`v1/weather/${location.value.id}/export`);

	if (status === 200) {
		toast.add({
			severity: 'success',
			summary: trans('common.success'),
			detail: t('csv_exported'),
			life: 3000,
		});

		const blob = new Blob([data], { type: 'text/csv' });
		const url = window.URL.createObjectURL(blob);
		const a = document.createElement('a');
		a.href = url;
		a.download = `${location.value.name}.csv`;
		a.click();
		window.URL.revokeObjectURL(url);
	} else {
		toast.add({
			severity: 'error',
			summary: trans('common.error'),
			detail: data?.message ?? trans('common.request_failed'),
			life: 10000,
		});
	}

	isExportWeatherLoading.value = false;
}
</script>

<template>
	<div class="p-8">
		<transition>
			<div v-if="locationsStore.isLocationsLoading" class="size-full flex items-center justify-center">
				{{ t('loading_locations') }}
			</div>

			<div v-else-if="!location" class="size-full flex items-center justify-center">
				{{ t('location_not_found') }}
			</div>

			<div v-else class="size-full flex flex-col gap-4">
				<div class="flex justify-between gap-4 flex-wrap">
					<div>
						<h1 class="text-3xl font-bold">
							{{ location.name }}
						</h1>

						<h2 class="text-surface-600 dark:text-surface-400">
							{{ t('forecast_for') }} {{ location.name }}.
						</h2>
					</div>

					<div class="flex gap-2 flex-wrap">
						<Button
							v-tooltip.top="t(userStore.isAuthenticated ? 'export_to_csv' : 'only_auth')"
							class="h-10 p-0 font-bold"
							:disabled="!userStore.isAuthenticated"
							:loading="isExportWeatherLoading"
							@click="exportWeather()"
						>
							<font-awesome-icon :icon="faFileCsv" size="xl" class="mr-1" />

							{{ t('export') }}
						</Button>

						<Button
							v-if="isSupported"
							v-tooltip.top="t('share')"
							class="size-10 p-0"
							@click="defaultShare()"
						>
							<font-awesome-icon :icon="faShare" size="xl" />
						</Button>

						<a
							v-for="network in shareNetworks"
							:key="network.name"
							v-tooltip.top="t('share_on') + ' ' + network.name"
							:href="network.url"
							target="_blank"
						>
							<Button class="size-10 p-0">
								<font-awesome-icon :icon="network.icon" size="xl" />
							</Button>
						</a>
					</div>
				</div>

				<DailyForecastBlock v-model="selectedDate" :selected-location="location" />

				<HourlyForecastBlock v-model="selectedDate" :selected-location="location" />

				<HourlyForecastChart :selected-location="location" />

				<ReviewsBlock :selected-location="location" />
			</div>
		</transition>
	</div>
</template>
