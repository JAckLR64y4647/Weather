<script setup lang="ts">
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import api from '@/plugins/api';
import { format } from 'date-fns';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const toast = useToast();

const isIndexLoading = ref(false);
const users = ref([]);
const page = ref(1);
const rowsPerPage = ref(10);
const totalRecords = ref(0);
const sortBy = ref('id');
const sortDir = ref('desc');
const abortController = ref<AbortController | null>(null);

async function fetchUsers() {
	isIndexLoading.value = true;

	if (abortController.value) {
		abortController.value.abort();
	}

	abortController.value = new AbortController();

	const { status, data, message } = await api.get('v1/admin/users', {
		params: { page: page.value, per_page: rowsPerPage.value, sort_by: sortBy.value, sort_dir: sortDir.value },
		signal: abortController.value.signal,
	});

	if (status === 200) {
		users.value = data.data;
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

fetchUsers();

const editingRows = ref([]);

async function onRowEditSave({ newData, index }) {
	isIndexLoading.value = true;

	const { status, data } = await api.put(`v1/admin/users/${newData.id}`, newData);

	if (status === 200) {
		users.value[index] = newData;

		toast.add({
			severity: 'success',
			summary: 'Success',
			detail: 'User updated successfully.',
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

async function deleteUser(id) {
	if (!confirm('Are you sure you want to delete this user?')) {
		return;
	}

	isDeleteLoading.value = true;

	const { status, data } = await api.delete(`v1/admin/users/${id}`);

	if (status === 204) {
		users.value = users.value.filter((user) => user.id !== id);

		toast.add({
			severity: 'success',
			summary: 'Success',
			detail: 'User deleted successfully.',
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

const isCreateUserModalVisible = ref(false);
const isCreateUserLoading = ref(false);

const createUserForm = ref({
	name: '',
	email: '',
	password: '',
});

async function createUser() {
	isCreateUserLoading.value = true;

	const { status, data } = await api.post('v1/admin/users', createUserForm.value);

	if (status === 201) {
		fetchUsers();

		createUserForm.value = {
			name: '',
			email: '',
			password: '',
		};

		toast.add({
			severity: 'success',
			summary: 'Success',
			detail: 'User created successfully.',
			life: 3000,
		});

		isCreateUserModalVisible.value = false;
	} else {
		toast.add({
			severity: 'error',
			summary: 'Error',
			detail: data?.message ?? 'An error occurred. Please try again.',
			life: 10000,
		});
	}

	isCreateUserLoading.value = false;
}
</script>

<template>
	<div class="p-4">
		<Dialog
			v-model:visible="isCreateUserModalVisible"
			modal
			header="Create user"
			:visible="isCreateUserModalVisible"
			:closable="!isCreateUserLoading"
		>
			<div class="flex flex-col gap-4">
				<div class="flex flex-col gap-2">
					<label for="name">Name</label>
					<InputText id="name" v-model="createUserForm.name" :disabled="isCreateUserLoading" />
				</div>

				<div class="flex flex-col gap-2">
					<label for="email">Email</label>
					<InputText id="email" v-model="createUserForm.email" :disabled="isCreateUserLoading" />
				</div>

				<div class="flex flex-col gap-2">
					<label for="password">Password</label>
					<Password
						id="password"
						v-model="createUserForm.password"
						toggle-mask
						:feedback="false"
						:disabled="isCreateUserLoading"
					/>
				</div>

				<Button
					label="Create"
					:loading="isCreateUserLoading"
					@click="createUser()"
				/>
			</div>
		</Dialog>

		<h1 class="text-3xl font-bold mb-2">
			Users
		</h1>

		<DataTable
			v-model:editingRows="editingRows"
			v-model:rows="rowsPerPage"
			lazy
			:total-records="totalRecords"
			:loading="isIndexLoading"
			:value="users"
			striped-rows
			paginator
			:rows-per-page-options="[10, 20, 50, 100]"
			edit-mode="row"
			data-key="id"
			scrollable
			scroll-height="80vh"
			@row-edit-save="onRowEditSave"
			@page="page = $event.page; fetchUsers()"
			@sort="sortBy = $event.sortField; sortDir = $event.sortOrder; fetchUsers()"
		>
			<template #header>
				<Button @click="isCreateUserModalVisible = true">
					<font-awesome-icon icon="plus" class="mr-1" />

					Create
				</Button>
			</template>

			<Column field="id" header="ID" sortable />

			<Column field="name" header="Name" sortable>
				<template #editor="{ data, field }">
					<InputText v-model="data[field]" />
				</template>
			</Column>

			<Column field="email" header="Email" sortable>
				<template #editor="{ data, field }">
					<InputText v-model="data[field]" />
				</template>
			</Column>

			<Column field="created_at" header="Created" sortable>
				<template #body="{ data }">
					{{ format(new Date(data.created_at), 'yyyy-MM-dd HH:mm') }}
				</template>
			</Column>

			<Column field="is_email_verified" header="Email verified" sortable>
				<template #body="{ data }">
					<font-awesome-icon
						:icon="data.is_email_verified ? 'check' : 'times'"
						:class="[data.is_email_verified ? 'text-green-500' : 'text-red-500']"
					/>
				</template>
			</Column>

			<Column field="is_blocked" header="Blocked" sortable>
				<template #body="{ data }">
					<font-awesome-icon
						:icon="data.is_blocked ? 'check' : 'times'"
						:class="[data.is_blocked ? 'text-green-500' : 'text-red-500']"
					/>
				</template>

				<template #editor="{ data, field }">
					<Checkbox v-model="data[field]" binary />
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
						@click="deleteUser(data.id)"
					>
						<font-awesome-icon icon="trash" />
					</Button>
				</template>
			</Column>
		</DataTable>
	</div>
</template>
