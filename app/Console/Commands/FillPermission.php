<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Permission;

class FillPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill:permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the permissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        Permission::truncate();

        $datas = [
            [
                'name' => 'Dashboard',
                'icon' => 'mdi mdi-view-dashboard mr-2',
                'slug' => 'dashboard',
            ],
            [
                'name' => 'Kasir',
                'icon' => 'mdi mdi-desktop-classic mr-2',
                'slug' => 'cashier'
            ],
            [
                'name' => 'Barang',
                'slug' => 'product',
                'icon' => 'mdi mdi-calendar-text mr-2',
                'children' => [
                    [
                        'name' => 'Data Barang',
                        'slug' => 'product'
                    ],
                    [
                        'name' => 'Kategori Barang',
                        'slug' => 'category'
                    ],
                    [
                        'name' => 'Satuan Barang',
                        'slug' => 'unit'
                    ],
                ]
            ],
            [
                'name' => 'Penjualan',
                'slug' => 'sales',
                'icon' => 'mdi mdi-basket mr-2'
            ],
            [
                'name' => 'Pembelian',
                'slug' => 'purchase',
                'icon' => 'mdi mdi-store mr-2',
                'children' => [
                    [
                        'name' => 'Pembelian Barang',
                        'slug' => 'purchase'
                    ],
                    [
                        'name' => 'Pembelian Peralatan',
                        'slug' => 'expense'
                    ]
                ]
            ],
            [
                'name' => 'Promo',
                'slug' => 'discount',
                'icon' => 'mdi mdi-ticket-confirmation mr-2'
            ],
            [
                'name' => 'Stok',
                'slug' => 'stock',
                'icon' => 'mdi mdi-library-shelves mr-2'
            ],
            [
                'name' => 'Pengguna',
                'slug' => 'user',
                'icon' => 'mdi mdi-account mr-2',
                'children' => [
                    [
                        'name' => 'Pengguna',
                        'slug' => 'user'
                    ],
                    [
                        'name' => 'Pelanggan',
                        'slug' => 'customer'
                    ],
                    [
                        'name' => 'Pemasok',
                        'slug' => 'supplier'
                    ],
                    [
                        'name' => 'Peranan',
                        'slug' => 'role'
                    ]
                ]
            ],
            [
                'name' => 'Laporan',
                'slug' => 'report-sales',
                'icon' => 'mdi mdi-file mr-2',
                'children' => [
                    [
                        'name' => 'Laporan Penjualan',
                        'slug' => 'report-sales'
                    ],
                    [
                        'name' => 'Laporan Pembelian Barang',
                        'slug' => 'report-purchase'
                    ],
                    [
                        'name' => 'Laporan Pembelian Peralatan',
                        'slug' => 'report-expense'
                    ],
                    [
                        'name' => 'Laporan Stok',
                        'slug' => 'report-stock'
                    ],
                ]
            ],
            [
                'name' => 'Pengaturan',
                'slug' => 'settings',
                'icon' => 'mdi mdi-settings mr-2'
            ],
        ];

        foreach ($datas as $data) {
            $permission = new Permission;
            $permission->name = $data['name'];
            $permission->slug = $data['slug'];
            $permission->icon = $data['icon'];
            $permission->save();
            if (!empty($data['children'])) {
                $this->saveChildren($data['children'], $permission->id);
            }
        }
    }

    protected function saveChildren($datas, $parent_id)
    {
        foreach ($datas as $data) {
            $permission = new Permission;
            $permission->name = $data['name'];
            $permission->slug = $data['slug'];
            $permission->parent_id = $parent_id;
            $permission->save();
            if (!empty($data['children'])) {
                $this->saveChildren($data['children'], $parent_id);
            }
        }
    }
}
