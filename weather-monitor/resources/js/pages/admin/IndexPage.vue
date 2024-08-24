<script setup lang="ts">
import { ref } from 'vue';
import api from '@/plugins/api';
import { useToast } from 'primevue/usetoast';

const toast = useToast();

const stats = ref([]);
const usersChartData = ref([]);

const isLoading = ref(false);

async function fetchStats() {
	isLoading.value = true;

	const { status, data } = await api.get('v1/admin/stats');

	if (status === 200) {
		stats.value = data.stats;
		usersChartData.value = data.users_chart_data;
	} else {
		toast.add({
			severity: 'error',
			summary: 'Error',
			detail: data?.message ?? 'Failed to fetch stats. Please try again.',
			life: 10000,
		});
	}

	isLoading.value = false;
}

fetchStats();
</script>

<template>
	<div class="p-4">
		<h1 class="text-3xl font-bold mb-2">
			Admin Dashboard
		</h1>

		<div class="flex flex-col gap-4">
			<Panel header="Stats">
				<div class="w-full flex flex-wrap gap-4 justify-around">
					<LoadingSpinner v-if="isLoading" />

					<template v-else>
						<div v-for="(stat, i) in stats" :key="i" class="text-center">
							<p class="text-2xl font-bold">
								{{ stat.value }}
							</p>
							<p class="text-xl">
								{{ stat.label }}
							</p>
						</div>
					</template>
				</div>
			</Panel>

			<Panel header="User creation rate">
				<LoadingSpinner v-if="isLoading" />

				<UsersChart v-else :data="usersChartData" />
			</Panel>
		</div>
	</div>
</template>
