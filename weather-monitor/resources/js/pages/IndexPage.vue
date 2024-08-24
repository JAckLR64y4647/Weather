<script setup lang="ts">
import UkraineMap from '@/components/UkraineMap.vue';
import { useLocationsStore } from '@/stores/locationsStore';
import router from '@/plugins/router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useUserStore } from '@/stores/userStore';
import api from '@/plugins/api';
import { useToast } from 'primevue/usetoast';
import { computed, ref } from 'vue';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import { useI18n } from 'vue-i18n';

const { t: trans } = useI18n();

function t(key: string) {
	return trans('pages.index.' + key);
}

const locationsStore = useLocationsStore();
const userStore = useUserStore();

const toast = useToast();

const isLoading = ref(false);

async function toggleFavourite(locationId: number) {
	if (!userStore.user) {
		return;
	}

	isLoading.value = true;

	const index = userStore.user.favourite_locations.indexOf(locationId);

	if (index === -1) {
		userStore.user.favourite_locations.push(locationId);
	} else {
		userStore.user.favourite_locations.splice(index, 1);
	}

	const { status, data } = await api.put(`v1/locations/${locationId}/favourite`);

	if (status !== 200) {
		toast.add({
			severity: 'error',
			summary: trans('common.error'),
			detail: data?.message ?? trans('common.request_failed'),
			life: 10000,
		});
	}

	isLoading.value = false;
}

function getDistance(lat1, lon1, lat2, lon2) {
	const R = 6371; // Radius of the Earth in km
	const dLat = (lat2 - lat1) * Math.PI / 180;
	const dLon = (lon2 - lon1) * Math.PI / 180;
	const a =
		Math.sin(dLat / 2) * Math.sin(dLat / 2) +
		Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
		Math.sin(dLon / 2) * Math.sin(dLon / 2);

	const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

	// Distance in km
	return R * c;
}

const search = ref('');

const filteredLocations = computed(() => {
	if (search.value.includes(',')) {
		const [lat, lon] = search.value.split(',').map(parseFloat);

		if (!isNaN(lat) && !isNaN(lon)) {
			return [...locationsStore.locations].sort((a, b) => {
				const distanceA = getDistance(lat, lon, a.latitude, a.longitude);
				const distanceB = getDistance(lat, lon, b.latitude, b.longitude);

				return distanceA - distanceB;
			});
		}
	}

	return locationsStore.locations.filter(location => {
		return location.name.toLowerCase().includes(search.value.toLowerCase());
	});
});
</script>

<template>
	<div class="h-full p-8 flex lg:flex-row flex-col lg:gap-4 gap-8">
		<UkraineMap class="lg:w-2/3 w-auto sm:block hidden flex-none" />

		<div class="card flex flex-col gap-2 lg:w-1/3 w-auto">
			<h2 class="text-2xl font-bold">
				{{ t('welcome') }}
			</h2>

			<h2 class="text-surface-600 dark:text-surface-400">
				{{ t('select_location') }}
			</h2>

			<InputGroup>
				<InputGroupAddon>
					<font-awesome-icon icon="search" />
				</InputGroupAddon>
				<InputText v-model="search" :placeholder="t('search')" />
			</InputGroup>

			<transition mode="out-in">
				<div v-if="locationsStore.isLocationsLoading" class="h-full flex items-center justify-center">
					<font-awesome-icon
						icon="spinner"
						spin
						size="2xl"
						class="text-primary-500"
					/>
				</div>

				<div v-else class="flex flex-col gap-2 overflow-y-auto h-full">
					<div v-for="location in filteredLocations" :key="location.id" class="flex gap-2">
						<Button class="w-full" @click="router.push('/forecast/' + location.slug)">
							<div>
								{{ location.name }}
							</div>
						</Button>

						<Button
							v-if="userStore.user"
							v-tooltip="t('add_to_favorites')"
							outlined
							severity="secondary"
							:loading="isLoading"
							@click="toggleFavourite(location.id)"
						>
							<font-awesome-icon
								icon="star"
								:class="{ 'text-yellow-500': userStore.user.favourite_locations.includes(location.id) }"
							/>
						</Button>
					</div>
				</div>
			</transition>
		</div>
	</div>
</template>
