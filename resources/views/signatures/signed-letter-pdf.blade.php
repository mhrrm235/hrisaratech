<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        .letter-number {
            text-align: right;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .letter-date {
            margin-bottom: 30px;
        }
        .subject {
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }
        .content {
            text-align: justify;
            margin-bottom: 30px;
            min-height: 200px;
        }
        .signature-section {
            margin-top: 50px;
            text-align: center;
        }
        .signature-box {
            display: inline-block;
            width: 200px;
            text-align: center;
            margin-top: 40px;
            margin-left: 20px;
        }
        .signature-image {
            max-width: 150px;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 40px;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            color: #999;
        }
        .metadata {
            font-size: 9px;
            color: #333;
            margin: 10px 0;
            line-height: 1.8;
        }
        .metadata strong {
            color: #000;
            font-weight: bold;
            display: block;
            margin-top: 8px;
            margin-bottom: 4px;
            font-size: 10px;
        }
        .metadata br + strong {
            margin-top: 12px;
        }
        .verification-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            margin-top: 5px;
        }
        .hash-value {
            font-family: 'Courier New', monospace;
            font-size: 8px;
            word-break: break-all;
            background: #f5f5f5;
            padding: 4px 6px;
            margin: 2px 0;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <!-- Header dengan logo/kop perusahaan -->
    <div class="header">
        <h1>PT. ARATECH INDONESIA</h1>
        <p>Jl. Industri No. 123, Jakarta 12345</p>
        <p>Telepon: (021) 123-4567 | Email: info@aratech.co.id</p>
    </div>

    <!-- Nomor Surat -->
    <div class="letter-number">
        Nomor : {{ $letter->letter_number }}
    </div>

    <!-- Tanggal -->
    <div class="letter-date">
        Tanggal : {{ $letter->approved_date ? $letter->approved_date->format('d F Y') : $letter->created_date->format('d F Y') }}
    </div>

    <!-- Subjek/Perihal -->
    <div class="subject">
        Perihal : {{ $letter->subject }}
    </div>

    <!-- Isi Surat -->
    <div class="content">
        {!! $letter->content !!}
    </div>

    <!-- Bagian Tanda Tangan -->
    <div class="signature-section">
        <p style="margin-bottom: 60px;">Jakarta, {{ $letter->approved_date ? $letter->approved_date->format('d F Y') : $letter->created_date->format('d F Y') }}</p>
        
        <div class="signature-box">
            <p style="margin-bottom: 10px;"><strong>Ditandatangani Secara Digital Oleh:</strong></p>
            <p style="margin: 5px 0; font-size: 11px;"><strong>{{ $signature->signer->name }}</strong></p>
            <p style="margin: 0; font-size: 10px;">{{ $signature->signer->employee->fullname ?? $signature->signer->name }}</p>
            <p style="margin: 5px 0; font-size: 9px;">{{ $signature->signed_date->format('d M Y H:i:s') }}</p>
            <div style="border-top: 2px solid #000; width: 150px; margin: 20px auto 5px auto; padding-top: 5px;">
                <p style="margin: 0; font-size: 9px; font-weight: bold;">________________________</p>
            </div>
            <div style="display: inline-block; background: #28a745; color: white; padding: 4px 10px; border-radius: 3px; font-size: 10px; margin-top: 10px; font-weight: bold;">
                ✓ TERTANDATANGANI DIGITAL
            </div>
        </div>
    </div>

    <!-- Metadata -->
    <div class="footer">
        <div class="metadata">
            <strong>Informasi Dokumen Tertandatangani:</strong><br>
            Nomor Surat : {{ $letter->letter_number }}<br>
            Tipe : {{ ucfirst($letter->letter_type) }}<br>
            Status : {{ ucfirst($letter->status) }}<br>
            Dibuat oleh : {{ $letter->user->name }}<br>
            Disetujui oleh : {{ $letter->approver?->name ?? '-' }}<br>
            Ditandatangani oleh : {{ $signature->signer->name }}<br>
            Waktu Tandatangan : {{ $signature->signed_date->format('d F Y H:i:s') }}<br>
            <br>
            <strong>Metadata Kriptografi:</strong>
            Hash Tanda Tangan (SHA-256) :<br>
            <span class="hash-value">{{ $signature->signature_hash }}</span>
            Token Verifikasi :<br>
            <span class="hash-value">{{ $signature->verification_token }}</span>
            <br>
            <strong>Informasi Teknis:</strong>
            IP Address Penandatangan : {{ $signature->ip_address }}<br>
            User Agent : {{ $signature->user_agent }}<br>
            Status Verifikasi : {{ ucfirst($signature->verifications->first()?->status ?? 'pending') }}<br>
            @if($signature->verifications->first())
                Diverifikasi oleh : {{ $signature->verifications->first()->verifier->name }}<br>
                Waktu Verifikasi : {{ $signature->verifications->first()->verification_date->format('d F Y H:i:s') }}<br>
                Catatan Verifikasi : {{ $signature->verifications->first()->remarks }}<br>
            @endif
            <em>Dokumen ini telah ditandatangani secara digital dan memiliki validitas hukum yang sah sesuai dengan Undang-Undang Informasi dan Transaksi Elektronik (ITE).</em>
        </div>
    </div>
</body>
</html>
