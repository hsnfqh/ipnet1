<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'warehouse_items';

    protected $fillable = [
        'item_code',
        'name',
        'brand',
        'item_type',
        'component_type',
        'cable_type',
        'serial_number',
        'product_number',
        'po_number',
        'contract_number',
        'category',
        'loan_source_type',
        'disti_name',
        'disti_address',
        'disti_pic',
        'disti_phone',
        'disti_duration',
        'ipnet_pic_name',
        'ipnet_work_unit',
        'ipnet_phone',
        'ipnet_duration',
        'location_type',
        'warehouse_rack',
        'customer_address',
        'customer_pic',
        'customer_phone',
        'customer_sales_name',
        'status',
        'poc_customer_name',
        'poc_pic_name',
        'poc_pic_phone',
        'poc_sales_name',
        'poc_duration',
        'notes',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Helper list jenis barang
     */
    public static function getItemTypes(): array
    {
        return [
            'Router',
            'Switch',
            'Firewall',
            'AP (Access Point)',
            'Bandwidth Management',
            'Kabel',
            'Lainnya / Other',
        ];
    }

    /**
     * Helper list komponen / peripheral
     */
    public static function getComponentTypes(): array
    {
        return [
            'Perangkat Utuh',
            'Filter Udara',
            'SFP',
            'Fan',
            'Modul / Card',
            'Chasis / Rack',
            'PSU',
            'Kabel',
        ];
    }

    /**
     * Helper list kabel
     */
    public static function getCableTypes(): array
    {
        return [
            'Fiber Optic (FO)',
            'UTP / Ethernet',
            'C14 - C13',
            'C13 - CEE 7',
            'CEE',
            'C15 - 2 Pin Japanese',
            'C19 - CEE 7',
            'Patch Cord',
            'Lainnya',
        ];
    }

    /**
     * Helper list unit kerja IPNET
     */
    public static function getWorkUnits(): array
    {
        return [
            'IPNET 1',
            'IPNET 2',
            'IPNET 3',
            'IPNET 4',
            'IPNET 5',
        ];
    }

    /**
     * Helper kategori
     */
    public static function getCategories(): array
    {
        return [
            'Stock',
            'Backup / Spare',
            'Pinjaman',
        ];
    }

    /**
     * Helper status
     */
    public static function getStatuses(): array
    {
        return [
            'Tersedia',
            'POC / Pinjaman',
            'Pinjaman',
            'Pelanggan',
            'Maintenance',
        ];
    }
}
