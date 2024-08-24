<script setup lang="ts">
import { ref } from 'vue';
import api from '@/plugins/api';
import { useUserStore } from '@/stores/userStore';
import router from '@/plugins/router';
import { useToast } from 'primevue/usetoast';
import { useI18n } from 'vue-i18n';

const { t: trans } = useI18n();

function t(key: string) {
	return trans('pages.logout.' + key);
}

const userStore = useUserStore();

const toast = useToast();

const isLoading = ref(false);

async function logout() {
  isLoading.value = true;

  const { status, data } = await api.post('v1/auth/logout');

  if (status === 200) {
    userStore.user = null;

    router.push({ name: 'index' });

    toast.add({
      severity: 'success',
      summary: trans('common.success'),
      detail: data.message,
      life: 3000,
    });
  } else {
    toast.add({
      severity: 'error',
      summary: trans('common.error'),
      detail: data?.message ?? trans('common.request_failed'),
      life: 10000,
    });
  }
}

logout();
</script>

<template>
	<div class="size-full flex items-center justify-center">
		<Panel :header="t('title')">
			<div class="flex items-center justify-center py-10 px-12">
				<font-awesome-icon icon="spinner" spin size="2xl" />
			</div>
		</Panel>
	</div>
</template>
