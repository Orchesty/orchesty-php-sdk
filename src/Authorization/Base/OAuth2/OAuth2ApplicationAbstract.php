<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Authorization\Base\OAuth2;

use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Enum\AuthorizationTypeEnum;
use Hanaboso\CommonsBundle\Utils\DateTimeUtils;
use Hanaboso\PipesPhpSdk\Authorization\Base\ApplicationAbstract;
use Hanaboso\PipesPhpSdk\Authorization\Base\ApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Authorization\Exception\ApplicationInstallException;
use Hanaboso\PipesPhpSdk\Authorization\Exception\AuthorizationException;
use Hanaboso\PipesPhpSdk\Authorization\Provider\Dto\OAuth2Dto;
use Hanaboso\PipesPhpSdk\Authorization\Provider\OAuth2Provider;
use Hanaboso\PipesPhpSdk\Authorization\Utils\ApplicationUtils;
use Hanaboso\PipesPhpSdk\Authorization\Utils\ScopeFormatter;

/**
 * Class OAuth2ApplicationAbstract
 *
 * @package Hanaboso\PipesPhpSdk\Authorization\Base\OAuth2
 */
abstract class OAuth2ApplicationAbstract extends ApplicationAbstract implements OAuth2ApplicationInterface
{

    /**
     * @var OAuth2Provider
     */
    private $provider;

    /**
     * OAuth2ApplicationAbstract constructor.
     *
     * @param OAuth2Provider $provider
     */
    public function __construct(OAuth2Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return string
     */
    abstract public function getAuthUrl(): string;

    /**
     * @return string
     */
    abstract public function getTokenUrl(): string;

    /**
     * @return string
     */
    public function getAuthorizationType(): string
    {
        return AuthorizationTypeEnum::OAUTH2;
    }

    /**
     * @param ApplicationInstall $applicationInstall
     * @param array              $scopes
     * @param string             $separator
     */
    public function authorize(
        ApplicationInstall $applicationInstall,
        array $scopes = [],
        string $separator = ScopeFormatter::COMMA
    ): void
    {
        $this->provider->authorize($this->createDto($applicationInstall), $scopes, $separator);
    }

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return bool
     */
    public function isAuthorize(ApplicationInstall $applicationInstall): bool
    {
        return isset($applicationInstall->getSettings()[ApplicationInterface::AUTHORIZATION_SETTINGS][OAuth2ApplicationInterface::TOKEN]);
    }

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return ApplicationInstall
     * @throws AuthorizationException
     */
    public function refreshAuthorization(ApplicationInstall $applicationInstall): ApplicationInstall
    {
        $token = $this->provider->refreshAccessToken(
            $this->createDto($applicationInstall),
            $this->getTokens($applicationInstall)
        );

        return $applicationInstall->setSettings([ApplicationInterface::AUTHORIZATION_SETTINGS => [ApplicationInterface::TOKEN => $token]]);
    }

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return string
     */
    public function getFrontendRedirectUrl(ApplicationInstall $applicationInstall): string
    {
        return $applicationInstall->getSettings()[ApplicationInterface::AUTHORIZATION_SETTINGS][ApplicationInterface::REDIRECT_URL];
    }

    /**
     * @param ApplicationInstall $applicationInstall
     * @param string             $redirectUrl
     *
     * @return OAuth2ApplicationInterface
     */
    public function setFrontendRedirectUrl(
        ApplicationInstall $applicationInstall,
        string $redirectUrl): OAuth2ApplicationInterface
    {
        $applicationInstall->setSettings([ApplicationInterface::AUTHORIZATION_SETTINGS => [ApplicationInterface::REDIRECT_URL => $redirectUrl]]);

        return $this;
    }

    /**
     * @param ApplicationInstall $applicationInstall
     * @param array              $token
     *
     * @return OAuth2ApplicationInterface
     * @throws AuthorizationException
     */
    public function setAuthorizationToken(
        ApplicationInstall $applicationInstall,
        array $token): OAuth2ApplicationInterface
    {
        $accessToken = $this->provider->getAccessToken($this->createDto($applicationInstall), $token);
        if (array_key_exists('expires', $accessToken)) {
            $applicationInstall->setExpires(DateTimeUtils::getUtcDateTimeFromTimeStamp($accessToken['expires']));
        }

        $applicationInstall->setSettings([ApplicationInterface::AUTHORIZATION_SETTINGS => [ApplicationInterface::TOKEN => $token]]);

        return $this;
    }

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return string
     * @throws ApplicationInstallException
     */
    public function getAccessToken(ApplicationInstall $applicationInstall): string
    {
        if (isset($applicationInstall->getSettings()[ApplicationInterface::AUTHORIZATION_SETTINGS][ApplicationInterface::TOKEN][OAuth2Provider::ACCESS_TOKEN])) {

            return $applicationInstall->getSettings()[ApplicationInterface::AUTHORIZATION_SETTINGS][ApplicationInterface::TOKEN][OAuth2Provider::ACCESS_TOKEN];

        } else {
            throw new ApplicationInstallException('There is no access token',
                ApplicationInstallException::AUTHORIZATION_OAUTH2_ERROR);
        }
    }

    /**
     * @param string|null $url
     *
     * @return Uri
     */
    public function getUri(?string $url): Uri
    {
        return new Uri(sprintf('%s', ltrim($url ?? '', '/')));
    }

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return OAuth2Dto
     */
    protected function createDto(ApplicationInstall $applicationInstall): OAuth2Dto
    {
        $redirectUrl = ApplicationUtils::generateUrl();

        $dto = new OAuth2Dto($applicationInstall, $redirectUrl, $this->getAuthUrl(), $this->getTokenUrl());
        $dto->setCustomAppDependencies($applicationInstall->getUser(), $applicationInstall->getKey());

        return $dto;
    }

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return array
     */
    protected function getTokens(ApplicationInstall $applicationInstall): array
    {
        return $applicationInstall->getSettings()[ApplicationInterface::AUTHORIZATION_SETTINGS][ApplicationInterface::TOKEN];
    }

}
