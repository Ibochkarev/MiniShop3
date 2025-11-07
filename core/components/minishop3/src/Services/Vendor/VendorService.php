<?php

namespace MiniShop3\Services\Vendor;

use MiniShop3\Model\msProductData;
use MiniShop3\Model\msVendor;
use MODX\Revolution\modX;

/**
 * Сервис для работы с производителями
 *
 * Обрабатывает бизнес-логику связанную с производителями товаров,
 * включая удаление и управление связями с товарами
 */
class VendorService
{
    protected modX $modx;

    public function __construct(modX $modx)
    {
        $this->modx = $modx;
    }

    /**
     * Получить статистику по производителю
     *
     * Возвращает количество товаров, привязанных к производителю
     *
     * @param msVendor $vendor
     * @return array ['total_products' => int]
     */
    public function getVendorStatistics(msVendor $vendor): array
    {
        $vendorId = $vendor->get('id');

        $totalProducts = $this->modx->getCount(msProductData::class, [
            'vendor_id' => $vendorId
        ]);

        return [
            'total_products' => $totalProducts,
        ];
    }

    /**
     * Проверка возможности удаления производителя
     *
     * Проверяет, можно ли безопасно удалить производителя
     * Можно использовать для предупреждения пользователя
     *
     * @param msVendor $vendor
     * @return array ['can_remove' => bool, 'products_count' => int, 'warnings' => array]
     */
    public function canRemoveVendor(msVendor $vendor): array
    {
        $stats = $this->getVendorStatistics($vendor);
        $warnings = [];

        if ($stats['total_products'] > 0) {
            $warnings[] = sprintf(
                'У производителя "%s" есть %d товаров. При удалении производителя у них будет обнулен vendor_id.',
                $vendor->get('name'),
                $stats['total_products']
            );
        }

        return [
            'can_remove' => true, // Всегда можно удалить, но с предупреждениями
            'products_count' => $stats['total_products'],
            'warnings' => $warnings,
        ];
    }

    public function getOrCreateVendor(int $id, string $newName): msVendor
    {
        // Сначала ищем по ID (если передан валидный ID)
        if ($id > 0) {
            $vendor = $this->modx->getObject(msVendor::class, $id);
            if ($vendor) {
                return $vendor;
            }
        }

        // Ищем по имени
        if (!empty($name)) {
            $vendor = $this->modx->getObject(msVendor::class, ['name' => $name]);
            if ($vendor) {
                return $vendor;
            }
        }

        $vendor = $this->modx->newObject(msVendor::class);
        $vendor->set('name', $newName);
        $vendor->save();

        return $vendor;
    }
}
