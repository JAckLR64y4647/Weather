<script setup lang="ts">
import { ref } from 'vue';
import api from '@/plugins/api';
import { useToast } from 'primevue/usetoast';
import { Setting } from '@/types';

const toast = useToast();

const settings = ref<Array<Setting>>([]);
const isLoading = ref(false);

async function fetchSettings() {
	isLoading.value = true;

	const { status, data } = await api.get('v1/admin/settings');

	if (status === 200) {
		settings.value = data;
	} else {
		toast.add({
			severity: 'error',
			summary: 'Error',
			detail: data?.message ?? 'An error occurred. Please try again.',
			life: 10000,
		});
	}

	isLoading.value = false;
}

fetchSettings();

const isSaveLoading = ref(false);

async function saveSettings() {
	isSaveLoading.value = true;

	const { status, data } = await api.post('v1/admin/settings', settings.value);

	if (status === 200) {
		toast.add({
			severity: 'success',
			summary: 'Success',
			detail: data.message,
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

	isSaveLoading.value = false;
}
</script>

<template>
	<div class="p-4 h-full flex flex-col">
		<h1 class="text-3xl font-bold mb-2">
			Settings
		</h1>

		<div class="card flex flex-col gap-4 h-full">
			<transition mode="out-in">
				<LoadingSpinner v-if="isLoading" />

				<div v-else class="grid grid-cols-2 gap-4 overflow-y-auto">
					<template v-for="setting in settings" :key="setting.key">
						<div>
							<label :for="setting.key" class="font-bold">{{ setting.name }}</label>
							<p class="text-surface-600 dark:text-surface-400 text-sm">
								{{ setting.description }}
							</p>
						</div>

						<div>
							<InputText
								:id="setting.key"
								v-model="setting.value"
								:disabled="isLoading || isSaveLoading"
								class="w-full"
							/>
						</div>
					</template>
				</div>
			</transition>

			<Button
				:loading="isSaveLoading"
				:disabled="isLoading"
				class="w-fit ml-auto mt-auto"
				@click="saveSettings()"
			>
				<font-awesome-icon icon="save" class="mr-1" />

				Save
			</Button>
		</div>
	</div>
</template>
