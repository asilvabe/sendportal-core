<?php

declare(strict_types=1);

namespace Sendportal\Base\Services\Subscribers\Segments;

use Sendportal\Base\Repositories\SubscriberTenantRepository;
use Exception;
use Illuminate\Support\Collection;

class ApiSubscriberSegmentService
{
    /** @var SubscriberTenantRepository */
    private $subscribers;

    public function __construct(SubscriberTenantRepository $subscribers)
    {
        $this->subscribers = $subscribers;
    }

    /**
     * Add segments to a subscriber.
     *
     * @param int $teamId
     * @param int $subscriberId
     * @param Collection $segmentIds
     *
     * @return Collection
     * @throws Exception
     */
    public function store(int $teamId, int $subscriberId, Collection $segmentIds): Collection
    {
        $subscriber = $this->subscribers->find($teamId, $subscriberId);

        /** @var Collection $existingSegments */
        $existingSegments = $subscriber->segments()->pluck('segment.id')->toBase();

        $segmentsToStore = $segmentIds->diff($existingSegments);

        $subscriber->segments()->attach($segmentsToStore);

        return $subscriber->segments->toBase();
    }

    /**
     * Sync the list of segments a subscriber is associated with.
     *
     * @param int $teamId
     * @param int $subscriberId
     * @param Collection $segmentIds
     *
     * @return Collection
     * @throws Exception
     */
    public function update(int $teamId, int $subscriberId, Collection $segmentIds): Collection
    {
        $subscriber = $this->subscribers->find($teamId, $subscriberId, ['segments']);

        $subscriber->segments()->sync($segmentIds);

        $subscriber->load('segments');

        return $subscriber->segments->toBase();
    }

    /**
     * Remove segments from a subscriber.
     *
     * @param int $teamId
     * @param int $subscriberId
     * @param Collection $segmentIds
     *
     * @return Collection
     * @throws Exception
     */
    public function destroy(int $teamId, int $subscriberId, Collection $segmentIds): Collection
    {
        $subscriber = $this->subscribers->find($teamId, $subscriberId);

        $subscriber->segments()->detach($segmentIds);

        return $subscriber->segments;
    }
}