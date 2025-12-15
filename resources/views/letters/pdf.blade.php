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
            font-size: 10px;
            color: #666;
            margin: 10px 0;
        }
        table {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table td:first-child {
            font-weight: bold;
            width: 150px;
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
            <p style="margin-bottom: 60px;">_____________________</p>
            <p>{{ $letter->approver?->name ?? 'HR Manager' }}</p>
            <p style="font-size: 9px;">Disetujui oleh HR</p>
        </div>
    </div>

    <!-- Metadata -->
    <div class="footer">
        <div class="metadata">
            <strong>Informasi Surat:</strong><br>
            Tipe : {{ ucfirst($letter->letter_type) }}<br>
            Status : {{ ucfirst($letter->status) }}<br>
            Dibuat oleh : {{ $letter->user->name }}<br>
            Waktu Cetak : {{ now()->format('d F Y H:i:s') }}
        </div>
    </div>
</body>
</html>
