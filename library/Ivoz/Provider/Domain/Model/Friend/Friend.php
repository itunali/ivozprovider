<?php

namespace Ivoz\Provider\Domain\Model\Friend;

use Assert\Assertion;
use Doctrine\Common\Collections\Criteria;
use Ivoz\Provider\Domain\Model\CallAcl\CallAcl;
use Ivoz\Provider\Domain\Model\Ddi\DdiInterface;
use Ivoz\Provider\Domain\Model\FriendsPattern\FriendsPattern;

/**
 * Friend
 */
class Friend extends FriendAbstract implements FriendInterface
{
    use FriendTrait;

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getChangeSet(): array
    {
        return parent::getChangeSet();
    }

    /**
     * Get id
     * @codeCoverageIgnore
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return string representation of this entity
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            "%s [%s]",
            $this->getName(),
            parent::__toString()
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function sanitizeValues()
    {
        if ($this->isDirectConnectivity() && !$this->getTransport()) {
            throw new \DomainException('Invalid empty transport');
        }

        if ($this->isDirectConnectivity() && !$this->getIp()) {
            throw new \DomainException('Invalid empty IP');
        }

        if ($this->isDirectConnectivity() && !$this->getPort()) {
            throw new \DomainException('Invalid empty port');
        }

        if ($this->isRegisterConnectivity() && !$this->getPassword()) {
            throw new \DomainException('Password cannot be empty for register friends');
        }

        if ($this->isInterPbxConnectivity() && $this->getPassword()) {
            throw new \DomainException('Password must be empty for intervpbx friends');
        }

        if ($this->isInterPbxConnectivity()) {
            // Force Inter company friends name
            $this->setName($this->getInterCompanyName());
            // Force DDI In mode
            $this->setDdiIn(FriendInterface::DDIIN_YES);
            // Set From Domain from target company
            $this->setFromDomain($this->getInterCompany()->getDomainUsers());
            // Empty From User
            $this->setFromUser(null);
        } else {
            $this->setInterCompany(null);
        }

        $this->setDomain(
            $this
                ->getCompany()
                ->getDomain()
        );
    }

    /**
     * @return bool
     */
    public function isInterPbxConnectivity(): bool
    {
        return $this->getDirectConnectivity() === self::DIRECTCONNECTIVITY_INTERVPBX;
    }

    /**
     * @return bool
     */
    public function isDirectConnectivity(): bool
    {
        return $this->getDirectConnectivity() === self::DIRECTCONNECTIVITY_YES;
    }

    /**
     * @return bool
     */
    public function isRegisterConnectivity(): bool
    {
        return $this->getDirectConnectivity() === self::DIRECTCONNECTIVITY_NO;
    }

    /**
     * @param string $number
     * @return \Ivoz\Provider\Domain\Model\Extension\ExtensionInterface | null
     */
    public function getInterCompanyExtension($number)
    {
        return $this
            ->getInterCompany()
            ->getExtension($number);
    }

    protected function setName(string $name): static
    {
        if (!empty($name)) {
            Assertion::regex(
                $name,
                '/^[a-zA-Z0-9_*]+$/',
                'Friend.name value "%s" does not match expression.'
            );
        }

        return parent::setName($name); // TODO: Change the autogenerated stub
    }

    /**
     * {@inheritDoc}
     * @see FriendAbstract::setIp
     * @deprecated this method will be protected
     */
    public function setIp(?string $ip = null): static
    {
        if (!empty($ip)) {
            Assertion::ip($ip);
        }
        return parent::setIp($ip);
    }

    /**
     * {@inheritDoc}
     * @see FriendAbstract::setPort
     * @deprecated this method will be protected
     */
    public function setPort(?int $port = null): static
    {
        if (!empty($port)) {
            Assertion::regex((string) $port, '/^[0-9]+$/');
            Assertion::lessThan($port, pow(2, 16), 'Friend.port provided "%s" is not lower than "%s".');
        }
        return parent::setPort($port);
    }

    /**
     * {@inheritDoc}
     * @see FriendAbstract::setPassword
     * @deprecated this method will be protected
     */
    public function setPassword(?string $password = null): static
    {
        if (empty($password)) {
            $password = null;
        } else {
            Assertion::regex(
                $password,
                '/^(?=.*[A-Z].*[A-Z].*[A-Z])(?=.*[+*_-])(?=.*[0-9].*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{10,}$/',
                'Friend.password value "%s" does not match expression.'
            );
        }

        return parent::setPassword($password);
    }

    /**
     * @return string
     */
    public function getContact(): string
    {
        return sprintf(
            "sip:%s@%s",
            $this->getName(),
            $this->getDomain()
        );
    }

    /**
     * @return string
     */
    public function getSorcery(): string
    {
        return sprintf(
            "b%dc%df%d_%s",
            $this
                ->getCompany()
                ->getBrand()
                ->getId(),
            $this->getCompany()->getId(),
            $this->getId(),
            $this->getName()
        );
    }

    /**
     * @param string $exten
     * @return bool
     */
    public function checkExtension($exten)
    {
        if ($this->isInterPbxConnectivity()) {
            // Inter-vPBX can call to any Extension pointing to user
            $extension = $this->getInterCompany()->getExtension($exten);
            if (is_null($extension)) {
                return false;
            }
            return true;
        }

        $patterns = $this->getPatterns();
        /**
         * @var FriendsPattern $pattern
         */
        foreach ($patterns as $pattern) {
            $regexp = '/' . $pattern->getRegExp() . '/';
            if (preg_match($regexp, $exten)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $exten
     * @return bool canCall
     */
    public function isAllowedToCall($exten)
    {
        /**
         * @var CallAcl $callAcl
         */
        $callAcl = $this->getCallAcl();
        if (empty($callAcl)) {
            return true;
        }
        return $callAcl->dstIsCallable($exten);
    }

    public function getLanguageCode(): string
    {
        $language = $this->getLanguage();
        if (!$language) {
            return $this
                ->getCompany()
                ->getLanguageCode();
        }

        return $language->getIden();
    }

    /**
     * Get Friend outgoingDdi
     * If no Ddi is assigned, retrieve company's default Ddi
     *
     * @return \Ivoz\Provider\Domain\Model\Ddi\DdiInterface|null
     */
    public function getOutgoingDdi(): ?DdiInterface
    {
        $ddi = parent::getOutgoingDdi();
        if (empty($ddi)) {
            $ddi = $this
                ->getCompany()
                ->getOutgoingDdi();
        }

        return $ddi;
    }


    /**
     * @return string
     * @throws \Exception
     */
    private function getInterCompanyName(): string
    {
        $company = $this->getCompany();

        $interCompany = $this->getInterCompany();

        Assertion::notNull(
            $interCompany,
            'InterCompany Friend without target company.'
        );

        /*
         * Return the same name for Interconnected friends no matter what company is its owner.
         */
        if ($interCompany->getId() > $company->getId()) {
            $companyOneId = $company->getId();
            $companyTwoId = $interCompany->getId();
        } else {
            $companyOneId = $interCompany->getId();
            $companyTwoId = $company->getId();
        }

        return sprintf("InterCompany%d_%d", $companyOneId, $companyTwoId);
    }
}
