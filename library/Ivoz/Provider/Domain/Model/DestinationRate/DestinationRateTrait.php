<?php

declare(strict_types=1);

namespace Ivoz\Provider\Domain\Model\DestinationRate;

use Ivoz\Core\Application\DataTransferObjectInterface;
use Ivoz\Core\Application\ForeignKeyTransformerInterface;
use Ivoz\Cgr\Domain\Model\TpRate\TpRateInterface;
use Ivoz\Cgr\Domain\Model\TpDestinationRate\TpDestinationRateInterface;

/**
* @codeCoverageIgnore
*/
trait DestinationRateTrait
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var TpRateInterface
     * mappedBy destinationRate
     */
    protected $tpRate;

    /**
     * @var TpDestinationRateInterface
     * mappedBy destinationRate
     */
    protected $tpDestinationRate;

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct(...func_get_args());
    }

    abstract protected function sanitizeValues(): void;

    /**
     * Factory method
     * @internal use EntityTools instead
     */
    public static function fromDto(
        DataTransferObjectInterface $dto,
        ForeignKeyTransformerInterface $fkTransformer
    ): static {
        /** @var static $self */
        $self = parent::fromDto($dto, $fkTransformer);
        if (!is_null($dto->getTpRate())) {
            $self->setTpRate(
                $fkTransformer->transform(
                    $dto->getTpRate()
                )
            );
        }

        if (!is_null($dto->getTpDestinationRate())) {
            $self->setTpDestinationRate(
                $fkTransformer->transform(
                    $dto->getTpDestinationRate()
                )
            );
        }

        $self->sanitizeValues();
        if ($dto->getId()) {
            $self->id = $dto->getId();
            $self->initChangelog();
        }

        return $self;
    }

    /**
     * @internal use EntityTools instead
     */
    public function updateFromDto(
        DataTransferObjectInterface $dto,
        ForeignKeyTransformerInterface $fkTransformer
    ): static {
        parent::updateFromDto($dto, $fkTransformer);
        if (!is_null($dto->getTpRate())) {
            $this->setTpRate(
                $fkTransformer->transform(
                    $dto->getTpRate()
                )
            );
        }

        if (!is_null($dto->getTpDestinationRate())) {
            $this->setTpDestinationRate(
                $fkTransformer->transform(
                    $dto->getTpDestinationRate()
                )
            );
        }
        $this->sanitizeValues();

        return $this;
    }

    /**
     * @internal use EntityTools instead
     */
    public function toDto(int $depth = 0): DestinationRateDto
    {
        $dto = parent::toDto($depth);
        return $dto
            ->setId($this->getId());
    }

    protected function __toArray(): array
    {
        return parent::__toArray() + [
            'id' => self::getId()
        ];
    }

    public function setTpRate(TpRateInterface $tpRate): static
    {
        $this->tpRate = $tpRate;

        return $this;
    }

    public function getTpRate(): ?TpRateInterface
    {
        return $this->tpRate;
    }

    public function setTpDestinationRate(TpDestinationRateInterface $tpDestinationRate): static
    {
        $this->tpDestinationRate = $tpDestinationRate;

        return $this;
    }

    public function getTpDestinationRate(): ?TpDestinationRateInterface
    {
        return $this->tpDestinationRate;
    }
}
