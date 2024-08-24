import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import { useUserStore } from '@/stores/userStore';

const routes: Array<RouteRecordRaw> = [
	{
		path: '/',
		name: 'index',
		component: () => import('@/pages/IndexPage.vue'),
		meta: {
			title: 'Home',
		},
	},
	{
		path: '/forecast/:slug',
		name: 'forecast',
		component: () => import('@/pages/ForecastPage.vue'),
		meta: {
			title: 'Home',
		},
	},
	{
		path: '/register',
		name: 'register',
		component: () => import('@/pages/auth/RegisterPage.vue'),
		meta: {
			title: 'Register',
		},
	},
	{
		path: '/login',
		name: 'login',
		component: () => import('@/pages/auth/LoginPage.vue'),
		meta: {
			title: 'Login',
		},
	},
	{
		path: '/profile',
		name: 'profile',
		component: () => import('@/pages/ProfilePage.vue'),
		meta: {
			title: 'Profile',
			requiresAuth: true,
		},
	},
	{
		path: '/logout',
		name: 'logout',
		component: () => import('@/pages/auth/LogoutPage.vue'),
		meta: {
			title: 'Logout',
			requiresAuth: true,
		},
	},
	{
		path: '/verify-email',
		name: 'verify-email',
		component: () => import('@/pages/auth/VerifyEmailPage.vue'),
		meta: {
			title: 'Verifying Email',
			hideNavigation: true,
		},
	},
	{
		path: '/forgot-password',
		name: 'forgot-password',
		component: () => import('@/pages/auth/ForgotPasswordPage.vue'),
		meta: {
			title: 'Forgot Password',
		},
	},
	{
		path: '/reset-password',
		name: 'reset-password',
		component: () => import('@/pages/auth/ResetPasswordPage.vue'),
		meta: {
			title: 'Reset Password',
			hideNavigation: true,
		},
	},
	{
		path: '/admin',
		name: 'admin',
		component: () => import('@/pages/admin/IndexPage.vue'),
		meta: {
			title: 'Dashboard',
			requiresAuth: true,
			roles: ['admin'],
		},
	},
	{
		path: '/admin/users',
		name: 'admin-users',
		component: () => import('@/pages/admin/UsersPage.vue'),
		meta: {
			title: 'Users',
			requiresAuth: true,
			roles: ['admin'],
		},
	},
	{
		path: '/admin/weather',
		name: 'admin-weather',
		component: () => import('@/pages/admin/WeatherPage.vue'),
		meta: {
			title: 'Weather',
			requiresAuth: true,
			roles: ['admin'],
		},
	},
	{
		path: '/admin/settings',
		name: 'admin-settings',
		component: () => import('@/pages/admin/SettingsPage.vue'),
		meta: {
			title: 'Settings',
			requiresAuth: true,
			roles: ['admin'],
		},
	},
	{
		path: '/:pathMatch(.*)*',
		redirect() {
			return '/';
		},
	}
];

const router = createRouter({
	history: createWebHistory(),
	routes,
	scrollBehavior() {
		setTimeout(() => {
			document.body.scrollTo(0, 0);
		}, 100);
	},
});

router.beforeEach((to, from, next) => {
	document.title = to.meta.title + ' | Weather Monitor';

	const userStore = useUserStore();

	if (to.meta.requiresAuth && !userStore.isAuthenticated) {
		next({ name: 'login' });
		return;
	}

	if (to.meta.roles && !to.meta.roles.some((role: string) => userStore.user?.roles.includes(role))) {
		next({ name: 'index' });
		return;
	}

	next();
});

export default router;
