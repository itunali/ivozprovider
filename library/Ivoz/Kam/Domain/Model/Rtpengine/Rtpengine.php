<?php

namespace Ivoz\Kam\Domain\Model\Rtpengine;

use Ivoz\Provider\Domain\Model\MediaRelaySet\MediaRelaySetInterface;

/**
 * Rtpengine
 */
class Rtpengine extends RtpengineAbstract implements RtpengineInterface
{
    use RtpengineTrait;

    /**
     * Get id
     * @codeCoverageIgnore
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setMediaRelaySet(?MediaRelaySetInterface $mediaRelaySet = null): self
    {
        if (!is_null($mediaRelaySet)) {
            $this->setSetid(
                (string) $mediaRelaySet->getId()
            );
        }

        return parent::setMediaRelaySet($mediaRelaySet);
    }
}
