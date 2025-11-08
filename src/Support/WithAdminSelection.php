<?php

namespace Dcat\Admin\Support;

use Illuminate\Console\Command;

/**
 * Console命令后台选择支持 Trait
 */
trait WithAdminSelection
{
    /**
     * 获取可用的后台类型选项
     *
     * @return array
     */
    protected function getAvailableAdminOptions(): array
    {
        $adminOptions = [
            'admin' => '主后台',
        ];

        // 从 multi_app 配置中读取其他启用的后台
        $multiApps = config('admin.multi_app', []);
        foreach ($multiApps as $app => $enabled) {
            if ($enabled) {
                $adminOptions[$app] = ucwords(str_replace('-', ' ', $app)) . '后台';
            }
        }

        return $adminOptions;
    }

    /**
     * 交互式选择后台类型
     *
     * @param string $default
     * @return string
     */
    protected function selectAdminType(string $default = 'admin'): string
    {
        $adminOptions = $this->getAvailableAdminOptions();

        if (count($adminOptions) === 1) {
            // 只有一个选项时直接返回
            return array_key_first($adminOptions);
        }

        return $this->choice('请选择后台类型:', $adminOptions, $default);
    }

    /**
     * 设置配置前缀并显示信息
     *
     * @param string $adminType
     * @return void
     */
    protected function setupAdminConfig(string $adminType): void
    {
        AdminConfig::setPrefix($adminType);

        $adminOptions = $this->getAvailableAdminOptions();
        $this->info("正在为 '{$adminOptions[$adminType]}' 操作...");
    }

    /**
     * 完整的后台选择和设置流程
     *
     * @param string $default
     * @return string
     */
    protected function selectAndSetupAdmin(string $default = 'admin'): string
    {
        $adminType = $this->selectAdminType($default);
        $this->setupAdminConfig($adminType);

        return $adminType;
    }
}