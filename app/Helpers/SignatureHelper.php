<?php

namespace App\Helpers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class SignatureHelper
{
    /**
     * Generate QR Code as inline SVG string
     */
    public static function getQrSvg(string $tokenOrUrl, int $size = 56): string
    {
        $payload = str_starts_with($tokenOrUrl, 'http') ? $tokenOrUrl : route('public.signature.verify', $tokenOrUrl);
        try {
            $svg = (string) QrCode::format('svg')
                ->size($size)
                ->margin(0)
                ->errorCorrection('M')
                ->generate($payload);

            return preg_replace('/<\?xml.*?\?>/i', '', $svg);
        } catch (\Throwable $e) {
            Log::warning('SignatureHelper getQrSvg error: ' . $e->getMessage());
            return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect width="24" height="24" rx="2" stroke-width="1.5"/><path d="M4 4h4v4H4zM16 4h4v4h-4zM4 16h4v4H4zM16 16h4v4h-4z" fill="currentColor"/></svg>';
        }
    }

    /**
     * Generate QR Code as base64 Data URI (safe for PDF <img> tags & canvas)
     */
    public static function getQrDataUri(string $tokenOrUrl, int $size = 60): string
    {
        $payload = str_starts_with($tokenOrUrl, 'http') ? $tokenOrUrl : route('public.signature.verify', $tokenOrUrl);
        try {
            $svg = (string) QrCode::format('svg')
                ->size($size)
                ->margin(0)
                ->errorCorrection('M')
                ->generate($payload);

            $cleanSvg = preg_replace('/<\?xml.*?\?>/i', '', $svg);
            return 'data:image/svg+xml;base64,' . base64_encode($cleanSvg);
        } catch (\Throwable $e) {
            Log::warning('SignatureHelper getQrDataUri error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Render the official IPNet Digital Signature block (styled matching reference image)
     * 
     * @param array $params [
     *    'mode'          => 'signature_qr' | 'signature_only' | 'qr_only',
     *    'signature'     => base64 string or image URL,
     *    'token'         => verification token (e.g. SIG-2026-XXXX),
     *    'name'          => signer full name,
     *    'role'          => signer role/title,
     *    'date'          => signed date string,
     *    'is_pdf'        => bool (table styling for DomPDF vs Tailwind HTML),
     *    'qr_svg'        => optional pre-generated SVG,
     *    'qr_data_uri'   => optional pre-generated Data URI,
     *    'custom_class'  => extra CSS classes
     * ]
     */
    public static function render(array $params = []): string
    {
        $mode        = $params['mode'] ?? 'signature_qr';
        $signature   = $params['signature'] ?? null;
        $token       = $params['token'] ?? ('SIG-' . strtoupper(substr(md5($params['name'] ?? 'ipnet'), 0, 8)));
        $name        = $params['name'] ?? 'Digitally Verified';
        $role        = $params['role'] ?? 'IPNet Verified Signer';
        $date        = $params['date'] ?? date('d/m/Y');
        $isPdf       = !empty($params['is_pdf']);
        $customClass = $params['custom_class'] ?? '';

        $qrDataUri = !empty($params['qr_data_uri']) ? $params['qr_data_uri'] : self::getQrDataUri($token);
        $qrSvg     = !empty($params['qr_svg']) ? $params['qr_svg'] : self::getQrSvg($token);

        // Render untuk DomPDF / PDF Export
        if ($isPdf) {
            $showQr  = in_array($mode, ['signature_qr', 'qr_only']);
            $showBox = in_array($mode, ['signature_qr', 'signature_only']);

            $html = '<table style="display: inline-table; width: auto; margin: 0 auto; border: none; border-collapse: separate; border-spacing: 5px; vertical-align: middle;">';
            $html .= '<tr>';

            if ($showQr) {
                $html .= '<td style="border: 1px solid #64748B; padding: 2px; background: #ffffff; width: 48px; height: 48px; text-align: center; vertical-align: middle;">';
                if ($qrDataUri) {
                    $html .= '<img src="' . $qrDataUri . '" alt="QR" style="width: 44px; height: 44px; display: block; margin: 0 auto;">';
                } else {
                    $html .= '<span style="font-size: 8px; font-family: monospace;">QR CODE</span>';
                }
                $html .= '</td>';
            }

            if ($showBox) {
                $html .= '<td style="border: 1px solid #1E293B; padding: 3px 6px; background: #ffffff; min-width: 110px; max-width: 150px; text-align: center; vertical-align: middle;">';
                $html .= '<div style="font-family: monospace; font-size: 6.5px; color: #475569; text-transform: lowercase; margin-bottom: 2px; border-bottom: 1px solid #E2E8F0; padding-bottom: 1px;">';
                $html .= '<span style="color: #8F0A0D; font-size: 7px;">●</span> digitally signed';
                $html .= '</div>';
                $html .= '<div style="height: 32px; line-height: 32px;">';
                if ($signature) {
                    $html .= '<img src="' . $signature . '" alt="TTD" style="max-height: 30px; max-width: 100px; vertical-align: middle;">';
                } else {
                    $html .= '<span style="font-family: \'Times New Roman\', serif; font-style: italic; font-size: 11px; font-weight: bold; color: #1E293B;">' . htmlspecialchars($name) . '</span>';
                }
                $html .= '</div>';
                $html .= '<div style="font-family: monospace; font-size: 6px; color: #64748B; border-top: 1px dashed #E2E8F0; padding-top: 1px; margin-top: 2px;">';
                $html .= 'dsign.ipnet.id/' . htmlspecialchars($token);
                $html .= '</div>';
                $html .= '</td>';
            }

            $html .= '</tr>';
            $html .= '</table>';
            return $html;
        }

        // Render untuk Web UI (Tailwind CSS)
        $showQr  = in_array($mode, ['signature_qr', 'qr_only']);
        $showBox = in_array($mode, ['signature_qr', 'signature_only']);

        $out = '<div class="inline-flex items-center gap-2.5 p-1 select-none text-left align-middle ' . htmlspecialchars($customClass) . '">';

        // 1. QR Code Box
        if ($showQr) {
            $out .= '<div class="w-14 h-14 shrink-0 p-1 bg-white border border-slate-300 rounded-md flex items-center justify-center shadow-2xs hover:border-[#8F0A0D] transition">';
            if (!empty($qrSvg)) {
                $out .= $qrSvg;
            } elseif (!empty($qrDataUri)) {
                $out .= '<img src="' . $qrDataUri . '" alt="QR" class="w-full h-full object-contain">';
            } else {
                $out .= '<div class="text-[9px] font-mono text-slate-400">QR</div>';
            }
            $out .= '</div>';
        }

        // 2. Framed Digital Signed Box (Reference Image Replica)
        if ($showBox) {
            $out .= '<div class="relative px-2.5 pt-2 pb-1 border border-slate-700 bg-white rounded-md min-w-[130px] max-w-[170px] min-h-[58px] flex flex-col justify-between shadow-2xs group hover:border-[#8F0A0D] transition">';
            
            // Top label badge: "digitally signed"
            $out .= '<div class="absolute -top-2 left-2 px-1 bg-white text-[8px] font-mono font-bold text-slate-700 tracking-wide flex items-center gap-1 border border-slate-300 rounded-xs">';
            $out .= '<span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>';
            $out .= '<span>digitally signed</span>';
            $out .= '</div>';

            // Signature stroke in center
            $out .= '<div class="flex-1 flex items-center justify-center my-0.5 min-h-[30px]">';
            if (!empty($signature)) {
                $out .= '<img src="' . $signature . '" alt="Tanda Tangan" class="max-h-8 max-w-[110px] object-contain filter drop-shadow-2xs">';
            } else {
                $out .= '<div class="font-serif italic text-[11px] text-slate-800 font-bold py-0.5 text-center">' . htmlspecialchars($name) . '</div>';
            }
            $out .= '</div>';

            // Bottom verification token
            $out .= '<div class="text-[7.5px] font-mono text-slate-500 tracking-tight text-center border-t border-dashed border-slate-200 pt-0.5 truncate">';
            $out .= 'dsign.ipnet.id/' . htmlspecialchars($token);
            $out .= '</div>';

            $out .= '</div>';
        }

        $out .= '</div>';
        return $out;
    }
}
