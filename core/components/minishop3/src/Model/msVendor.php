<?php

namespace MiniShop3\Model;

use MiniShop3\ModelLifecycle\VendorLifecycleHandler;
use xPDO\Om\xPDOSimpleObject;

/**
 * Class msVendor
 *
 * @property integer $position
 * @property string $name
 * @property integer $resource_id
 * @property string $country
 * @property string $logo
 * @property string $address
 * @property string $phone
 * @property string $email
 * @property string $description
 * @property array $properties
 *
 * @package MiniShop3\Model
 */
class msVendor extends xPDOSimpleObject
{
    protected ?VendorLifecycleHandler $lifecycleHandler;

    /**
     * @param array $ancestors
     *
     * @return bool
     */
    public function remove(array $ancestors = []): bool
    {
        $this->getLifecycleHandler()->beforeRemove($this, $ancestors);

        return parent::remove($ancestors);
    }

    protected function getLifecycleHandler(): VendorLifecycleHandler
    {
        if ($this->lifecycleHandler === null) {
            if ($this->xpdo->services->has('ms3_vendor_lifecycle_handler')) {
                $this->lifecycleHandler = $this->xpdo->services->get('ms3_vendor_lifecycle_handler');
            } else {
                $this->lifecycleHandler = new VendorLifecycleHandler($this->xpdo);
            }
        }

        return $this->lifecycleHandler;
    }
}
