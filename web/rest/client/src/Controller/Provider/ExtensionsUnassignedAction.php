<?php

namespace Controller\Provider;

use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use Ivoz\Provider\Domain\Model\Administrator\AdministratorInterface;
use Ivoz\Provider\Domain\Model\Company\CompanyInterface;
use Ivoz\Provider\Domain\Model\Extension\Extension;
use Ivoz\Provider\Domain\Model\Extension\ExtensionDto;
use Ivoz\Provider\Domain\Model\Extension\ExtensionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ExtensionsUnassignedAction
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private ExtensionRepository $extensionRepository,
        private RequestStack $requestStack,
        private DenormalizerInterface $denormalizer
    ) {
    }

    public function __invoke()
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $token =  $this->tokenStorage->getToken();

        if (!$token || !$token->getUser()) {
            throw new ResourceClassNotFoundException('User not found');
        }

        /** @var AdministratorInterface $admin */
        $admin = $token->getUser();
        /** @var CompanyInterface $company */
        $company = $admin->getCompany();

        $includeId = (int) $request->query->get('_includeId');
        $includeIds = $includeId
            ? [$includeId]
            : [];

        $extensions = $this->extensionRepository->findUnassignedByCompanyId(
            (int) $company->getId(),
            $includeIds
        );

        $response = [];
        foreach ($extensions as $extension) {
            $response[] = $this->denormalizer->denormalize(
                [],
                Extension::class,
                $request->getRequestFormat(),
                [
                    'object_to_populate' => $extension,
                    'operation_normalization_context' => ExtensionDto::CONTEXT_COLLECTION
                ]
            );
        }

        return $response;
    }
}
