<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 16px; margin-bottom: 2px; }
        h2 { font-size: 13px; margin-top: 20px; margin-bottom: 6px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .subtitle { font-size: 10px; color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 4px 6px; text-align: left; }
        th { background-color: #f3f4f6; }
        .stats-table td { border: none; padding: 2px 6px 2px 0; }
    </style>
</head>
<body>
    <h1>Lokal ng Lopez</h1>
    <div class="subtitle">Talaan ng mga Mang-aawit {{ now()->format('F d, Y h:i A') }}</div>

    <h2>Mang-aawit</h2>
    <table>
        <thead>
            <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Birthday</th>
                <th>Purok-Grupo</th>
                <th>Tungkulin</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mangAawitList as $m)
                <tr>
                    <td>{{ $m['last_name'] }}</td>
                    <td>{{ $m['first_name'] }}</td>
                    <td>{{ $m['birthday'] }}</td>
                    <td>{{ $m['purok_grupo'] }}</td>
                    <td>{{ $m['tungkulin'] }}</td>
                    <td>{{ $m['status'] }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="stats-table">
        <tr><td><strong>Total Mang-aawit:</strong> {{ $stats['mangAawit']['total'] }}</td><td><strong>Male:</strong> {{ $stats['mangAawit']['totalMale'] }}</td><td><strong>Female:</strong> {{ $stats['mangAawit']['totalFemale'] }}</td></tr>
        <tr><td><strong>Active:</strong> {{ $stats['mangAawit']['active'] }}</td><td><strong>Male:</strong> {{ $stats['mangAawit']['activeMale'] }}</td><td><strong>Female:</strong> {{ $stats['mangAawit']['activeFemale'] }}</td></tr>
        <tr><td><strong>Inactive:</strong> {{ $stats['mangAawit']['inactive'] }}</td><td><strong>Male:</strong> {{ $stats['mangAawit']['inactiveMale'] }}</td><td><strong>Female:</strong> {{ $stats['mangAawit']['inactiveFemale'] }}</td></tr>
    </table>

    <h2>Nagsasanay</h2>
    <table>
        <thead>
            <tr>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Birthday</th>
                <th>Purok-Grupo</th>
                <th>Track</th>
                <th>Progress Stage</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nagsasanayList as $n)
                <tr>
                    <td>{{ $n['last_name'] }}</td>
                    <td>{{ $n['first_name'] }}</td>
                    <td>{{ $n['birthday'] }}</td>
                    <td>{{ $n['purok_grupo'] }}</td>
                    <td>{{ $n['track'] }}</td>
                    <td>{{ $n['progress_stage'] }}</td>
                    <td>{{ $n['status'] }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="stats-table">
        <tr><td><strong>Total Nagsasanay:</strong> {{ $stats['nagsasanay']['total'] }}</td></tr>
        <tr><td><strong>Mang-aawit track — Male:</strong> {{ $stats['nagsasanay']['mangAawitMale'] }}</td><td><strong>Female:</strong> {{ $stats['nagsasanay']['mangAawitFemale'] }}</td></tr>
        <tr><td><strong>Organista track — Male:</strong> {{ $stats['nagsasanay']['organistaMale'] }}</td><td><strong>Female:</strong> {{ $stats['nagsasanay']['organistaFemale'] }}</td></tr>
    </table>
</body>
</html>