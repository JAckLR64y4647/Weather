<script setup lang="ts">
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import api from '@/plugins/api';
import { format } from 'date-fns';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useLocationsStore } from '@/stores/locationsStore';
import { weatherTypes } from '@/constants';

const locationsStore = useLocationsStore();

const toast = useToast();

const isIndexLoading = ref(false);
const weather = ref([]);
const page = ref(1);
const rowsPerPage = ref(10);
const totalRecords = ref(0);
const sortBy = ref('id');
const sortDir = ref('desc');
const abortController = ref<AbortController | null>(null);

async function fetchWeather() {
	isIndexLoading.value = true;

	if (abortController.value) {
		abortController.value.abort();
	}

	abortController.value = new AbortController();

	const { status, data, message } = await api.get('v1/admin/weather', {
		params: { page: page.value, per_page: rowsPerPage.value, sort_by: sortBy.value, sort_dir: sortDir.value },
		signal: abortController.value.signal,
	});

	if (status === 200) {
		weather.value = data.data.map((weather) => ({
			...weather,
			forecasted_at: new Date(weather.forecasted_at),
		}));
		totalRecords.value = data.meta.total;
	} else if (message === 'canceled') {
		// Do nothing.
	} else {
		toast.add({
			severity: 'error',
			summary: 'Error',
			detail: data?.message ?? 'An error occurred. Please try again.',
			life: 10000,
		});
	}

	isIndexLoading.value = false;
}

fetchWeather();

const editingRows = ref([]);

async function onRowEditSave({ newData, index }) {
	isIndexLoading.value = true;

	const { status, data } = await api.put(`v1/admin/weather/${newData.id}`, newData);

	if (status === 200) {
		weather.value[index] = newData;

		toast.add({
			severity: 'success',
			summary: 'Success',
			detail: 'Weather updated successfully.',
			life: 3000,
		});
	} else {
		toast.add({
			severity: 'error',
			summary: 'Error',
			detail: data?.message ?? 'An error occurred. Please try again.',
			life: 10000,
		});
	}

	isIndexLoading.value = false;
}

const isDeleteLoading = ref(false);

async function deleteWeather(id) {
	if (!confirm('Are you sure you want to delete this weather?')) {
		return;
	}

	isDeleteLoading.value = true;

	const { status, data } = await api.delete(`v1/admin/weather/${id}`);

	if (status === 204) {
		weather.value = weather.value.filter((user) => user.id !== id);

		toast.add({
			severity: 'success',
			summary: 'Success',
			detail: 'Weather deleted successfully.',
			life: 3000,
		});
	} else {
		toast.add({
			severity: 'error',
			summary: 'Error',
			detail: data?.message ?? 'An error occurred. Please try again.',
			life: 10000,
		});
	}

	isDeleteLoading.value = false;
}

const isCreateWeatherModalVisible = ref(false);
const isCreateWeatherLoading = ref(false);

const createWeatherForm = ref({
	location_id: null,
	temperature: null,
	humidity: null,
	pressure: null,
	wind_speed: null,
	wind_direction: null,
	type: null,
	forecasted_at: null,
});

async function createWeather() {
	isCreateWeatherLoading.value = true;

	const { status, data } = await api.post('v1/admin/weather', createWeatherForm.value);

	if (status === 201) {
		fetchWeather();

		createWeatherForm.value = {
			location_id: null,
			temperature: null,
			humidity: null,
			pressure: null,
			wind_speed: null,
			wind_direction: null,
			type: null,
			forecasted_at: null,
		};

		toast.add({
			severity: 'success',
			summary: 'Success',
			detail: 'Weather created successfully.',
			life: 3000,
		});

		isCreateWeatherModalVisible.value = false;
	} else {
		toast.add({
			severity: 'error',
			summary: 'Error',
			detail: data?.message ?? 'An error occurred. Please try again.',
			life: 10000,
		});
	}

	isCreateWeatherLoading.value = false;
}
</script>

<template>
	<div class="p-4">
		<Dialog
			v-model:visible="isCreateWeatherModalVisible"
			modal
			header="Create user"
			:visible="isCreateWeatherModalVisible"
			:closable="!isCreateWeatherLoading"
		>
			<div class="flex flex-col gap-4">
				<div class="flex flex-col gap-2">
					<label for="location">Location</label>
					<Dropdown
						id="location"
						v-model="createWeatherForm.location_id"
						:options="locationsStore.locations"
						:loading="locationsStore.isLocationsLoading"
						option-label="name"
						option-value="id"
						:disabled="isCreateWeatherLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="temperature">Temperature</label>
					<InputText
						id="temperature"
						v-model="createWeatherForm.temperature"
						type="number"
						:disabled="isCreateWeatherLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="humidity">Humidity</label>
					<InputText
						id="humidity"
						v-model="createWeatherForm.humidity"
						type="number"
						:disabled="isCreateWeatherLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="temperature">Pressure</label>
					<InputText
						id="pressure"
						v-model="createWeatherForm.pressure"
						type="number"
						:disabled="isCreateWeatherLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="wind_speed">Wind speed</label>
					<InputText
						id="wind_speed"
						v-model="createWeatherForm.wind_speed"
						type="number"
						:disabled="isCreateWeatherLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="wind_direction">Wind direction</label>
					<InputText
						id="wind_direction"
						v-model="createWeatherForm.wind_direction"
						type="number"
						:disabled="isCreateWeatherLoading"
						min="0"
						max="360"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="type">Type</label>
					<Dropdown
						id="type"
						v-model="createWeatherForm.type"
						:options="weatherTypes"
						option-label="label"
						option-value="value"
						:disabled="isCreateWeatherLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="forecasted_at">Forecasted at</label>
					<Calendar
						id="forecasted_at"
						v-model="createWeatherForm.forecasted_at"
						show-time
						:disabled="isCreateWeatherLoading"
					/>
				</div>

				<Button
					label="Create"
					:loading="isCreateWeatherLoading"
					@click="createWeather()"
				/>
			</div>
		</Dialog>

		<h1 class="text-3xl font-bold mb-2">
			Weather
		</h1>

		<DataTable
			v-model:editingRows="editingRows"
			v-model:rows="rowsPerPage"
			:total-records="totalRecords"
			:loading="isIndexLoading"
			:value="weather"
			lazy
			striped-rows
			paginator
			:rows-per-page-options="[10, 20, 50, 100]"
			edit-mode="row"
			data-key="id"
			scrollable
			scroll-height="68vh"
			removable-sort
			@row-edit-save="onRowEditSave"
			@page="page = $event.page; fetchWeather()"
			@sort="sortBy = $event.sortField; sortDir = $event.sortOrder; fetchWeather()"
		>
			<template #header>
				<Button @click="isCreateWeatherModalVisible = true">
					<font-awesome-icon icon="plus" class="mr-1" />

					Create
				</Button>
			</template>

			<Column field="id" header="ID" sortable />

			<Column field="location_id" header="Location" sortable>
				<template #body="{ data }">
					{{ locationsStore.locations.find(l => l.id === data.location_id)?.name }}
				</template>

				<template #editor="{ data, field }">
					<Dropdown
						v-model="data[field]"
						:options="locationsStore.locations"
						:loading="locationsStore.isLocationsLoading"
						option-label="name"
						option-value="id"
					/>
				</template>
			</Column>

			<Column field="temperature" header="Temperature" sortable>
				<template #editor="{ data, field }">
					<InputText v-model="data[field]" type="number" />
				</template>
			</Column>

			<Column field="humidity" header="Humidity" sortable>
				<template #editor="{ data, field }">
					<InputText v-model="data[field]" type="number" />
				</template>
			</Column>

			<Column field="wind_speed" header="Wind speed" sortable>
				<template #editor="{ data, field }">
					<InputText v-model="data[field]" type="number" />
				</template>
			</Column>

			<Column field="wind_direction" header="Wind direction" sortable>
				<template #editor="{ data, field }">
					<InputText
						v-model="data[field]"
						type="number"
						min="0"
						max="360"
					/>
				</template>
			</Column>

			<Column field="pressure" header="Pressure" sortable>
				<template #editor="{ data, field }">
					<InputText v-model="data[field]" type="number" />
				</template>
			</Column>

			<Column field="forecasted_at" header="Forecasted" sortable>
				<template #body="{ data }">
					{{ format(new Date(data.forecasted_at), 'yyyy-MM-dd HH:mm') }}
				</template>

				<template #editor="{ data, field }">
					<Calendar v-model="data[field]" show-time />
				</template>
			</Column>

			<Column field="type" header="Weather type" sortable>
				<template #body="{ data }">
					<WeatherName :type="data.type" />
				</template>

				<template #editor="{ data, field }">
					<Dropdown
						v-model="data[field]"
						:options="weatherTypes"
						option-label="label"
						option-value="value"
					/>
				</template>
			</Column>

			<Column row-editor class="min-w-16" body-style="text-align:center">
				<template #roweditoriniticon>
					<font-awesome-icon icon="pencil" />
				</template>
			</Column>

			<Column header="" class="w-8">
				<template #body="{ data }">
					<Button
						class="size-8 !p-0"
						severity="danger"
						rounded
						text
						@click="deleteWeather(data.id)"
					>
						<font-awesome-icon icon="trash" />
					</Button>
				</template>
			</Column>
		</DataTable>
	</div>
</template>
