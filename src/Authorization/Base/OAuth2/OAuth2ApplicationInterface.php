<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Authorization\Base\OAuth2;

use Hanaboso\PipesPhpSdk\Authorization\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Document\ApplicationInstall;

/**
 * Interface OAuth2ApplicationInterface
 *
 * @package Hanaboso\PipesPhpSdk\Authorization\Base\OAuth2
 */
interface OAuth2ApplicationInterface extends ApplicationInterface
{

    public const  CLIENT_ID             = 'client_id';
    public const  CLIENT_SECRET         = 'client_secret';
    public const  FRONTEND_REDIRECT_URL = 'frontend_redirect_url';
    public const  API_KEYPOINT          = 'api_keypoint';

    /**
     * @param ApplicationInstall $applicationInstall
     */
    public function authorize(ApplicationInstall $applicationInstall): void;

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return ApplicationInstall
     */
    public function refreshAuthorization(ApplicationInstall $applicationInstall): ApplicationInstall;

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return string
     */
    public function getFrontendRedirectUrl(ApplicationInstall $applicationInstall): string;

    /**
     * @param ApplicationInstall $applicationInstall
     * @param string             $redirectUrl
     *
     * @return OAuth2ApplicationInterface
     */
    public function setFrontendRedirectUrl(
        ApplicationInstall $applicationInstall,
        string $redirectUrl
    ): OAuth2ApplicationInterface;

    /**
     * @param ApplicationInstall $applicationInstall
     * @param array              $token
     *
     * @return OAuth2ApplicationInterface
     */
    public function setAuthorizationToken(
        ApplicationInstall $applicationInstall,
        array $token
    ): OAuth2ApplicationInterface;

}