<script setup lang="ts">
import { ref } from 'vue';
import api from '@/plugins/api';
import { useToast } from 'primevue/usetoast';
import router from '@/plugins/router';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useUserStore } from '@/stores/userStore';
import { useI18n } from 'vue-i18n';

const { t: trans } = useI18n();

function t(key: string) {
	return trans('pages.login.' + key);
}

const userStore = useUserStore();

const toast = useToast();

const isLoading = ref(false);
const form = ref({
  email: '',
  password: '',
  remember_me: false,
});

async function login() {
  isLoading.value = true;

  const { status, data } = await api.post('v1/auth/login', form.value);

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
	<div class="size-full flex flex-col items-center justify-center">
		<Panel :header="t('title')">
			<div class="flex flex-col gap-4">
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
						:feedback="false"
						:disabled="isLoading"
					/>
				</div>

				<div class="flex align-items-center">
					<Checkbox
						v-model="form.remember_me"
						input-id="remember_me"
						name="remember_me"
						value="Remember me"
						:disabled="isLoading"
						binary
					/>
					<label for="remember_me" class="ml-2">{{ t('remember') }}</label>
				</div>

				<div class="flex gap-2">
					<Button class="grow" severity="secondary" @click="router.back()">
						<font-awesome-icon icon="chevron-left" class="mr-2" />
						{{ trans('common.back') }}
					</Button>

					<Button
						class="grow"
						:label="t('login')"
						:loading="isLoading"
						@click="login"
					/>
				</div>

				<div class="flex gap-2 justify-between">
					<router-link to="forgot-password" class="hover:underline text-center">
						{{ t('forgot_password') }}
					</router-link>

					<router-link to="register" class="hover:underline text-center">
						{{ t('no_account') }}
					</router-link>
				</div>
			</div>
		</Panel>
	</div>
</template>
