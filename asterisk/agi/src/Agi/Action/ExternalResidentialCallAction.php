<?php

namespace Agi\Action;
use Agi\ChannelInfo;
use Agi\Wrapper;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @class ExternalResidentialCallAction
 *
 * @brief Manage outgoing external calls generated by a residential devices
 *
 */
class ExternalResidentialCallAction extends ExternalCallAction
{
    /**
     * Destination number in E.164 format
     *
     * @var string
     */
    protected $number;

    /**
     * ExternalResidentialCallAction constructor.
     *
     * @param Wrapper $agi
     * @param ChannelInfo $channelInfo
     * @param EntityManagerInterface $em
     */
    public function __construct(
        Wrapper $agi,
        ChannelInfo $channelInfo,
        EntityManagerInterface $em
    )
    {
        parent::__construct($agi, $channelInfo, $em);
    }

    /**
     * @param string|null $number
     * @return $this
     */
    public function setDestination(string $number = null)
    {
        $this->number = $number;
        return $this;
    }

    public function process()
    {
        /** @var \Ivoz\Provider\Domain\Model\ResidentialDevice\ResidentialDeviceInterface $residential */
        $residential = $this->channelInfo->getChannelCaller();
        $number = $this->number;

        // Check if the diversion header contains a valid number
        if ($this->agi->getRedirecting('count')) {
            $diversionNum = $this->agi->getRedirecting('from-num');
            $ddi = $residential->getDDI($diversionNum);
            if (empty($ddi)) {
                // Not a Residential Device DDI. Remove it.
                $this->agi->error("Removing invalid diversion header from %s", $diversionNum);
                $this->agi->setRedirecting('count', 0);
            } else {
                $this->agi->verbose("Allowing valid diversion header from %s", $diversionNum);
            }
        } else {
            // Allow identification from any Residential Device DDI
            $callerIdNum = $this->agi->getCallerIdNum();
            $ddi = $residential->getDDI($callerIdNum);

            if (!empty($ddi)) {
                $this->agi->notice("Device %s presented origin matches account DDI %s", $residential, $ddi);
            }
        }

        // Update caller displayed number
        if (!isset($ddi)) {
            $ddi = $residential->getOutgoingDDI();
            if ($ddi) {
                $callerIdNum = $this->agi->getCallerIdNum();
                $this->agi->notice("Using fallback DDI %s for device %s because %s does not match any DDI.",$ddi, $residential, $callerIdNum);
                $this->agi->setCallerIdNum($ddi->getDdie164());
            } else {
                $this->agi->error("Residential Device %s has not OutgoingDDI configured", $residential);
                $this->agi->decline();
                return;
            }
        }

        // Check if DDI has recordings enabled
        $this->checkDDIRecording($ddi);

        // Call the PSJIP endpoint
        $this->agi->setVariable("DIAL_DST", "PJSIP/" . $number . '@proxytrunks');
        $this->agi->setVariable("DIAL_OPTS", "");
        $this->agi->setVariable("DIAL_TIMEOUT", "");
        $this->agi->redirect('call-world', $number);
    }
}
