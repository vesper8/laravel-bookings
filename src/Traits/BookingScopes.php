<?php

declare(strict_types=1);

namespace Rinvex\Bookings\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait BookingScopes
{
    /**
     * Get past bookings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function pastBookings(): MorphMany
    {
        return $this->bookings()
                    ->whereNull('canceled_at')
                    ->whereNotNull('ends_at')
                    ->where('ends_at', '<', now());
    }

    /**
     * Get future bookings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function futureBookings(): MorphMany
    {
        return $this->bookings()
                    ->whereNull('canceled_at')
                    ->whereNotNull('starts_at')
                    ->where('starts_at', '>', now());
    }

    /**
     * Get current bookings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function currentBookings(): MorphMany
    {
        return $this->bookings()
                    ->whereNull('canceled_at')
                    ->whereNotNull('starts_at')
                    ->whereNotNull('ends_at')
                    ->where('starts_at', '<', now())
                    ->where('ends_at', '>', now());
    }

    /**
     * Get cancelled bookings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function cancelledBookings(): MorphMany
    {
        return $this->bookings()
                    ->whereNotNull('canceled_at');
    }

    /**
     * Get bookings starts before the given date.
     *
     * @param string $date
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsStartsBefore(string $date): MorphMany
    {
        return $this->bookings()
                    ->whereNull('canceled_at')
                    ->whereNotNull('starts_at')
                    ->where('starts_at', '<', new Carbon($date));
    }

    /**
     * Get bookings starts after the given date.
     *
     * @param string $date
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsStartsAfter(string $date): MorphMany
    {
        return $this->bookings()
                    ->whereNull('canceled_at')
                    ->whereNotNull('starts_at')
                    ->where('starts_at', '>', new Carbon($date));
    }

    /**
     * Get bookings starts between the given dates.
     *
     * @param string $startsAt
     * @param string $endsAt
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsStartsBetween(string $startsAt, string $endsAt): MorphMany
    {
        return $this->bookings()
                    ->whereNull('canceled_at')
                    ->whereNotNull('starts_at')
                    ->where('starts_at', '>', new Carbon($startsAt))
                    ->where('starts_at', '<', new Carbon($endsAt));
    }

    /**
     * Get bookings ends before the given date.
     *
     * @param string $date
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsEndsBefore(string $date): MorphMany
    {
        return $this->bookings()
                    ->whereNull('canceled_at')
                    ->whereNotNull('ends_at')
                    ->where('ends_at', '<', new Carbon($date));
    }

    /**
     * Get bookings ends after the given date.
     *
     * @param string $date
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsEndsAfter(string $date): MorphMany
    {
        return $this->bookings()
                    ->whereNull('canceled_at')
                    ->whereNotNull('ends_at')
                    ->where('ends_at', '>', new Carbon($date));
    }

    /**
     * Get bookings ends between the given dates.
     *
     * @param string $startsAt
     * @param string $endsAt
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsEndsBetween(string $startsAt, string $endsAt): MorphMany
    {
        return $this->bookings()
                    ->whereNull('canceled_at')
                    ->whereNotNull('ends_at')
                    ->where('ends_at', '>', new Carbon($startsAt))
                    ->where('ends_at', '<', new Carbon($endsAt));
    }

    /**
     * Get bookings cancelled before the given date.
     *
     * @param string $date
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsCancelledBefore(string $date): MorphMany
    {
        return $this->bookings()
                    ->whereNotNull('canceled_at')
                    ->where('canceled_at', '<', new Carbon($date));
    }

    /**
     * Get bookings cancelled after the given date.
     *
     * @param string $date
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsCancelledAfter(string $date): MorphMany
    {
        return $this->bookings()
                    ->whereNotNull('canceled_at')
                    ->where('canceled_at', '>', new Carbon($date));
    }

    /**
     * Get bookings cancelled between the given dates.
     *
     * @param string $startsAt
     * @param string $endsAt
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsCancelledBetween(string $startsAt, string $endsAt): MorphMany
    {
        return $this->bookings()
                    ->whereNotNull('canceled_at')
                    ->where('canceled_at', '>', new Carbon($startsAt))
                    ->where('canceled_at', '<', new Carbon($endsAt));
    }

    /**
     * Get bookings between given start and end dates inclusively.
     * 
     * - Check for two things - 
     * Either starts_at or ends_at falling into any of the already existing booking intervals; 
     * or starts_at before and ends_at after those of an existing booking 
     * (because you can’t make a new booking from 9am to 14am, if one from 10am to 11am already exists.)
     *
     * @param string $startsAt
     * @param string $endsAt
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookingsBetween(string $startsAt, string $endsAt): MorphMany
    {
        return $this->bookings()
            ->whereNull('canceled_at')
            ->where(
                fn ($q) => $q->whereBetween('starts_at', [(new Carbon($startsAt)), (new Carbon($endsAt))->subSeconds(1)])
                    ->orWhereBetween('ends_at', [(new Carbon($startsAt))->addSeconds(1), (new Carbon($endsAt))])
                    ->orWhere(
                        fn ($q) => $q->where('starts_at', '<', (new Carbon($startsAt)))
                            ->where('ends_at', '>', (new Carbon($endsAt)))
                    )
            );
    }
}
