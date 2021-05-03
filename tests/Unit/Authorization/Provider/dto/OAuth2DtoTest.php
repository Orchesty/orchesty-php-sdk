<?php declare(strict_types=1);

namespace PipesPhpSdkTests\Unit\Authorization\Provider\dto;

use Exception;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Authorization\Base\Basic\BasicApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Base\OAuth2\OAuth2ApplicationInterface;
use Hanaboso\PipesPhpSdk\Authorization\Provider\Dto\OAuth2Dto;
use PipesPhpSdkTests\KernelTestCaseAbstract;

/**
 * Class OAuth2DtoTest
 *
 * @package PipesPhpSdkTests\Unit\Authorization\Provider\dto
 */
final class OAuth2DtoTest extends KernelTestCaseAbstract
{

    /**
     * @throws Exception
     */
    public function testOauth2Dto(): void
    {
        $applicationInstall = (new ApplicationInstall())->setSettings(
            [
                BasicApplicationInterface::AUTHORIZATION_SETTINGS =>
                    [
                        OAuth2ApplicationInterface::CLIENT_ID     => '159',
                        OAuth2ApplicationInterface::CLIENT_SECRET => 'secret',
                    ],
            ],
        );
        $dto                = new OAuth2Dto($applicationInstall, 'auth/url', 'token/url');
        $dto->setRedirectUrl('redirect/url');

        self::assertEquals('159', $dto->getClientId());
        self::assertEquals('secret', $dto->getClientSecret());
        self::assertEquals('auth/url', $dto->getAuthorizeUrl());
        self::assertEquals('token/url', $dto->getTokenUrl());
        self::assertEquals('redirect/url', $dto->getRedirectUrl());
        self::assertTrue($dto->isRedirectUrl());
    }

}
