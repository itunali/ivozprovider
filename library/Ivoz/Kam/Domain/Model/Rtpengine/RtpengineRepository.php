<?php

namespace Ivoz\Kam\Domain\Model\Rtpengine;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

interface RtpengineRepository extends ObjectRepository, Selectable
{
}
