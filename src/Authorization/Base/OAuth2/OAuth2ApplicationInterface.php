<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Authorization\Base\OAuth2;

use Hanaboso\PipesPhpSdk\Application\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;

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

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return string
     */
    public function authorize(ApplicationInstall $applicationInstall): string;

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
        string $redirectUrl,
    ): OAuth2ApplicationInterface;

    /**
     * @param ApplicationInstall $applicationInstall
     * @param mixed[]            $token
     *
     * @return OAuth2ApplicationInterface
     */
    public function setAuthorizationToken(
        ApplicationInstall $applicationInstall,
        array $token,
    ): OAuth2ApplicationInterface;

}
