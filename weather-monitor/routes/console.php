<?php

use App\Jobs\UpdateWeatherJob;

Schedule::command('auth:clear-resets')->everyThirtyMinutes();
Schedule::job(new UpdateWeatherJob())->hourly();
