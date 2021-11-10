<?php

declare(strict_types=1);

namespace Ivoz\Provider\Domain\Model\RetailAccount;

use Ivoz\Core\Application\DataTransferObjectInterface;
use Ivoz\Core\Application\ForeignKeyTransformerInterface;
use Ivoz\Ast\Domain\Model\PsEndpoint\PsEndpointInterface;
use Ivoz\Ast\Domain\Model\PsIdentify\PsIdentifyInterface;
use Ivoz\Provider\Domain\Model\Ddi\DdiInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Ivoz\Provider\Domain\Model\CallForwardSetting\CallForwardSettingInterface;

/**
* @codeCoverageIgnore
*/
trait RetailAccountTrait
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var PsEndpointInterface
     * mappedBy retailAccount
     */
    protected $psEndpoint;

    /**
     * @var PsIdentifyInterface
     * mappedBy retailAccount
     */
    protected $psIdentify;

    /**
     * @var ArrayCollection
     * DdiInterface mappedBy retailAccount
     */
    protected $ddis;

    /**
     * @var ArrayCollection
     * CallForwardSettingInterface mappedBy retailAccount
     */
    protected $callForwardSettings;

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct(...func_get_args());
        $this->ddis = new ArrayCollection();
        $this->callForwardSettings = new ArrayCollection();
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
        if (!is_null($dto->getPsEndpoint())) {
            $self->setPsEndpoint(
                $fkTransformer->transform(
                    $dto->getPsEndpoint()
                )
            );
        }

        if (!is_null($dto->getPsIdentify())) {
            $self->setPsIdentify(
                $fkTransformer->transform(
                    $dto->getPsIdentify()
                )
            );
        }

        if (!is_null($dto->getDdis())) {
            $self->replaceDdis(
                $fkTransformer->transformCollection(
                    $dto->getDdis()
                )
            );
        }

        if (!is_null($dto->getCallForwardSettings())) {
            $self->replaceCallForwardSettings(
                $fkTransformer->transformCollection(
                    $dto->getCallForwardSettings()
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
        if (!is_null($dto->getPsEndpoint())) {
            $this->setPsEndpoint(
                $fkTransformer->transform(
                    $dto->getPsEndpoint()
                )
            );
        }

        if (!is_null($dto->getPsIdentify())) {
            $this->setPsIdentify(
                $fkTransformer->transform(
                    $dto->getPsIdentify()
                )
            );
        }

        if (!is_null($dto->getDdis())) {
            $this->replaceDdis(
                $fkTransformer->transformCollection(
                    $dto->getDdis()
                )
            );
        }

        if (!is_null($dto->getCallForwardSettings())) {
            $this->replaceCallForwardSettings(
                $fkTransformer->transformCollection(
                    $dto->getCallForwardSettings()
                )
            );
        }
        $this->sanitizeValues();

        return $this;
    }

    /**
     * @internal use EntityTools instead
     */
    public function toDto(int $depth = 0): RetailAccountDto
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

    public function setPsEndpoint(PsEndpointInterface $psEndpoint): static
    {
        $this->psEndpoint = $psEndpoint;

        return $this;
    }

    public function getPsEndpoint(): ?PsEndpointInterface
    {
        return $this->psEndpoint;
    }

    public function setPsIdentify(PsIdentifyInterface $psIdentify): static
    {
        $this->psIdentify = $psIdentify;

        return $this;
    }

    public function getPsIdentify(): ?PsIdentifyInterface
    {
        return $this->psIdentify;
    }

    public function addDdi(DdiInterface $ddi): RetailAccountInterface
    {
        $this->ddis->add($ddi);

        return $this;
    }

    public function removeDdi(DdiInterface $ddi): RetailAccountInterface
    {
        $this->ddis->removeElement($ddi);

        return $this;
    }

    public function replaceDdis(ArrayCollection $ddis): RetailAccountInterface
    {
        $updatedEntities = [];
        $fallBackId = -1;
        foreach ($ddis as $entity) {
            $index = $entity->getId() ? $entity->getId() : $fallBackId--;
            $updatedEntities[$index] = $entity;
            $entity->setRetailAccount($this);
        }
        $updatedEntityKeys = array_keys($updatedEntities);

        foreach ($this->ddis as $key => $entity) {
            $identity = $entity->getId();
            if (in_array($identity, $updatedEntityKeys)) {
                $this->ddis->set($key, $updatedEntities[$identity]);
            } else {
                $this->ddis->remove($key);
            }
            unset($updatedEntities[$identity]);
        }

        foreach ($updatedEntities as $entity) {
            $this->addDdi($entity);
        }

        return $this;
    }

    public function getDdis(Criteria $criteria = null): array
    {
        if (!is_null($criteria)) {
            return $this->ddis->matching($criteria)->toArray();
        }

        return $this->ddis->toArray();
    }

    public function addCallForwardSetting(CallForwardSettingInterface $callForwardSetting): RetailAccountInterface
    {
        $this->callForwardSettings->add($callForwardSetting);

        return $this;
    }

    public function removeCallForwardSetting(CallForwardSettingInterface $callForwardSetting): RetailAccountInterface
    {
        $this->callForwardSettings->removeElement($callForwardSetting);

        return $this;
    }

    public function replaceCallForwardSettings(ArrayCollection $callForwardSettings): RetailAccountInterface
    {
        $updatedEntities = [];
        $fallBackId = -1;
        foreach ($callForwardSettings as $entity) {
            $index = $entity->getId() ? $entity->getId() : $fallBackId--;
            $updatedEntities[$index] = $entity;
            $entity->setRetailAccount($this);
        }
        $updatedEntityKeys = array_keys($updatedEntities);

        foreach ($this->callForwardSettings as $key => $entity) {
            $identity = $entity->getId();
            if (in_array($identity, $updatedEntityKeys)) {
                $this->callForwardSettings->set($key, $updatedEntities[$identity]);
            } else {
                $this->callForwardSettings->remove($key);
            }
            unset($updatedEntities[$identity]);
        }

        foreach ($updatedEntities as $entity) {
            $this->addCallForwardSetting($entity);
        }

        return $this;
    }

    public function getCallForwardSettings(Criteria $criteria = null): array
    {
        if (!is_null($criteria)) {
            return $this->callForwardSettings->matching($criteria)->toArray();
        }

        return $this->callForwardSettings->toArray();
    }
}
