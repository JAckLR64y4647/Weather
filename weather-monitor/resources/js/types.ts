import { WeatherCodeEnum } from '@/constants';

export interface User {
  id: number;
  name: string;
  email: string;
	roles: Array<string>;
  created_at: string;
  is_email_verified: boolean;
	location_views: Array<number>;
	favourite_locations: Array<number>;
}

export interface Weather {
  id: number;
  temperature: number;
  humidity: number;
  pressure: number;
  wind_direction: number;
  wind_speed: number;
  type: WeatherCodeEnum;
  forecasted_at: string;
}

export interface WeatherDay {
  forecasted_at: Date;
  min: number;
  max: number;
  items: Array<Weather>;
  type: WeatherCodeEnum;
}

export interface Location {
  id: number;
  name: string;
  slug: string;
  code: string;
  type: number;
  weather: Array<Weather> | null;
	latitude: number;
	longitude: number;
}

export interface Review {
	id: number;
	created_at: string;
	comment: string;
	user: {
		id: number;
		name: string;
	};
}

export interface Setting {
	name: string;
	description: string;
	key: string;
	value: string | null;
}
