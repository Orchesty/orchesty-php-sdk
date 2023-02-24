<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Storage\Mongodb;

/**
 * Class DocumentAbstract
 *
 * @package Hanaboso\PipesPhpSdk\Storage\Mongodb
 */
abstract class DocumentAbstract
{

    /**
     * @var string|null
     */
    private ?string $id = NULL;

    /**
     * @return mixed[]
     */
    abstract public function toArray(): array;

    /**
     * @param mixed[] $data
     *
     * @return DocumentAbstract
     */
    abstract protected function fromArray(array $data): DocumentAbstract;

    /**
     * DocumentAbstract constructor.
     *
     * @param mixed[]|null $data
     */
    public function __construct(?array $data = [])
    {
        if ($data) {
            $this->fromArray($data);
        }
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     *
     * @return $this
     */
    public function setId(?string $id): DocumentAbstract
    {
        $this->id = $id;

        return $this;
    }

}
