<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Application\Model\Form;

use Hanaboso\PipesPhpSdk\Application\Exception\ApplicationInstallException;

/**
 * Class Field
 *
 * @package Hanaboso\PipesPhpSdk\Application\Model\Form
 */
final class Field
{

    public const TEXT       = 'text';
    public const NUMBER     = 'number';
    public const URL        = 'url';
    public const PASSWORD   = 'password';
    public const SELECT_BOX = 'selectbox';
    public const CHECKBOX   = 'checkbox';

    /**
     * @var string
     */
    private string $description = '';

    /**
     * @var bool
     */
    private bool $readOnly = FALSE;

    /**
     * @var bool
     */
    private bool $disabled = FALSE;

    /**
     * @var mixed[]
     */
    private array $choices = [];

    /**
     * Field constructor.
     *
     * @param string $type
     * @param string $key
     * @param string $label
     * @param mixed  $value
     * @param bool   $required
     *
     * @throws ApplicationInstallException
     */
    public function __construct(
        private string $type,
        private string $key,
        private string $label,
        private $value = NULL,
        private bool $required = FALSE,
    )
    {
        if (!in_array($type, $this->getTypes(), TRUE)) {
            throw new ApplicationInstallException(
                sprintf('Invalid field type "%s"', $type),
                ApplicationInstallException::INVALID_FIELD_TYPE,
            );
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param mixed $value
     *
     * @return Field
     */
    public function setValue(mixed $value): Field
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param string $label
     *
     * @return Field
     */
    public function setLabel(string $label): Field
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return Field
     */
    public function setDescription(string $description): Field
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param bool $required
     *
     * @return Field
     */
    public function setRequired(bool $required): Field
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @param bool $readOnly
     *
     * @return Field
     */
    public function setReadOnly(bool $readOnly): Field
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    /**
     * @param bool $disabled
     *
     * @return Field
     */
    public function setDisabled(bool $disabled): Field
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * @param mixed[] $choices
     *
     * @return Field
     */
    public function setChoices(array $choices): Field
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        return $this->readOnly;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @return mixed[]
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'type'        => $this->getType(),
            'key'         => $this->getKey(),
            'value'       => $this->getValue(),
            'label'       => $this->getLabel(),
            'description' => $this->getDescription(),
            'required'    => $this->isRequired(),
            'readOnly'    => $this->isReadOnly(),
            'disabled'    => $this->isDisabled(),
            'choices'     => $this->getChoices(),
        ];
    }

    /**
     * @return string[]
     */
    private function getTypes(): array
    {
        return [
            self::TEXT,
            self::URL,
            self::NUMBER,
            self::PASSWORD,
            self::CHECKBOX,
            self::SELECT_BOX,
        ];
    }

}
