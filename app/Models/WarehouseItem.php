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
     * Auto generate unique item code: WH-YYYY-XXXX
     */
    public static function generateItemCode(): string
    {
        $countThisYear = static::withTrashed()->whereYear('created_at', date('Y'))->count() + 1;
        return 'WH-' . date('Y') . '-' . str_pad($countThisYear, 4, '0', STR_PAD_LEFT);
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

    /**
     * Helper list model perangkat hardware & network resmi
     */
    public static function getDeviceModels(): array
    {
        return [
            'ACX2200',
            'ACX4000',
            'ACX5000',
            'ACX7000',
            'BD8800',
            'BDX8',
            'CTP2056',
            'EX2200',
            'EX2300',
            'EX3200',
            'EX3300',
            'EX3400',
            'EX4200',
            'EX4300',
            'EX4500',
            'EX4550',
            'EX4600',
            'EX8200',
            'EX9200',
            'ISG1000',
            'ISG2000',
            'MX80',
            'MX104',
            'MX204',
            'MX240',
            'MX301',
            'MX304',
            'MX480',
            'MX960',
            'MX10K',
            'M10i',
            'M320',
            'NS500',
            'NUTANIX',
            'Newtec',
            'PA3020',
            'PA5010',
            'PA5020',
            'PA5050',
            'QFX3500',
            'QFX5100',
            'QFX5200',
            'SSG20',
            'SSG140',
            'SRX100',
            'SRX210',
            'SRX240',
            'SRX300',
            'SRX550',
            'SRX650',
            'SRX1400',
            'SRX1500',
            'SRX3400',
            'SRX3500',
            'SRX5600',
            'SRX5800',
            'T1600',
            'T4000',
            'PTX5000',
            'X430',
            'X440',
            'X450',
            'X460',
            'X480',
            'X650',
            'X670',
            'sfp-xfp-qsfp-cfp',
            'Procera',
            'PulseSecure',
            'Others',
        ];
    }

    /**
     * Helper list merk / brand resmi sesuai katalog perangkat
     */
    public static function getDeviceBrands(): array
    {
        return [
            'Juniper Networks',
            'Extreme Networks',
            'Palo Alto Networks',
            'Nutanix',
            'Newtec',
            'Procera (Sandvine)',
            'Pulse Secure',
            'Cisco',
            'Fortinet',
            'MikroTik',
            'Ruijie Networks',
            'Multi-Vendor / Generic',
            'Others',
        ];
    }
}
