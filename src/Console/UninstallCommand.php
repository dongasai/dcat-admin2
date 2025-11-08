<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Admin;
use Illuminate\Console\Command;

/**
 * Dcat Admin 卸载命令
 *
 * 注意：此命令专门用于卸载 admin 主应用的所有文件和数据，
 * 不支持多后台配置，会删除所有相关资源文件。
 * 这是一个破坏性操作，请谨慎使用。
 */

class UninstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall the admin package';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->confirm('Are you sure to uninstall dcat-admin?')) {
            return;
        }

        $this->removeFilesAndDirectories();

        $this->line('<info>Uninstalling dcat-admin!</info>');
    }

    /**
     * Remove files and directories.
     *
     * @return void
     */
    protected function removeFilesAndDirectories()
    {
        $this->laravel['files']->deleteDirectory(AdminConfig::directory());
        $this->laravel['files']->deleteDirectory(public_path(Admin::asset()->getRealPath('@extension')));
        $this->laravel['files']->deleteDirectory(public_path(Admin::asset()->getRealPath('@admin')));
        $this->laravel['files']->delete(config_path('admin.php'));
    }
}
