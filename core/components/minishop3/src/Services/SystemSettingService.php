<?php

namespace MiniShop3\Services;

use MODX\Revolution\modSystemSetting;

class SystemSettingService
{
    public function __construct(
        private readonly \modX $modx
    )
    {
    }

    /**
     * General method to get JSON settings
     */
    public function getSettingValue(string $key): array
    {
        $setting = $this->modx->getObject(modSystemSetting::class, ['key' => $key]);
        if (!$setting) {
            $setting = $this->modx->newObject(modSystemSetting::class);
            $setting->set('key', $key);
            $setting->set('value', json_encode([]));
            $setting->save();
        }

        $value = json_decode($setting->get('value'), true);
        if (!is_array($value)) {
            $value = [];
            $setting->set('value', json_encode($value));
            $setting->save();
        }

        return $value;
    }

    /**
     * General method to update JSON settings
     */
    public function setSettingValue(string $key, array $value): void
    {
        $setting = $this->modx->getObject(modSystemSetting::class, ['key' => $key]);

        if (!$setting) {
            $setting = $this->modx->newObject(modSystemSetting::class);
            $setting->set('key', $key);
        }

        $setting->set('value', json_encode($value));
        $setting->save();
    }
}