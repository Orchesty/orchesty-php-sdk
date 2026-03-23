<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFApplicationBundle\Controller;

use Hanaboso\PipesPhpSdk\Application\Exception\ApplicationInstallException;
use Hanaboso\PipesPhpSdk\Authorization\Provider\OAuth2Provider;
use Hanaboso\PipesPhpSdk\HbPFApplicationBundle\Handler\ApplicationHandler;
use Hanaboso\Utils\String\Json;
use Hanaboso\Utils\System\ControllerUtils;
use Hanaboso\Utils\Traits\ControllerTrait;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

/**
 * Class ApplicationController
 *
 * @package Hanaboso\PipesPhpSdk\HbPFApplicationBundle\Controller
 */
final class ApplicationController
{

    use ControllerTrait;

    /**
     * ApplicationController constructor.
     *
     * @param ApplicationHandler $applicationHandler
     */
    public function __construct(private ApplicationHandler $applicationHandler)
    {
    }

    /**
     * @return Response
     */
    #[Route('/applications', methods: ['GET'])]
    #[Route('/applications/', methods: ['GET'])]
    public function listOfApplicationsAction(): Response
    {
        try {
            return $this->getResponse($this->applicationHandler->getApplications());
        } catch (Throwable $t) {
            return $this->getErrorResponse($t);
        }
    }

    /**
     * @param Request $request
     * @param string  $sdk
     *
     * @return Response
     */
    #[Route('/applications/sdk/{sdk}/limits', methods: ['POST'])]
    #[Route('/applications/sdk/{sdk}/limits/', methods: ['POST'])]
    public function listOfApplicationsLimitsAction(Request $request, string $sdk): Response
    {
        try {
            $parameters = Json::decode($request->getContent());

            return $this->getResponse(
                $this->applicationHandler->getApplicationsLimits(
                    $parameters['user'] ?? '',
                    $sdk,
                    $parameters['applications']?? '',
                ),
            );
        } catch (Throwable $t) {
            return $this->getErrorResponse($t);
        }
    }

    /**
     * @param string $key
     *
     * @return Response
     */
    #[Route('/applications/{key}', methods: ['GET'])]
    public function getApplicationAction(string $key): Response
    {
        try {
            return $this->getResponse($this->applicationHandler->getApplicationByKey($key));
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param string $key
     *
     * @return Response
     */
    #[Route('/applications/{key}/sync/list', methods: ['GET'])]
    public function getSynchronousActionsAction(string $key): Response
    {
        try {
            return $this->getResponse($this->applicationHandler->getSynchronousActions($key));
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param Request $request
     * @param string  $key
     * @param string  $method
     *
     * @return Response
     */
    #[Route('/applications/{key}/sync/{method}', methods: ['GET', 'POST'])]
    public function runSynchronousActionsAction(Request $request, string $key, string $method): Response
    {
        try {
            $res = $this->applicationHandler->runSynchronousAction($key, $method, $request);
            $res = is_array($res) ? $res : [$res];

            return $this->getResponse($res);
        } catch (ApplicationInstallException $e) {
            if ($e->getCode() === ApplicationInstallException::METHOD_NOT_FOUND) {
                return $this->getErrorResponse($e);
            }

            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param Request $request
     * @param string  $key
     * @param string  $user
     * @param string  $sdk
     *
     * @return Response
     */
    #[Route('/applications/{key}/users/{user}/sdk/{sdk}/authorize', methods: ['GET'])]
    public function authorizeApplicationAction(Request $request, string $key, string $user, string $sdk): Response
    {
        try {
            /** @var string $redirectUrl */
            $redirectUrl = $request->query->get('redirect_url', '');
            if ($redirectUrl === '') {
                throw new InvalidArgumentException('Missing "redirect_url" query parameter.');
            }

            $url = $this->applicationHandler->authorizeApplication($key, $user, $sdk, $redirectUrl);

            return $this->getResponse(['authorizeUrl' => $url]);
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param Request $request
     * @param string  $key
     * @param string  $user
     * @param string  $sdk
     *
     * @return Response
     */
    #[Route('/applications/{key}/users/{user}/sdk/{sdk}/authorize/token', methods: ['GET'])]
    public function setAuthorizationTokenAction(Request $request, string $key, string $user, string $sdk): Response
    {
        try {
            $url = $this->applicationHandler->saveAuthToken($key, $user, $sdk, $request->query->all());

            return $this->getResponse(['redirectUrl' => $url]);
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/applications/authorize/token', methods: ['GET'])]
    public function setAuthorizationTokenQueryAction(Request $request): Response
    {
        try {
            [$user, $key, $sdk] = OAuth2Provider::stateDecode($request->query->getString('state'));

            $url = $this->applicationHandler->saveAuthToken($key, $user, $sdk, $request->query->all());

            return $this->getResponse(['redirectUrl' => $url]);
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param string $user
     * @param string $sdk
     *
     * @return Response
     */
    #[Route('/applications/users/{user}/sdk/{sdk}', methods: ['GET'])]
    public function getUsersApplicationAction(string $user, string $sdk): Response
    {
        try {
            return $this->getResponse($this->applicationHandler->getApplicationsByUser($user, $sdk));
        } catch (Throwable $t) {
            return $this->getErrorResponse($t);
        }
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     *
     * @return Response
     */
    #[Route('/applications/{key}/users/{user}/sdk/{sdk}', methods: ['GET'])]
    public function getApplicationDetailAction(string $key, string $user, string $sdk): Response
    {
        try {
            return $this->getResponse($this->applicationHandler->getApplicationByKeyAndUser($key, $user, $sdk));
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     *
     * @return Response
     */
    #[Route('/applications/{key}/users/{user}/sdk/{sdk}/install', methods: ['POST'])]
    public function installApplicationAction(string $key, string $user, string $sdk): Response
    {
        try {
            return $this->getResponse($this->applicationHandler->installApplication($key, $user, $sdk));
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param string $key
     * @param string $user
     * @param string $sdk
     *
     * @return Response
     */
    #[Route('/applications/{key}/users/{user}/sdk/{sdk}/uninstall', methods: ['DELETE'])]
    public function uninstallApplicationAction(string $key, string $user, string $sdk): Response
    {
        try {
            return $this->getResponse($this->applicationHandler->uninstallApplication($key, $user, $sdk));
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param Request $request
     * @param string  $key
     * @param string  $user
     * @param string  $sdk
     *
     * @return Response
     */
    #[Route('/applications/{key}/users/{user}/sdk/{sdk}/changeState', methods: ['PUT'])]
    public function changeStateOfApplication(Request $request, string $key, string $user, string $sdk): Response
    {
        try {
            return $this->getResponse($this->applicationHandler->changeStateOfApplication(
                $key,
                $user,
                $sdk,
                $request->request->getBoolean('enabled'),
            ));
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param Request $request
     * @param string  $key
     * @param string  $user
     * @param string  $sdk
     *
     * @return Response
     */
    #[Route('/applications/{key}/users/{user}/sdk/{sdk}/settings', methods: ['PUT'])]
    public function updateApplicationSettingsAction(Request $request, string $key, string $user, string $sdk): Response
    {
        try {
            return $this->getResponse(
                $this->applicationHandler->updateApplicationSettings($key, $user, $sdk, $request->request->all()),
            );
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

    /**
     * @param Request $request
     * @param string  $key
     * @param string  $user
     * @param string  $sdk
     *
     * @return Response
     */
    #[Route('/applications/{key}/users/{user}/sdk/{sdk}/password', methods: ['PUT'])]
    public function saveApplicationPasswordAction(Request $request, string $key, string $user, string $sdk): Response
    {
        try {
            return $this->getResponse(
                $this->applicationHandler->updateApplicationPassword(
                    $key,
                    $user,
                    $sdk,
                    $request->request->all(),
                ),
            );
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

}
