<?php

namespace Affiliate\Domain;

class SuccessLink
{
    public function __construct(
        private string $clientId,
        private string $partnerLink,
    ){}

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getPartnerLink(): string
    {
        return $this->partnerLink;
    }
}
