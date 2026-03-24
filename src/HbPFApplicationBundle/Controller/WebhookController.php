<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\HbPFApplicationBundle\Controller;

use Hanaboso\PipesPhpSdk\Application\Exception\ApplicationInstallException;
use Hanaboso\PipesPhpSdk\HbPFApplicationBundle\Handler\WebhookHandler;
use Hanaboso\Utils\System\ControllerUtils;
use Hanaboso\Utils\Traits\ControllerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

/**
 * Class WebhookController
 *
 * @package Hanaboso\PipesPhpSdk\HbPFApplicationBundle\Controller
 */
final class WebhookController
{

    use ControllerTrait;

    /**
     * WebhookController constructor.
     *
     * @param WebhookHandler $webhookHandler
     */
    public function __construct(private WebhookHandler $webhookHandler)
    {
    }

    /**
     * @param Request $request
     * @param string  $key
     * @param string  $user
     * @param string  $sdk
     *
     * @return Response
     */
    #[Route('/webhook/applications/{key}/users/{user}/sdk/{sdk}/subscribe', methods: ['POST'])]
    public function subscribeWebhooksAction(Request $request, string $key, string $user, string $sdk): Response
    {
        try {
            $this->webhookHandler->subscribeWebhooks($key, $user, $sdk, $request->request->all());

            return $this->getResponse([]);
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
    #[Route('/webhook/applications/{key}/users/{user}/sdk/{sdk}/unsubscribe', methods: ['POST'])]
    public function unsubscribeWebhooksAction(Request $request, string $key, string $user, string $sdk): Response
    {
        try {
            $this->webhookHandler->unsubscribeWebhooks($key, $user, $sdk, $request->request->all());

            return $this->getResponse([]);
        } catch (ApplicationInstallException $e) {
            return $this->getErrorResponse($e, 404, ControllerUtils::NOT_FOUND);
        } catch (Throwable $e) {
            return $this->getErrorResponse($e);
        }
    }

}
