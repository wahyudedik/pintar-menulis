<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Proyek - {{ $project->business_name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .project-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .project-name {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        .project-type {
            color: #6b7280;
            font-size: 14px;
        }
        .role-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin: 10px 0;
        }
        .buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-accept {
            background: #10b981;
            color: white;
        }
        .btn-accept:hover {
            background: #059669;
        }
        .btn-decline {
            background: #ef4444;
            color: white;
        }
        .btn-decline:hover {
            background: #dc2626;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6b7280;
            font-size: 12px;
        }
        .custom-message {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 6px 6px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">📝 {{ config('app.name') }}</div>
            <h1 style="color: #1f2937; margin: 0;">Undangan Bergabung di Proyek</h1>
        </div>

        <p>Halo!</p>

        <p><strong>{{ $invitedBy->name }}</strong> mengundang Anda untuk bergabung dalam proyek kolaborasi:</p>

        <div class="project-info">
            <div class="project-name">{{ $project->business_name }}</div>
            <div class="project-type">{{ $project->business_type }}</div>
            <div class="role-badge">{{ ucfirst($invitation->role) }}</div>
            
            @if($project->business_description)
            <p style="margin: 15px 0 5px 0; color: #4b5563;">
                <strong>Deskripsi:</strong><br>
                {{ $project->business_description }}
            </p>
            @endif

            @if($project->target_audience)
            <p style="margin: 10px 0 5px 0; color: #4b5563;">
                <strong>Target Audience:</strong> {{ $project->target_audience }}
            </p>
            @endif
        </div>

        @if($customMessage)
        <div class="custom-message">
            <strong>Pesan dari {{ $invitedBy->name }}:</strong><br>
            {{ $customMessage }}
        </div>
        @endif

        <p>Sebagai <strong>{{ ucfirst($invitation->role) }}</strong>, Anda akan dapat:</p>
        
        <ul style="color: #4b5563;">
            @if($invitation->role === 'admin')
            <li>✅ Membuat dan mengedit konten</li>
            <li>✅ Menyetujui atau menolak konten</li>
            <li>✅ Mengelola anggota tim</li>
            <li>✅ Mengakses semua fitur proyek</li>
            @elseif($invitation->role === 'editor')
            <li>✅ Membuat dan mengedit konten</li>
            <li>✅ Mengirim konten untuk review</li>
            <li>✅ Berkolaborasi dengan tim</li>
            @else
            <li>👁️ Melihat semua konten proyek</li>
            <li>💬 Memberikan komentar dan feedback</li>
            <li>📊 Mengakses laporan proyek</li>
            @endif
        </ul>

        <div class="buttons">
            <a href="{{ $acceptUrl }}" class="btn btn-accept">✅ Terima Undangan</a>
            <a href="{{ $declineUrl }}" class="btn btn-decline">❌ Tolak Undangan</a>
        </div>

        <p style="color: #6b7280; font-size: 14px;">
            <strong>Catatan:</strong> Undangan ini akan kedaluwarsa dalam 7 hari. Jika Anda tidak dapat mengklik tombol di atas, salin dan tempel URL berikut ke browser Anda:
        </p>

        <p style="word-break: break-all; color: #6b7280; font-size: 12px;">
            Terima: {{ $acceptUrl }}<br>
            Tolak: {{ $declineUrl }}
        </p>

        <div class="footer">
            <p>Email ini dikirim secara otomatis dari sistem {{ config('app.name') }}.<br>
            Jika Anda memiliki pertanyaan, silakan hubungi {{ $invitedBy->name }} atau tim support kami.</p>
            
            <p style="margin-top: 15px;">
                © {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.
            </p>
        </div>
    </div>
</body>
</html>