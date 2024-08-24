import { defineStore } from 'pinia';
import api from '@/plugins/api';
import router from '@/plugins/router';
import { User } from '@/types';

export const useUserStore = defineStore('userStore', {
  state: () => ({
    user: null as null | User,
  }),
  getters: {
    isAuthenticated: (state) => state.user !== null,
  },
  actions: {
    async fetchMe() {
      const { data, status } = await api.get('v1/users/me');

      if (status === 200) {
        this.user = data;
      } else {
        this.logout();
      }
    },
    logout() {
      this.user = null;

      router.push({ name: 'login' });
    },
  },
  persist: {
    storage: localStorage,
    key: 'application',
  },
});
