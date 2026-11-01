@props([
    'mode'        => 'signature_qr', // 'signature_qr', 'signature_only', 'qr_only'
    'signature'   => null,
    'token'       => null,
    'name'        => 'Digitally Verified',
    'role'        => 'PIC Resmi',
    'date'        => null,
    'qrSvg'       => null,
    'qrDataUri'   => null,
    'isPdf'       => false,
    'class'       => '',
])

{!! \App\Helpers\SignatureHelper::render([
    'mode'        => $mode,
    'signature'   => $signature,
    'token'       => $token,
    'name'        => $name,
    'role'        => $role,
    'date'        => $date,
    'qr_svg'      => $qrSvg,
    'qr_data_uri' => $qrDataUri,
    'is_pdf'      => $isPdf,
    'custom_class'=> $class,
]) !!}
