import { defineStore } from 'pinia';
import api from '@/plugins/api';
import { Location } from '@/types';
import { useToast } from 'primevue/usetoast';

export const useLocationsStore = defineStore('locationsStore', {
  state: () => ({
    locations: [] as Array<Location>,
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		locationsPromise: null as Promise<any> | null,
    isLocationsLoading: false,
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		weatherPromise: null as Promise<any> | null,
    isWeatherLoading: false,
  }),
  actions: {
    async fetchLocations() {
      this.isLocationsLoading = true;

			this.locationsPromise = api.get('v1/locations');

      const { data, status } = await this.locationsPromise;

      if (status === 200) {
        this.locations = data;
        this.isLocationsLoading = false;
      } else {
        this.isLocationsLoading = false;
        throw new Error(data?.message ?? 'Failed to fetch locations. Please try again later.');
      }
    },
    async fetchWeather(location: Location) {
      this.isWeatherLoading = true;

			this.weatherPromise = api.get('v1/weather/' + location.id);

      const { status, data } = await this.weatherPromise;

      if (status === 200) {
        location.weather = data.weather;
      } else {
        const toast = useToast();

        toast.add({
          severity: 'error',
          summary: 'Error',
          detail: data?.message ?? 'Failed to fetch weather data',
          life: 10000,
        });
      }

      this.isWeatherLoading = false;
    }
  },
});
