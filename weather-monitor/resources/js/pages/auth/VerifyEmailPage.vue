<script setup lang="ts">
import router from '@/plugins/router';
import { useToast } from 'primevue/usetoast';
import api from '@/plugins/api';
import { useI18n } from 'vue-i18n';

const { t: trans } = useI18n();

function t(key: string) {
	return trans('pages.verify-email.' + key);
}

const toast = useToast();

async function verifyEmail() {
  if (!router.currentRoute.value.query.link) {
    toast.add({
      severity: 'error',
      summary: trans('common.error'),
      detail: t('invalid_link'),
      life: 10000,
    });
  } else {
    const { status, data } = await api.get(router.currentRoute.value.query.link);

    if (status === 200) {
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

  router.push({ name: 'profile' });
}

verifyEmail();
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
