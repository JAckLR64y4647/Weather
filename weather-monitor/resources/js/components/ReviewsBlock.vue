<script setup lang="ts">
import { ref } from 'vue';
import { Review, Location } from '@/types';
import api from '@/plugins/api';
import { useToast } from 'primevue/usetoast';
import { useUserStore } from '@/stores/userStore';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useI18n } from 'vue-i18n';
import { format } from 'date-fns';

const props = defineProps<{
	selectedLocation: Location;
}>();

const { t: trans } = useI18n();

function t(key: string) {
	return trans('components.reviews-block.' + key);
}

const userStore = useUserStore();

const toast = useToast();

const reviews = ref<Array<Review>>([]);
const isLoading = ref(false);

async function fetchReviews() {
	isLoading.value = true;
	reviews.value = [];

	const { status, data } = await api.get(`v1/locations/${props.selectedLocation.id}/reviews`);

	if (status === 200) {
		reviews.value = data;
	} else {
		toast.add({
			severity: 'error',
			summary: 'Error',
			detail: data?.message ?? trans('common.request_failed'),
			life: 10000,
		});
	}

	isLoading.value = false;
}

fetchReviews();

const isSubmitCommentLoading = ref(false);
const comment = ref('');

async function submitComment() {
	isSubmitCommentLoading.value = true;

	const { status, data } = await api.post(`v1/locations/${props.selectedLocation.id}/reviews`, {
		comment: comment.value,
	});

	if (status === 201) {
		toast.add({
			severity: 'success',
			summary: trans('common.success'),
			detail: t('review_added'),
			life: 3000,
		});

		fetchReviews();
		comment.value = '';
	} else {
		toast.add({
			severity: 'error',
			summary: trans('common.error'),
			detail: data?.message ?? trans('common.request_failed'),
			life: 10000,
		});
	}

	isSubmitCommentLoading.value = false;
}

const isDeleteReviewLoading = ref(false);

async function deleteReview(reviewId: number) {
	isDeleteReviewLoading.value = true;

	const { status, data } = await api.delete(`v1/locations/${props.selectedLocation.id}/reviews/${reviewId}`);

	if (status === 204) {
		toast.add({
			severity: 'success',
			summary: trans('common.success'),
			detail: t('review_deleted'),
			life: 3000,
		});

		fetchReviews();
	} else {
		toast.add({
			severity: 'error',
			summary: trans('common.error'),
			detail: data?.message ?? trans('common.request_failed'),
			life: 10000,
		});
	}

	isDeleteReviewLoading.value = false;
}
</script>

<template>
	<div>
		<p class="text-xl font-bold mb-4">
			{{ t('reviews') }}
		</p>

		<div class="card flex flex-col gap-4 mb-4">
			<p>{{ t('add_review_for') }} {{ selectedLocation.name }}</p>

			<InputText
				v-model="comment"
				:disabled="!userStore.isAuthenticated || isSubmitCommentLoading"
				:placeholder="t(userStore.isAuthenticated ? 'comment' : 'please_auth')"
			/>

			<Button
				:label="t('submit')"
				class="w-fit"
				:disabled="!userStore.isAuthenticated"
				:loading="isSubmitCommentLoading"
				@click="submitComment"
			/>
		</div>

		<div class="flex flex-col gap-4">
			<div v-if="isLoading" class="card text-center">
				<font-awesome-icon
					icon="spinner"
					spin
					size="2xl"
					class="text-primary-500"
				/>
			</div>

			<div v-else-if="!reviews.length" class="card text-center text-surface-600 dark:text-surface-400">
				{{ t('no_reviews') }}
			</div>

			<div v-for="review in reviews" :key="review.id" class="card flex flex-col gap-4">
				<div class="flex items-center justify-between flex-wrap gap-4">
					<p class="text-xl">
						{{ review.user.name }}
					</p>

					<Button
						v-if="userStore.user?.id === review.user.id"
						severity="danger"
						class="w-fit ml-auto !size-8 !p-0 flex items-center justify-center"
						:loading="isDeleteReviewLoading"
						@click="deleteReview(review.id)"
					>
						<font-awesome-icon icon="trash" />
					</Button>

					<p class="text-sm text-surface-600 dark:text-surface-400">
						{{ format(review.created_at, 'yyyy-MM-dd HH:mm') }}
					</p>
				</div>

				<p>
					{{ review.comment }}
				</p>
			</div>
		</div>
	</div>
</template>
