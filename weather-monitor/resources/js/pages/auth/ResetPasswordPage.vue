<script setup lang="ts">
import router from '@/plugins/router';
import { useToast } from 'primevue/usetoast';
import api from '@/plugins/api';
import { ref } from 'vue';
import { useUserStore } from '@/stores/userStore';
import { useI18n } from 'vue-i18n';

const { t: trans } = useI18n();

function t(key: string) {
	return trans('pages.reset-password.' + key);
}

const userStore = useUserStore();

const toast = useToast();

if (!router.currentRoute.value.query.link) {
  toast.add({
    severity: 'error',
    summary: trans('common.error'),
    detail: t('invalid_link'),
    life: 10000,
  });
}

const isLoading = ref(false);
const resetPasswordForm = ref({
  password: '',
  password_confirmation: '',
});

async function resetPassword() {
  isLoading.value = true;

  const { status, data } = await api.get(
    router.currentRoute.value.query.link + '&' + new URLSearchParams(resetPasswordForm.value).toString(),
  );

  if (status === 200) {
    toast.add({
      severity: 'success',
      summary: trans('common.success'),
      detail: data.message,
      life: 3000,
    });

    await userStore.fetchMe();

    await router.push({ name: 'profile' });
  } else {
    toast.add({
      severity: 'error',
      summary: trans('common.error'),
      detail: data?.message ?? trans('common.request_failed'),
      life: 10000,
    });
  }

  isLoading.value = false;
}
</script>

<template>
	<div class="size-full flex items-center justify-center">
		<Panel header="Reset password">
			<div class="flex flex-col gap-4">
				<div class="flex flex-col gap-2">
					<label for="newPassword">{{ t('new_password') }}</label>
					<Password
						id="newPassword"
						v-model="resetPasswordForm.password"
						toggle-mask
						:disabled="isLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="passwordConfirmation">{{ t('new_password_confirm') }}</label>
					<Password
						id="passwordConfirmation"
						v-model="resetPasswordForm.password_confirmation"
						toggle-mask
						:feedback="false"
						:disabled="isLoading"
					/>
				</div>

				<Button
					type="button"
					:label="trans('common.submit')"
					:loading="isLoading"
					@click="resetPassword()"
				/>
			</div>
		</Panel>
	</div>
</template>
