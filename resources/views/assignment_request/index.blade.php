<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Demande d'assistance {{ $assignmentRequest->reference }} / BCA-CI</title>
    <style>
        <?php include(public_path().'/bootstrap/css/bootstrap.css'); ?>

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            color: #222;
            margin: 1cm;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }

        h2 {
            font-size: 14px;
            margin: 18px 0 8px;
            border-bottom: 1px solid #333;
            padding-bottom: 4px;
            text-transform: uppercase;
        }

        .header {
            width: 100%;
            margin-bottom: 16px;
        }

        .header td {
            vertical-align: middle;
        }

        .meta {
            text-align: right;
            font-size: 12px;
        }

        .logo {
            max-height: 60px;
        }

        table.info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        table.info th,
        table.info td {
            border: 1px solid #444;
            padding: 6px 8px;
            vertical-align: top;
        }

        table.info th {
            width: 32%;
            background: #f3f3f3;
            text-align: left;
            font-weight: bold;
        }

        .photos img {
            width: 140px;
            height: 100px;
            object-fit: cover;
            margin: 4px;
            border: 1px solid #ccc;
        }

        .footer {
            margin-top: 24px;
            font-size: 10px;
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td width="30%">
                @if(!empty($logo))
                    <img src="{{ $logo }}" class="logo" alt="logo">
                @endif
            </td>
            <td width="70%" class="meta">
                <h1>Récapitulatif de demande d'assistance</h1>
                <div><strong>Référence :</strong> {{ $assignmentRequest->reference }}</div>
                <div><strong>Statut :</strong> {{ $assignmentRequest->status->label ?? '' }}</div>
                <div><strong>Créée le :</strong>
                    @if($assignmentRequest->created_at)
                        {{ \Carbon\Carbon::parse($assignmentRequest->created_at)->format('d/m/Y H:i') }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <h2>Informations générales</h2>
    <table class="info">
        <tr>
            <th>Date</th>
            <td>
                @if($assignmentRequest->date)
                    {{ \Carbon\Carbon::parse($assignmentRequest->date)->format('d/m/Y') }}
                @endif
            </td>
        </tr>
        <tr>
            <th>Lieu d'assistance</th>
            <td>{{ $assignmentRequest->assistance_place ?? '' }}</td>
        </tr>
        <tr>
            <th>Observation</th>
            <td>{!! nl2br(e($assignmentRequest->observation ?? '')) !!}</td>
        </tr>
        <tr>
            <th>Créée par</th>
            <td>{{ $assignmentRequest->createdBy->name ?? '' }}</td>
        </tr>
    </table>

    <h2>Client</h2>
    <table class="info">
        <tr>
            <th>Nom</th>
            <td>{{ $assignmentRequest->client->name ?? '' }}</td>
        </tr>
        <tr>
            <th>Téléphone</th>
            <td>
                {{ $assignmentRequest->client->phone_1 ?? '' }}
                @if(!empty($assignmentRequest->client->phone_2))
                    / {{ $assignmentRequest->client->phone_2 }}
                @endif
            </td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $assignmentRequest->client->email ?? '' }}</td>
        </tr>
        <tr>
            <th>Adresse</th>
            <td>{{ $assignmentRequest->client->address ?? '' }}</td>
        </tr>
    </table>

    <h2>Véhicule</h2>
    <table class="info">
        <tr>
            <th>Immatriculation</th>
            <td>{{ $assignmentRequest->vehicle->license_plate ?? '' }}</td>
        </tr>
        <tr>
            <th>Marque / Modèle</th>
            <td>
                {{ $assignmentRequest->vehicle->brand->label ?? '' }}
                {{ $assignmentRequest->vehicle->vehicleModel->label ?? '' }}
            </td>
        </tr>
        <tr>
            <th>Couleur</th>
            <td>{{ $assignmentRequest->vehicle->color->label ?? '' }}</td>
        </tr>
        <tr>
            <th>Énergie</th>
            <td>{{ $assignmentRequest->vehicle->vehicleEnergy->label ?? '' }}</td>
        </tr>
        <tr>
            <th>N° de série</th>
            <td>{{ $assignmentRequest->vehicle->serial_number ?? '' }}</td>
        </tr>
        <tr>
            <th>Mise en circulation</th>
            <td>
                @if(!empty($assignmentRequest->vehicle?->first_entry_into_circulation_date))
                    {{ \Carbon\Carbon::parse($assignmentRequest->vehicle->first_entry_into_circulation_date)->format('d/m/Y') }}
                @endif
            </td>
        </tr>
        <tr>
            <th>Kilométrage</th>
            <td>{{ $assignmentRequest->vehicle->mileage ?? '' }}</td>
        </tr>
    </table>

    <h2>Réparateur(s)</h2>
    <table class="info">
        @forelse(($assignmentRequest->repairers ?? []) as $repairer)
            <tr>
                <th>{{ $repairer->name }}</th>
                <td>
                    {{ $repairer->telephone ?? '' }}
                    @if(!empty($repairer->email))
                        — {{ $repairer->email }}
                    @endif
                    @if(!empty($repairer->address))
                        <br>{{ $repairer->address }}
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2">Aucun réparateur renseigné</td>
            </tr>
        @endforelse
    </table>

    @if(($assignmentRequest->photos ?? collect())->isNotEmpty())
        <h2>Photos</h2>
        <div class="photos">
            @foreach($assignmentRequest->photos as $photo)
                @php
                    $photoPath = public_path('storage/photos/assignment_request/'.$assignmentRequest->reference.'/'.$photo->name);
                @endphp
                @if(file_exists($photoPath))
                    @php
                        $type = pathinfo($photoPath, PATHINFO_EXTENSION);
                        $src = 'data:image/'.$type.';base64,'.base64_encode(file_get_contents($photoPath));
                    @endphp
                    <img src="{{ $src }}" alt="{{ $photo->name }}">
                @endif
            @endforeach
        </div>
    @endif

    <div class="footer">
        Document généré automatiquement — BCA-CI — {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
