<?php declare(strict_types=1);

namespace Hanaboso\PipesPhpSdk\Application\Repository;

use Hanaboso\PipesPhpSdk\Storage\Mongodb\Filter;

/**
 * Class ApplicationInstallFilter
 *
 * @package Hanaboso\PipesPhpSdk\Application\Repository
 */
final class ApplicationInstallFilter extends Filter
{

    /**
     * ApplicationInstallFilter constructor.
     *
     * @param bool|null    $enabled
     * @param mixed[]|NULL $names
     * @param mixed[]|NULL $users
     * @param int|NULL     $expires
     * @param mixed[]|NULL $nonEncrypted
     * @param mixed[]|NULL $sdks
     * @param mixed[]|null $ids
     * @param bool|null    $deleted
     */
    public function __construct(
        public ?bool $enabled = NULL,
        public ?array $names = NULL,
        public ?array $users = NULL,
        public ?int $expires = NULL,
        public ?array $nonEncrypted = NULL,
        public ?array $sdks = NULL,
        ?array $ids = NULL,
        ?bool $deleted = NULL,
    )
    {
        parent::__construct($ids, $deleted);
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        $retArray = parent::toArray();

        $retArray['enabled']                                = $this->enabled;
        if ($this->names) $retArray['names']                = $this->names;
        if ($this->users) $retArray['users']                = $this->users;
        if ($this->expires) $retArray['expires']            = $this->expires;
        if ($this->nonEncrypted) $retArray['non_encrypted'] = $this->nonEncrypted;
        if ($this->sdks) $retArray['sdks']                  = $this->sdks;

        return $retArray;
    }

}
