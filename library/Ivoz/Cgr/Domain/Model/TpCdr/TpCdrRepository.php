<?php

namespace Ivoz\Cgr\Domain\Model\TpCdr;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ObjectRepository;

interface TpCdrRepository extends ObjectRepository, Selectable
{
    /**
     * @param string $originId
     * @return TpCdrInterface | null
     */
    public function getByOriginId(string $originId);


    /**
     * @param string $cgrid
     * @return TpCdrInterface | null
     */
    public function getDefaultRunByCgrid(string $cgrid);

    /**
     * @param string $cgrid
     * @return TpCdrInterface | null
     */
    public function getCarrierRunByCgrid(string $cgrid);

    /**
     * @param array<int> $cgrids
     * @return int affected rows
     * @psalm-suppress PossiblyUnusedReturnValue
     */
    public function fixCorruptedByCgrids(array $cgrids): int;
}
