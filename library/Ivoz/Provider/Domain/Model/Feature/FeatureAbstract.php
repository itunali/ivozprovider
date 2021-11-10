<?php

declare(strict_types=1);

namespace Ivoz\Provider\Domain\Model\Feature;

use Assert\Assertion;
use Ivoz\Core\Application\DataTransferObjectInterface;
use Ivoz\Core\Domain\Model\ChangelogTrait;
use Ivoz\Core\Domain\Model\EntityInterface;
use Ivoz\Core\Application\ForeignKeyTransformerInterface;
use Ivoz\Provider\Domain\Model\Feature\Name;

/**
* FeatureAbstract
* @codeCoverageIgnore
*/
abstract class FeatureAbstract
{
    use ChangelogTrait;

    protected $iden;

    /**
     * @var Name | null
     */
    protected $name;

    /**
     * Constructor
     */
    protected function __construct(
        string $iden,
        Name $name
    ) {
        $this->setIden($iden);
        $this->setName($name);
    }

    abstract public function getId(): null|string|int;

    public function __toString(): string
    {
        return sprintf(
            "%s#%s",
            "Feature",
            (string) $this->getId()
        );
    }

    /**
     * @throws \Exception
     */
    protected function sanitizeValues(): void
    {
    }

    public static function createDto(string|int|null $id = null): FeatureDto
    {
        return new FeatureDto($id);
    }

    /**
     * @internal use EntityTools instead
     * @param null|FeatureInterface $entity
     */
    public static function entityToDto(?EntityInterface $entity, int $depth = 0): ?FeatureDto
    {
        if (!$entity) {
            return null;
        }

        Assertion::isInstanceOf($entity, FeatureInterface::class);

        if ($depth < 1) {
            return static::createDto($entity->getId());
        }

        if ($entity instanceof \Doctrine\ORM\Proxy\Proxy && !$entity->__isInitialized()) {
            return static::createDto($entity->getId());
        }

        $dto = $entity->toDto($depth - 1);

        return $dto;
    }

    /**
     * Factory method
     * @internal use EntityTools instead
     * @param FeatureDto $dto
     */
    public static function fromDto(
        DataTransferObjectInterface $dto,
        ForeignKeyTransformerInterface $fkTransformer
    ): static {
        Assertion::isInstanceOf($dto, FeatureDto::class);

        $name = new Name(
            $dto->getNameEn(),
            $dto->getNameEs(),
            $dto->getNameCa(),
            $dto->getNameIt()
        );

        $self = new static(
            $dto->getIden(),
            $name
        );

        ;

        $self->initChangelog();

        return $self;
    }

    /**
     * @internal use EntityTools instead
     * @param FeatureDto $dto
     */
    public function updateFromDto(
        DataTransferObjectInterface $dto,
        ForeignKeyTransformerInterface $fkTransformer
    ): static {
        Assertion::isInstanceOf($dto, FeatureDto::class);

        $name = new Name(
            $dto->getNameEn(),
            $dto->getNameEs(),
            $dto->getNameCa(),
            $dto->getNameIt()
        );

        $this
            ->setIden($dto->getIden())
            ->setName($name);

        return $this;
    }

    /**
     * @internal use EntityTools instead
     */
    public function toDto(int $depth = 0): FeatureDto
    {
        return self::createDto()
            ->setIden(self::getIden())
            ->setNameEn(self::getName()->getEn())
            ->setNameEs(self::getName()->getEs())
            ->setNameCa(self::getName()->getCa())
            ->setNameIt(self::getName()->getIt());
    }

    protected function __toArray(): array
    {
        return [
            'iden' => self::getIden(),
            'nameEn' => self::getName()->getEn(),
            'nameEs' => self::getName()->getEs(),
            'nameCa' => self::getName()->getCa(),
            'nameIt' => self::getName()->getIt()
        ];
    }

    protected function setIden(string $iden): static
    {
        Assertion::maxLength($iden, 100, 'iden value "%s" is too long, it should have no more than %d characters, but has %d characters.');

        $this->iden = $iden;

        return $this;
    }

    public function getIden(): string
    {
        return $this->iden;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    protected function setName(Name $name): static
    {
        $isEqual = $this->name && $this->name->equals($name);
        if ($isEqual) {
            return $this;
        }

        $this->name = $name;
        return $this;
    }
}
