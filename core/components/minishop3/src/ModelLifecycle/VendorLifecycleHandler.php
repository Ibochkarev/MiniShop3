<?php

namespace MiniShop3\ModelLifecycle;

use MiniShop3\Model\msProductData;
use MiniShop3\Model\msVendor;
use xPDO\xPDO;

class VendorLifecycleHandler
{
    protected xPDO $xpdo;

    public function __construct(xPDO $xpdo)
    {
        $this->xpdo = $xpdo;
    }

    /**
     * Обнуление связей в товарах перед удалением производителя
     *
     * @param msVendor $vendor
     * @param array $ancestors
     * @return bool
     */
    public function beforeRemove(msVendor $vendor, array $ancestors = []): bool
    {
        $this->resetVendorForProducts($vendor->get('id'));
        return true;
    }

    protected function resetVendorForProducts(int $vendorId): void
    {
        // Обнуляем vendor_id у всех товаров этого производителя
        $query = $this->xpdo->newQuery(msProductData::class);
        $query->command('UPDATE');
        $query->set(['vendor_id' => 0]);
        $query->where(['vendor_id' => $vendorId]);

        if ($query->prepare() && $query->stmt->execute()) {
            // Логируем количество обновленных товаров
            $affectedRows = $query->stmt->rowCount();
            if ($affectedRows > 0) {
                $this->xpdo->log(
                    xPDO::LOG_LEVEL_INFO,
                    sprintf(
                        'VendorService: Обнулен vendor_id у %d товаров при удалении производителя ID=%d',
                        $affectedRows,
                        $vendorId
                    )
                );
            }
        }
    }
}
