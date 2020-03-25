<?php

namespace Sendportal\Base\Events\Webhooks;

class SendgridWebhookEvent
{
    /**
     * @var array
     */
    public $payload;

    /**
     * AutomationDispatchEvent constructor
     *
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}