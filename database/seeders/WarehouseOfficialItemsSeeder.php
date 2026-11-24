<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WarehouseItem;
use App\Models\WarehouseSupplier;

class WarehouseOfficialItemsSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'item_code'           => 'WH-2026-0001',
                'name'                => 'MX240',
                'brand'               => 'Juniper Networks',
                'item_type'           => 'Router',
                'component_type'      => 'Perangkat Utuh',
                'serial_number'       => 'JN11CD60CAFC',
                'product_number'      => 'MX240BASE-AC-HIGH',
                'po_number'           => '079/2010',
                'category'            => 'Backup / Spare',
                'customer_sales_name' => 'Raiza Maindoka',
                'poc_sales_name'      => 'Raiza Maindoka',
                'location_type'       => 'Gudang IPNET',
                'warehouse_rack'      => 'Rak Utama A-01',
                'status'              => 'Tersedia',
                'device_status'       => 'Perlu dilakukan Pengecekan',
                'image'               => 'warehouse/devices/mx240_router_chassis.jpg',
                'notes'               => 'Universal Edge Router 3D chassis Juniper MX240 AC High Power.',
            ],
            [
                'item_code'           => 'WH-2026-0002',
                'name'                => 'EX8200',
                'brand'               => 'Juniper Networks',
                'item_type'           => 'Switch',
                'component_type'      => 'Modul / Card',
                'serial_number'       => 'BD1711222780',
                'product_number'      => 'EX8200-48T',
                'po_number'           => '059/2011',
                'category'            => 'Backup / Spare',
                'customer_sales_name' => 'Raiza Maindoka',
                'poc_sales_name'      => 'Raiza Maindoka',
                'location_type'       => 'Gudang IPNET',
                'warehouse_rack'      => 'Rak Utama A-01',
                'status'              => 'Tersedia',
                'device_status'       => 'Aktif',
                'image'               => 'warehouse/devices/ex8200_switch_card.jpg',
                'notes'               => 'Line card 48-port 10/100/1000BASE-T RJ-45 untuk chassis Juniper EX8200.',
            ],
            [
                'item_code'           => 'WH-2026-0003',
                'name'                => 'EX8200',
                'brand'               => 'Juniper Networks',
                'item_type'           => 'Switch',
                'component_type'      => 'Modul / Card',
                'serial_number'       => 'BD1711192676',
                'product_number'      => 'EX8200-48T',
                'po_number'           => '059/2011',
                'category'            => 'Backup / Spare',
                'customer_sales_name' => 'Raiza Maindoka',
                'poc_sales_name'      => 'Raiza Maindoka',
                'location_type'       => 'Gudang IPNET',
                'warehouse_rack'      => 'Rak Utama A-01',
                'status'              => 'Tersedia',
                'device_status'       => 'Perlu dilakukan Pengecekan',
                'image'               => 'warehouse/devices/ex8200_switch_card.jpg',
                'notes'               => 'Line card 48-port 10/100/1000BASE-T RJ-45 untuk chassis Juniper EX8200. Perlu dilakukan pengecekan port.',
            ],
            [
                'item_code'           => 'WH-2026-0004',
                'name'                => 'EX8200',
                'brand'               => 'Juniper Networks',
                'item_type'           => 'Switch',
                'component_type'      => 'Modul / Card',
                'serial_number'       => 'BD1710381623',
                'product_number'      => 'EX8200-48T',
                'po_number'           => '059/2011',
                'category'            => 'Backup / Spare',
                'customer_sales_name' => 'Nabylla Berlianita',
                'poc_sales_name'      => 'Nabylla Berlianita',
                'location_type'       => 'Gudang IPNET',
                'warehouse_rack'      => 'Rak Utama A-01',
                'status'              => 'Tersedia',
                'device_status'       => 'Aktif',
                'image'               => 'warehouse/devices/ex8200_switch_card.jpg',
                'notes'               => 'Line card 48-port 10/100/1000BASE-T RJ-45 untuk chassis Juniper EX8200.',
            ],
            [
                'item_code'           => 'WH-2026-0005',
                'name'                => 'EX8200',
                'brand'               => 'Juniper Networks',
                'item_type'           => 'Switch',
                'component_type'      => 'Modul / Card',
                'serial_number'       => 'BB1710452964',
                'product_number'      => 'EX8200-8XS',
                'po_number'           => '059/2011',
                'category'            => 'Stock',
                'customer_sales_name' => 'Raiza Maindoka',
                'poc_sales_name'      => 'Raiza Maindoka',
                'location_type'       => 'Gudang IPNET',
                'warehouse_rack'      => 'Rak Utama A-01',
                'status'              => 'Tersedia',
                'device_status'       => 'Aktif',
                'image'               => 'warehouse/devices/ex8200_switch_card.jpg',
                'notes'               => 'Line card 8-port 10GbE SFP+ untuk chassis Juniper EX8200.',
            ],
        ];

        foreach ($items as $itemData) {
            $existing = WarehouseItem::where('item_code', $itemData['item_code'])
                ->orWhere('serial_number', $itemData['serial_number'])
                ->first();

            if (!$existing) {
                WarehouseItem::create($itemData);
            } else {
                $updates = [];
                if (empty($existing->customer_sales_name) || in_array($existing->customer_sales_name, ['-', 'Others', 'Lainnya'])) {
                    $updates['customer_sales_name'] = $itemData['customer_sales_name'];
                }
                if (empty($existing->poc_sales_name) || in_array($existing->poc_sales_name, ['-', 'Others', 'Lainnya'])) {
                    $updates['poc_sales_name'] = $itemData['poc_sales_name'];
                }
                if (empty($existing->image)) {
                    $updates['image'] = $itemData['image'];
                }
                if (empty($existing->device_status)) {
                    $updates['device_status'] = $itemData['device_status'];
                }
                if (!empty($updates)) {
                    $existing->update($updates);
                }
            }
        }

        if (WarehouseSupplier::where('name', 'PT. SISTECH KHARISMA')->count() === 0) {
            WarehouseSupplier::create([
                'name'           => 'PT. SISTECH KHARISMA',
                'code'           => 'SUP-SISTECH-01',
                'contact_person' => 'Alfin & Cindy',
                'phone'          => '+622135056683 / 3807668',
                'email'          => 'sales@sistech.co.id',
                'address'        => 'Jl. Ir. H Juanda IV No. 3B-C Jakarta-Pusat 10120',
                'status'         => 'Aktif',
                'brands'         => json_encode(['Juniper', 'Cisco', 'Palo Alto']),
                'contacts'       => json_encode([
                    ['name' => 'Alfin & Cindy', 'phone' => '+622135056683 / 3807668', 'email' => 'sales@sistech.co.id']
                ]),
            ]);
        }
    }
}
