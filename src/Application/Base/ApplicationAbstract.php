<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Application\Base;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Hanaboso\CommonsBundle\Enum\ApplicationTypeEnum;
use Hanaboso\PipesPhpSdk\Application\Document\ApplicationInstall;
use Hanaboso\PipesPhpSdk\Application\Model\Form\Field;
use Hanaboso\Utils\File\File;

/**
 * Class ApplicationAbstract
 *
 * @package Hanaboso\PipesPhpSdk\Application\Base
 */
abstract class ApplicationAbstract implements ApplicationInterface
{

    public const FORM = 'form';

    /**
     * @var string
     */
    protected $logoFilename = 'logo.svg';

    /**
     * @return string|null
     */
    public function getLogo(): ?string
    {
        try {
            if (file_exists($this->logoFilename)) {
                return sprintf(
                    'data:%s;base64, %s',
                    mime_content_type($this->logoFilename),
                    base64_encode(File::getContent($this->logoFilename)),
                );
            }
        } catch (Exception $e) {$e;}

        return NULL;
    }

    /**
     * @return string
     */
    public function getApplicationType(): string
    {
        return ApplicationTypeEnum::CRON;
    }

    /**
     * @param ApplicationInstall $applicationInstall
     *
     * @return mixed[]
     */
    public function getApplicationForm(ApplicationInstall $applicationInstall): array
    {
        $settings = $applicationInstall->getSettings()[self::FORM] ?? [];
        $form     = $this->getSettingsForm();
        foreach ($form->getFields() as $field) {
            if (array_key_exists($field->getKey(), $settings)) {
                if ($field->getType() === Field::PASSWORD) {
                    $field->setValue(TRUE);

                    continue;
                }

                $field->setValue($settings[$field->getKey()]);
            }
        }

        return $form->toArray();
    }

    /**
     * @param ApplicationInstall $applicationInstall
     * @param mixed[]            $settings
     *
     * @return ApplicationInstall
     */
    public function setApplicationSettings(ApplicationInstall $applicationInstall, array $settings): ApplicationInstall
    {
        $preparedSetting = [];
        foreach ($this->getSettingsForm()->getFields() as $field) {
            if (array_key_exists($field->getKey(), $settings)) {
                $preparedSetting[$field->getKey()] = $settings[$field->getKey()];
            }
        }

        return $applicationInstall->addSettings([self::FORM => $preparedSetting]);
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
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'name'               => $this->getPublicName(),
            'authorization_type' => $this->getAuthorizationType(),
            'application_type'   => $this->getApplicationType(),
            'key'                => $this->getName(),
            'description'        => $this->getDescription(),
        ];
    }

}
