<?php

namespace Affiliate\Domain;

class Item
{
    public function __construct(
        private string $clientId,
        private string $userAgent,
        private string $location,
        private string $referer,
        private \DateTime $dateTime,
    ) {}

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getReferer(): string
    {
        return $this->referer;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }
}