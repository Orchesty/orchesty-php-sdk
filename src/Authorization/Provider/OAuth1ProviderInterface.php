<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Authorization\Provider;

use Hanaboso\PipesPhpSdk\Authorization\Exception\AuthorizationException;
use Hanaboso\PipesPhpSdk\Authorization\Provider\Dto\OAuth1DtoInterface;

/**
 * Interface OAuth1ProviderInterface
 *
 * @package Hanaboso\PipesPhpSdk\Authorization\Provider
 */
interface OAuth1ProviderInterface extends OAuthProviderInterface
{

    /**
     * @param OAuth1DtoInterface $dto
     * @param string             $tokenUrl
     * @param string             $authorizeUrl
     * @param callable           $saveOauthStuffs
     * @param string[]           $scopes
     *
     * @return string
     * @throws AuthorizationException
     */
    public function authorize(
        OAuth1DtoInterface $dto,
        string $tokenUrl,
        string $authorizeUrl,
        callable $saveOauthStuffs,
        array $scopes = [],
    ): string;

    /**
     * @param OAuth1DtoInterface $dto
     * @param mixed[]            $request
     * @param string             $accessTokenUrl
     *
     * @return mixed[]
     * @throws AuthorizationException
     */
    public function getAccessToken(OAuth1DtoInterface $dto, array $request, string $accessTokenUrl): array;

    /**
     * @param OAuth1DtoInterface $dto
     * @param string             $method
     * @param string             $url
     *
     * @return string
     * @throws AuthorizationException
     */
    public function getAuthorizeHeader(OAuth1DtoInterface $dto, string $method, string $url): string;

}
