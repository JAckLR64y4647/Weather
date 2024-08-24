<script setup lang="ts">
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import api from '@/plugins/api';
import router from '@/plugins/router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useUserStore } from '@/stores/userStore';
import { useI18n } from 'vue-i18n';

const { t: trans } = useI18n();

function t(key: string) {
	return trans('pages.register.' + key);
}

const userStore = useUserStore();

const toast = useToast();

const isLoading = ref(false);
const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

async function register() {
  isLoading.value = true;

  const { status, data } = await api.post('v1/auth/register', form.value);

  if (status === 201) {
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
	<div class="size-full flex flex-col items-center justify-center gap-2">
		<Panel :header="t('title')">
			<div class="flex flex-col gap-4">
				<div class="flex flex-col gap-2">
					<label for="name">{{ t('name') }}</label>
					<InputText id="name" v-model="form.name" :disabled="isLoading" />
				</div>

				<div class="flex flex-col gap-2">
					<label for="email">{{ t('email') }}</label>
					<InputText id="email" v-model="form.email" :disabled="isLoading" />
				</div>

				<div class="flex flex-col gap-2">
					<label for="password">{{ t('password') }}</label>
					<Password
						id="password"
						v-model="form.password"
						toggle-mask
						:disabled="isLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="passwordConfirmation">{{ t('password_confirm') }}</label>
					<Password
						id="passwordConfirmation"
						v-model="form.password_confirmation"
						toggle-mask
						:feedback="false"
						:disabled="isLoading"
					/>
				</div>

				<div class="flex gap-2">
					<Button class="grow" severity="secondary" @click="router.back()">
						<font-awesome-icon icon="chevron-left" class="mr-2" />
						{{ trans('common.back') }}
					</Button>

					<Button
						class="grow"
						:label="t('register')"
						:loading="isLoading"
						@click="register"
					/>
				</div>

				<router-link to="login" class="hover:underline text-center">
					{{ t('already_account') }}
				</router-link>
			</div>
		</Panel>
	</div>
</template>
