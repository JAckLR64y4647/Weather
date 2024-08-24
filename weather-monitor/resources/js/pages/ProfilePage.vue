<script setup lang="ts">
import { useUserStore } from '@/stores/userStore';
import { ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import api from '@/plugins/api';
import { useToast } from 'primevue/usetoast';
import { useLocationsStore } from '@/stores/locationsStore';
import router from '@/plugins/router';
import { useI18n } from 'vue-i18n';

const { t: trans } = useI18n();

function t(key: string) {
	return trans('pages.profile.' + key);
}

const userStore = useUserStore();
const locationsStore = useLocationsStore();

const toast = useToast();

userStore.fetchMe();

const isEditMode = ref(false);
const isLoading = ref(false);
const form = ref({
  name: '',
  email: '',
});

form.value.name = userStore.user.name;
form.value.email = userStore.user.email;

async function updateUser() {
  isLoading.value = true;

  const { status, data } = await api.put('v1/users/me', form.value);

  if (status === 200) {
    toast.add({
      severity: 'success',
      summary: trans('common.success'),
      detail: data.message,
      life: 3000,
    });

    userStore.user = data.user;
  } else {
    toast.add({
      severity: 'error',
      summary: trans('common.error'),
      detail: data?.message ?? trans('common.request_failed'),
      life: 10000,
    });
  }

  isLoading.value = false;
  isEditMode.value = false;
}

const isSendVerificationEmailLoading = ref(false);

async function sendVerificationEmail() {
  isSendVerificationEmailLoading.value = true;

  const { status, data } = await api.post('v1/auth/email-verification/send');

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

  isSendVerificationEmailLoading.value = false;
}

const isChangePasswordModalVisible = ref(false);
const changePasswordForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

function closeChangePasswordModal() {
  isChangePasswordModalVisible.value = false;
  changePasswordForm.value = {
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
  };
}

async function changePassword() {
  isLoading.value = true;

  const { status, data } = await api.post('v1/auth/change-password', changePasswordForm.value);

  if (status === 200) {
    toast.add({
      severity: 'success',
      summary: trans('common.success'),
      detail: data.message,
      life: 3000,
    });

    isChangePasswordModalVisible.value = false;
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

const isToggleFavouriteLoading = ref(false);

async function toggleFavourite(locationId: number) {
	if (!userStore.user) {
		return;
	}

	isToggleFavouriteLoading.value = true;

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

	isToggleFavouriteLoading.value = false;
}

const isDeleteAccountLoading = ref(false);

async function deleteAccount() {
	if (!confirm(t('delete_account_confirm'))) {
		return;
	}

	isDeleteAccountLoading.value = true;

	const { status, data } = await api.delete('v1/users/me');

	if (status === 200) {
		toast.add({
			severity: 'success',
			summary: trans('common.success'),
			detail: data.message,
			life: 3000,
		});

		userStore.user = null;

		router.push({ name: 'index' });
	} else {
		toast.add({
			severity: 'error',
			summary: trans('common.error'),
			detail: data?.message ?? trans('common.request_failed'),
			life: 10000,
		});
	}

	isDeleteAccountLoading.value = false;
}
</script>

<template>
	<div class="size-full flex flex-wrap gap-4 items-center justify-center overflow-y-auto p-4">
		<Panel :header="t('title')" class="md:w-auto w-full">
			<div class="flex md:flex-row flex-col gap-8 md:h-96 h-auto">
				<div>
					<div class="flex flex-col gap-4">
						<div class="flex flex-col gap-2">
							<label for="name">{{ t('name') }}</label>
							<InputText id="name" v-model="form.name" :disabled="!isEditMode || isLoading" />
						</div>

						<div class="flex flex-col gap-2">
							<label for="email">{{ t('email') }}</label>
							<InputText id="email" v-model="form.email" :disabled="!isEditMode || isLoading" />
						</div>

						<div class="flex gap-2">
							<Button v-if="!isEditMode" @click="isEditMode = true">
								<font-awesome-icon icon="pencil" class="mr-2" />
								{{ trans('common.edit') }}
							</Button>

							<template v-else>
								<Button
									class="grow"
									outlined
									:disabled="isLoading"
									@click="isEditMode = false"
								>
									<font-awesome-icon icon="times" class="mr-2" />
									{{ trans('common.cancel') }}
								</Button>

								<Button class="grow" :loading="isLoading" @click="updateUser()">
									<font-awesome-icon icon="floppy-disk" class="mr-2" />
									{{ trans('common.save') }}
								</Button>
							</template>
						</div>
					</div>
				</div>

				<div>
					<div class="flex flex-col gap-2">
						<div class="flex flex-col">
							<label for="email">{{ t('registered_at') }}:</label>
							<p>{{ userStore.user!.created_at.slice(0, 19).replace('T', ' ') }}</p>
						</div>

						<div class="flex items-center gap-1">
							{{ t('is_email_verified') }}:
							<font-awesome-icon
								:icon="userStore.user!.is_email_verified ? 'check' : 'times'"
								:class="userStore.user!.is_email_verified ? 'text-green-500' : 'text-red-500'"
							/>
						</div>

						<Button
							v-if="!userStore.user!.is_email_verified"
							:label="t('send_verification_email')"
							outlined
							:loading="isSendVerificationEmailLoading"
							:disabled="isDeleteAccountLoading"
							@click="sendVerificationEmail()"
						/>
						<Button
							:label="t('change_password')"
							:disabled="isSendVerificationEmailLoading || isDeleteAccountLoading"
							@click="isChangePasswordModalVisible = true"
						/>
						<Button
							:label="t('delete_account')"
							severity="danger"
							:disabled="isSendVerificationEmailLoading"
							:loading="isDeleteAccountLoading"
							@click="deleteAccount()"
						/>
					</div>
				</div>
			</div>
		</Panel>

		<Panel :header="t('recent_locations')" class="md:w-auto w-full">
			<div class="flex flex-col gap-2 overflow-y-auto max-h-96 h-96">
				<div
					v-if="userStore.user!.location_views.length === 0"
					class="text-center h-full content-center text-surface-600 dark:text-surface-400"
				>
					{{ t('no_locations') }}
				</div>

				<Button
					v-for="locationId in userStore.user!.location_views"
					:key="locationId"
					@click="router.push('/forecast/' + locationsStore.locations.find(l => l.id === locationId).slug)"
				>
					{{ locationsStore.locations.find(loc => loc.id === locationId)?.name }}
				</Button>
			</div>
		</Panel>

		<Panel :header="t('favourite_locations')" class="md:w-auto w-full">
			<div class="flex flex-col gap-2 overflow-y-auto max-h-96 h-96">
				<div
					v-if="userStore.user!.favourite_locations.length === 0"
					class="text-center h-full content-center text-surface-600 dark:text-surface-400"
				>
					{{ t('no_locations') }}
				</div>

				<div
					v-for="locationId in userStore.user!.favourite_locations"
					:key="locationId"
					class="flex gap-2"
				>
					<Button
						class="w-full"
						@click="router.push('/forecast/' + locationsStore.locations.find(l => l.id === locationId).slug)"
					>
						{{ locationsStore.locations.find(loc => loc.id === locationId)?.name }}
					</Button>

					<Button outlined :disabled="isToggleFavouriteLoading" @click="toggleFavourite(locationId)">
						<font-awesome-icon icon="times" />
					</Button>
				</div>
			</div>
		</Panel>

		<Dialog
			v-model:visible="isChangePasswordModalVisible"
			modal
			header="Change Password"
			:style="{ width: '25rem' }"
			:closable="!isLoading"
		>
			<div class="flex flex-col gap-4">
				<div class="flex flex-col gap-2">
					<label for="currentPassword">{{ t('current_password') }}</label>
					<Password
						id="currentPassword"
						v-model="changePasswordForm.current_password"
						toggle-mask
						:feedback="false"
						:disabled="isLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="newPassword">{{ t('new_password') }}</label>
					<Password
						id="newPassword"
						v-model="changePasswordForm.new_password"
						toggle-mask
						:disabled="isLoading"
					/>
				</div>

				<div class="flex flex-col gap-2">
					<label for="passwordConfirmation">{{ t('new_password_confirm') }}</label>
					<Password
						id="passwordConfirmation"
						v-model="changePasswordForm.new_password_confirmation"
						toggle-mask
						:feedback="false"
						:disabled="isLoading"
					/>
				</div>

				<div class="flex justify-end gap-2">
					<Button
						type="button"
						:label="trans('common.cancel')"
						severity="secondary"
						@click="closeChangePasswordModal()"
					/>
					<Button type="button" :label="trans('common.save')" @click="changePassword()" />
				</div>
			</div>
		</Dialog>
	</div>
</template>
