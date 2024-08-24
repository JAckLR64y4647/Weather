<script setup lang="ts">
import { useUserStore } from '@/stores/userStore';
import Toast from 'primevue/toast';
import router from '@/plugins/router';
import { useLocationsStore } from '@/stores/locationsStore';
import { useDark, useToggle } from '@vueuse/core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { useI18n } from 'vue-i18n';
import { useStorage } from '@vueuse/core';
import { watch } from 'vue';
import api from '@/plugins/api';

const { t } = useI18n();

const userStore = useUserStore();
const locationsStore = useLocationsStore();

locationsStore.fetchLocations();

if (userStore.isAuthenticated) {
	userStore.fetchMe();
}

const menuItems = [
	{
		label: 'nav.home',
		icon: 'home',
		route: 'index',
	},
	{
		label: 'nav.dashboard',
		icon: 'chart-line',
		route: 'admin',
		visible: () => userStore.isAuthenticated && userStore.user!.roles.includes('admin'),
	},
	{
		label: 'nav.users',
		icon: 'users',
		route: 'admin-users',
		visible: () => userStore.isAuthenticated && userStore.user!.roles.includes('admin'),
	},
	{
		label: 'nav.weather',
		icon: 'cloud',
		route: 'admin-weather',
		visible: () => userStore.isAuthenticated && userStore.user!.roles.includes('admin'),
	},
	{
		label: 'nav.settings',
		icon: 'cog',
		route: 'admin-settings',
		visible: () => userStore.isAuthenticated && userStore.user!.roles.includes('admin'),
	},
	{
		label: 'nav.login',
		icon: 'sign-in',
		route: 'login',
		visible: () => !userStore.isAuthenticated,
		class: 'md:ml-auto ml-0',
	},
	{
		label: 'nav.register',
		icon: 'user-plus',
		route: 'register',
		visible: () => !userStore.isAuthenticated,
	},
	{
		label: 'nav.profile',
		icon: 'user',
		route: 'profile',
		visible: () => userStore.isAuthenticated,
		class: 'md:ml-auto ml-0',
	},
	{
		label: 'nav.logout',
		icon: 'sign-out',
		route: 'logout',
		visible: () => userStore.isAuthenticated,
	},
];

const isDark = useDark();
const toggleDark = useToggle(isDark);

const { locale } = useI18n();

const language = navigator.language || navigator.userLanguage;

const storedLocale = useStorage('locale', language.slice(0, 2));

locale.value = storedLocale.value;

watch(locale, () => {
	storedLocale.value = locale.value;
	api.defaults.headers.common['Accept-Language'] = locale.value;
});
</script>

<template>
	<div class="size-full flex flex-col bg-white dark:bg-surface-900 text-surface-900 dark:text-surface-300">
		<Menubar v-if="!router.currentRoute.value?.meta?.hideNavigation" :model="menuItems" class="rounded-none">
			<template #start>
				<router-link to="/" class="mr-6 flex items-center gap-2 whitespace-nowrap">
					<font-awesome-icon icon="sun" size="2xl" class="text-primary-500" />

					<h1 class="text-xl font-bold text-surface-500 dark:text-white/80 sm:block hidden">
						Weather Monitor
					</h1>
				</router-link>
			</template>

			<template #item="{ item }">
				<router-link
					v-if="item.route"
					v-slot="{ href, navigate, isActive }"
					:to="{ name: item.route }"
					custom
				>
					<a
						:href="href"
						class="p-2 block text-center rounded duration-200"
						:class="[
							item.class,
							isActive ? 'bg-primary-500 text-white' : 'text-surface-500 dark:text-white/80'
						]"
						@click="navigate"
					>
						<font-awesome-icon :icon="item.icon" />
						<span class="ml-2">{{ t(item.label) }}</span>
					</a>
				</router-link>

				<a
					v-else
					class="p-2 block"
					:href="item.url"
					:target="item.target"
				>
					<font-awesome-icon :icon="item.icon" />
					<span class="ml-2">{{ t(item.label) }}</span>
				</a>
			</template>

			<template #end>
				<div class="flex gap-2 items-center ml-2">
					<Dropdown v-model="locale" :options="['en', 'uk', 'ja']">
						<template #value="slotProps">
							<div v-if="slotProps.value" class="flex align-items-center">
								<img
									:alt="slotProps.value"
									:src="`/images/lang/${slotProps.value}.svg`"
									class="mr-1 w-4"
								>
								<div class="w-6">
									{{ slotProps.value }}
								</div>
							</div>
							<span v-else>
								{{ slotProps.placeholder }}
							</span>
						</template>

						<template #option="slotProps">
							<div class="flex align-items-center">
								<img
									:alt="slotProps.option"
									:src="`/images/lang/${slotProps.option}.svg`"
									class="mr-2 w-4"
								>
								<div>{{ slotProps.option }}</div>
							</div>
						</template>
					</Dropdown>

					<Button
						v-model="isDark"
						v-tooltip="t('common.toggle_theme')"
						severity="secondary"
						outlined
						class="size-8 p-0"
						@click="toggleDark()"
					>
						<font-awesome-icon icon="circle-half-stroke" />
					</Button>
				</div>
			</template>
		</Menubar>

		<div class="overflow-auto h-full">
			<router-view v-slot="{ Component, route }">
				<transition mode="out-in">
					<component :is="Component" :key="route.path" />
				</transition>
			</router-view>
		</div>

		<Toast />
	</div>
</template>
