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
        $datas = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
            ],
            [
                'name' => 'Kasir',
                'slug' => 'cashier'
            ],
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
            [
                'name' => 'Penjualan',
                'slug' => 'sales'
            ],
            [
                'name' => 'Pembelian Barang',
                'slug' => 'purchase'
            ],
            [
                'name' => 'Pembelian Peralatan',
                'slug' => 'expense'
            ],
            [
                'name' => 'Promo',
                'slug' => 'discount'
            ],
            [
                'name' => 'Stok',
                'slug' => 'stock'
            ],
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
            [
                'name' => 'Pengaturan',
                'slug' => 'settings'
            ],
        ];

        foreach ($datas as $data) {
            $permission = new Permission;
            $permission->name = $data['name'];
            $permission->slug = $data['slug'];
            $permission->save();
        }
    }
}
