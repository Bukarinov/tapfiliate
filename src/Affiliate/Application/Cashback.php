<?php

namespace Affiliate\Application;

use Affiliate\Domain\LogItem;
use Affiliate\Domain\SuccessLink;

class Cashback
{
    /**
     * @var LogItem[]
     */
    public function __construct(
        private array $logItems,
    ){
        $this->sortLogItemsByDate();
    }

    /**
     * @param string $checkoutLink
     * @param array $partnersHosts
     * @return SuccessLink[]
     */
    public function getSuccessLinks(string $checkoutLink, array $partnersHosts): array
    {
        $result = [];
        $refereesPaths = [];

        foreach ($this->logItems as $item) {
            /** @var LogItem $item */
            $browserId = $item->getBrowserId();
            if (!isset($refereesPaths[$browserId])) {
                if ($item->getLocation() === $checkoutLink) {
                    $refereesPaths[$browserId] = $item;
                }
            } else {
                foreach ($partnersHosts as $partnerHost) {
                    $refererHost = parse_url($item->getReferer(), PHP_URL_HOST);
                    if ($refererHost === $partnerHost) {
                        $result[] = new SuccessLink(
                            clientId: $item->getClientId(),
                            partnerLink: $item->getReferer(),
                        );
                        unset($refereesPaths[$browserId]);

                        break;
                    }
                }
            }
        }

        return $result;
    }

    private function sortLogItemsByDate(): void
    {
        usort($this->logItems, function(LogItem $a, LogItem $b) {
            return $b->getDateTime() <=> $a->getDateTime();
        });
    }
}
