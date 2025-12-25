<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner de Badge - Congrès</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --warning-gradient: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
            --danger-gradient: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .scanner-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .scanner-header {
            background: var(--primary-gradient);
            color: white;
            padding: 2rem;
            border-radius: 15px 15px 0 0;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        .scanner-main {
            background: white;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Scanner Section */
        .scanner-section {
            padding: 2rem;
            border-bottom: 1px solid #eee;
        }

        .scanner-wrapper {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        #reader {
            width: 100%;
            height: 400px;
            background: #000;
        }

        .scanner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }

        .scanner-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 70%;
            border: 3px solid #38ef7d;
            border-radius: 10px;
            box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.7);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                border-color: #38ef7d;
            }

            50% {
                border-color: #667eea;
            }
        }

        .scanner-corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border-color: #38ef7d;
        }

        .corner-tl {
            top: -3px;
            left: -3px;
            border-top: 5px solid;
            border-left: 5px solid;
            border-radius: 10px 0 0 0;
        }

        .corner-tr {
            top: -3px;
            right: -3px;
            border-top: 5px solid;
            border-right: 5px solid;
            border-radius: 0 10px 0 0;
        }

        .corner-bl {
            bottom: -3px;
            left: -3px;
            border-bottom: 5px solid;
            border-left: 5px solid;
            border-radius: 0 0 0 10px;
        }

        .corner-br {
            bottom: -3px;
            right: -3px;
            border-bottom: 5px solid;
            border-right: 5px solid;
            border-radius: 0 0 10px 0;
        }

        /* Scanner Controls */
        .scanner-controls {
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 2rem;
        }

        .control-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-start {
            background: var(--success-gradient);
            color: white;
        }

        .btn-stop {
            background: var(--danger-gradient);
            color: white;
        }

        .btn-switch {
            background: var(--warning-gradient);
            color: white;
        }

        /* Statistics */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-top: 4px solid;
        }

        .stat-total {
            border-color: #667eea;
        }

        .stat-scanned {
            border-color: #38ef7d;
        }

        .stat-unique {
            border-color: #ffc107;
        }

        .stat-remaining {
            border-color: #eb3349;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-total .stat-number {
            color: #667eea;
        }

        .stat-scanned .stat-number {
            color: #28a745;
        }

        .stat-unique .stat-number {
            color: #ffc107;
        }

        .stat-remaining .stat-number {
            color: #dc3545;
        }

        /* Recent Scans */
        .recent-scans {
            padding: 2rem;
            background: #f8f9fa;
        }

        .scan-item {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .scan-info {
            flex: 1;
        }

        .scan-name {
            font-weight: 600;
            color: #2d3748;
        }

        .scan-details {
            color: #718096;
            font-size: 0.9rem;
        }

        .scan-time {
            color: #6c757d;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .duplicate-badge {
            background: #ffc107;
            color: #212529;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .scanner-header {
                padding: 1.5rem;
            }

            #reader {
                height: 300px;
            }

            .scanner-controls .d-flex {
                flex-direction: column;
                gap: 0.75rem;
            }

            .control-btn {
                width: 100%;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            .scanner-section {
                padding: 1rem;
            }

            #reader {
                height: 250px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="scanner-container py-4">
        <!-- Header -->
        <div class="scanner-header text-center mb-4">
            <h1 class="mb-3">
                <i class="fas fa-qr-code me-2"></i>
                {{ __('scanner.title') }}
            </h1>
            @php
                $congres = App\Models\Congress::latest()->first();
                \Carbon\Carbon::setLocale(app()->getLocale());
            @endphp
            <h2 class="h5 mb-2">{!! $congres->translate(app()->getLocale(), 'fallbackLocale')->title !!}</h2>
            <p class="mb-0 opacity-90">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ now()->format('d/m/Y') }}
                <span class="mx-3">•</span>
                <i class="fas fa-clock me-2"></i>
                <span id="currentTime">{{ now()->format('H:i:s') }}</span>
            </p>
        </div>
        <div id="activeSessionBar" class="alert alert-success d-none d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-door-open me-2"></i>
                <strong>
                    {{ app()->getLocale() == 'fr' ? 'Session active :' : 'Active session :' }}
                </strong>
                <span id="activeSessionName"></span>
            </div>
            <button class="btn btn-sm btn-danger" id="exitSession">
                <i class="fas fa-sign-out-alt"></i>
                {{ app()->getLocale() == 'fr' ? 'Quitter' : 'Exit' }}
            </button>
        </div>
        <!-- Main Content -->
        <div class="scanner-main">
            <!-- Scanner Section -->
            <div class="scanner-section">
                <div class="scanner-wrapper">
                    <div id="reader"></div>
                    <div class="scanner-overlay">
                        <div class="scanner-frame">
                            <div class="scanner-corner corner-tl"></div>
                            <div class="scanner-corner corner-tr"></div>
                            <div class="scanner-corner corner-bl"></div>
                            <div class="scanner-corner corner-br"></div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <i class="glyphicon glyphicon-list-alt"></i>
                                        {{ app()->getLocale() == 'fr' ? 'Sessions Disponibles' : 'Available Sessions' }}
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    @if ($sessions->count() > 0)
                                        <div class="list-group">
                                            @foreach ($sessions as $session)
                                                <div class="list-group-item session-item"
                                                    data-session-id="{{ $session->id }}"
                                                    data-session-name="{{ $session->translate(app()->getLocale(), 'fallbackLocale')->libelle }}"
                                                    style="cursor:pointer">
                                                    <h4>
                                                        {{ $session->translate(app()->getLocale(), 'fallbackLocale')->libelle }}
                                                    </h4>

                                                </div>

                                        </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="glyphicon glyphicon-info-sign"></i>
                                {{ app()->getLocale() == 'fr' ? 'Aucune session en cours' : 'No active sessions' }}
                            </div>
                            @endif
                        </div>
                        @if ($sessions->count() > 0)
                            <div class="panel-footer">
                                <small class="text-muted">
                                    <i class="glyphicon glyphicon-stats"></i>
                                    {{ $sessions->count() }}
                                    {{ app()->getLocale() == 'fr' ? 'session(s) trouvée(s)' : 'session(s) found' }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="scanner-controls">
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <button class="control-btn btn-start" id="startScanner">
                    <i class="fas fa-play-circle"></i>
                    {{ app()->getLocale() == 'fr' ? 'Démarrer le Scanner' : 'Start Scanner' }}
                </button>
                <button class="control-btn btn-stop" id="stopScanner" disabled>
                    <i class="fas fa-stop-circle"></i>
                    {{ app()->getLocale() == 'fr' ? 'Arrêter le Scanner' : 'Stop Scanner' }}
                </button>
                <button class="control-btn btn-switch" id="switchCamera">
                    <i class="fas fa-sync-alt"></i>
                    {{ app()->getLocale() == 'fr' ? 'Changer la Caméra' : 'Change Camera' }}
                </button>
            </div>

            <div class="text-center mt-3">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="soundToggle" checked>
                    <label class="form-check-label" for="soundToggle">
                        <i class="fas fa-volume-up me-1"></i>
                        {{ app()->getLocale() == 'fr' ? 'Son' : 'Sound' }}
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="vibrateToggle" checked>
                    <label class="form-check-label" for="vibrateToggle">
                        <i class="fas fa-vibrate me-1"></i>
                        {{ app()->getLocale() == 'fr' ? 'Vibration' : 'Vibration' }}
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    {{-- <div class="stats-grid">
        <div class="stat-card stat-total">
            <div class="stat-number" id="totalParticipants">{{ $totalParticipants ?? 0 }}</div>
            <div class="stat-label">
                {{ app()->getLocale() == 'fr' ? 'Participants Totaux' : 'Total Participants' }}</div>
        </div>
        <div class="stat-card stat-scanned">
            <div class="stat-number" id="scannedToday">{{ $scannedToday ?? 0 }}</div>
            <div class="stat-label">
                {{ app()->getLocale() == 'fr' ? 'Scannés Aujourd’hui' : 'Scanned Today' }}</div>
        </div>
        <div class="stat-card stat-unique">
            <div class="stat-number" id="uniqueScanned">{{ $uniqueScannedToday ?? 0 }}</div>
            <div class="stat-label">
                {{ app()->getLocale() == 'fr' ? 'Participants Uniques' : 'Unique Participants' }}
            </div>
        </div>
        <div class="stat-card stat-remaining">
            <div class="stat-number" id="remainingToday">
                {{ ($totalParticipants ?? 0) - ($uniqueScannedToday ?? 0) }}</div>
            <div class="stat-label">
                {{ app()->getLocale() == 'fr' ? 'Participants Restants' : 'Remaining Participants' }}
            </div>
        </div>
    </div> --}}

    <!-- Recent Scans -->
    {{-- <div class="recent-scans">
        <h4 class="mb-3">
            <i class="fas fa-history me-2"></i>
            {{ app()->getLocale() == 'fr' ? 'Scans Récents' : 'Recent Scans' }}
        </h4>
        <div id="recentScansList">
            @if (isset($recentScans) && $recentScans->count() > 0)
                @foreach ($recentScans as $scan)
                    <div class="scan-item"
                        style="border-left-color: {{ $scan->participant->badge_color->color ?? '#667eea' }}">
                        <div class="scan-info">
                            <div class="scan-name">
                                {{ $scan->participant->badge_full_name ?? $scan->participant->civility->libelle . ' ' . $scan->participant->fname . ' ' . $scan->participant->lname }}
                                @if ($scan->is_duplicate)
                                    <span class="duplicate-badge">DOUBLE</span>
                                @endif
                            </div>
                            <div class="scan-details">
                                {{ $scan->participant->organisation }}
                            </div>
                        </div>
                        <div class="scan-time">
                            {{ $scan->scanned_at->format('H:i') }}
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-qr-code fa-2x mb-3"></i>
                    <p class="mb-0">
                        {{ app()->getLocale() == 'fr' ? 'Aucun Scan Récents' : 'No Recent Scans' }}
                    </p>
                </div>
            @endif
        </div>
    </div> --}}



    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        // Configuration
        const config = {
            apiUrl: 'https://congress.afwasa.org//get_register/scanner',
            soundEnabled: true,
            vibrateEnabled: true,
            backCameraFirst: true
        };

        // Variables globales
        let html5QrcodeScanner = null;
        let isScanning = false;
        let currentCameraId = null;
        let cameras = [];
        let backCamera = null;
        let frontCamera = null;

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initDateTime();
            initCameraDetection();
            initEventListeners();
        });

        // Mise à jour de l'heure
        function initDateTime() {
            updateTime();
            setInterval(updateTime, 1000);
        }

        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent =
                now.toLocaleTimeString('fr-FR', {
                    hour12: false
                });
        }

        // Détection des caméras
        async function initCameraDetection() {
            try {
                cameras = await Html5Qrcode.getCameras();

                if (cameras.length === 0) {
                    showError('Aucune caméra détectée');
                    return;
                }

                // Identifier caméra avant/arrière
                cameras.forEach(camera => {
                    const label = camera.label.toLowerCase();
                    if (label.includes('back') || label.includes('arrière') ||
                        label.includes('rear') || !label.includes('front')) {
                        backCamera = camera;
                    } else if (label.includes('front') || label.includes('avant')) {
                        frontCamera = camera;
                    }
                });

                // Utiliser caméra arrière par défaut si disponible
                currentCameraId = config.backCameraFirst && backCamera ?
                    backCamera.id : cameras[0].id;

                console.log('Caméra sélectionnée:', cameras.find(c => c.id === currentCameraId)?.label);

            } catch (error) {
                console.error('Erreur caméra:', error);
                showError('Impossible d\'accéder aux caméras');
            }
        }

        // Initialiser les événements
        function initEventListeners() {
            // Démarrer le scanner
            document.getElementById('startScanner').addEventListener('click', startScanner);

            // Arrêter le scanner
            document.getElementById('stopScanner').addEventListener('click', stopScanner);

            // Changer de caméra
            document.getElementById('switchCamera').addEventListener('click', switchCamera);

            // Toggles
            document.getElementById('soundToggle').addEventListener('change', function() {
                config.soundEnabled = this.checked;
            });

            document.getElementById('vibrateToggle').addEventListener('change', function() {
                config.vibrateEnabled = this.checked;
            });
        }

        // Démarrer le scanner
        async function startScanner() {
            if (!currentCameraId) {
                showError('Aucune caméra disponible');
                return;
            }

            try {
                html5QrcodeScanner = new Html5Qrcode("reader");

                await html5QrcodeScanner.start(
                    currentCameraId, {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        },
                        aspectRatio: 1.0
                    },
                    onScanSuccess,
                    onScanError
                );

                isScanning = true;
                updateScannerControls(true);
                showSuccess('Scanner démarré avec succès');

            } catch (error) {
                console.error('Erreur démarrage scanner:', error);
                showError('Erreur démarrage scanner: ' + error.message);
            }
        }

        // Arrêter le scanner
        async function stopScanner() {
            if (!html5QrcodeScanner || !isScanning) return;

            try {
                await html5QrcodeScanner.stop();
                isScanning = false;
                updateScannerControls(false);
                showInfo('Scanner arrêté');
            } catch (error) {
                console.error('Erreur arrêt scanner:', error);
            }
        }

        // Changer de caméra
        async function switchCamera() {
            if (cameras.length < 2) {
                showInfo('Une seule caméra disponible');
                return;
            }

            if (isScanning) {
                await html5QrcodeScanner.stop();
            }

            // Trouver l'index de la caméra actuelle
            const currentIndex = cameras.findIndex(cam => cam.id === currentCameraId);
            const nextIndex = (currentIndex + 1) % cameras.length;
            currentCameraId = cameras[nextIndex].id;

            const cameraName = cameras[nextIndex].label.split('(')[0].trim();
            showInfo(`Caméra changée: ${cameraName}`);

            if (isScanning) {
                // Redémarrer avec la nouvelle caméra
                setTimeout(() => startScanner(), 500);
            }
        }

        // Callback succès scan
        async function onScanSuccess(decodedText) {
            // Feedback
            if (config.vibrateEnabled && navigator.vibrate) {
                navigator.vibrate(100);
            }

            if (config.soundEnabled) {
                playScanSound();
            }

            // Désactiver temporairement le scanner pour éviter les scans multiples
            if (html5QrcodeScanner) {
                await html5QrcodeScanner.pause();
            }

            // Vérifier le participant via API
            checkParticipant(decodedText);
        }

        // Callback erreur scan
        function onScanError(error) {
            // Ignorer les erreurs silencieusement
            console.debug('Scan error:', error);
        }

        // Vérifier le participant via API
        async function checkParticipant(participantId) {
            try {
                if (!activeSessionId) {
                    showWarning('Veuillez sélectionner une session avant de scanner');
                    return;
                }
                const response = await fetch(
                    `https://congress.afwasa.org/get_register/scanner/session?participant_id=${participantId}&session_id=${activeSessionId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                const data = await response.json();

                if (data.success) {
                    handleScanSuccess(data.participant, data.message);
                } else {
                    handleScanError(participantId, data.message);
                }

            } catch (error) {
                console.error('Erreur API:', error);
                handleScanError(participantId, 'Erreur de connexion');
            } finally {
                // Réactiver le scanner après un court délai
                setTimeout(async () => {
                    if (html5QrcodeScanner && isScanning) {
                        await html5QrcodeScanner.resume();
                    }
                }, 1500);
            }
        }

        // Gérer succès scan
        function handleScanSuccess(participant, message) {
            // Mettre à jour les statistiques
            updateStatistics();

            // Ajouter au historique
            addToRecentScans(participant);

            // Afficher notification
            if (participant.already_scanned) {
                showWarning(message, participant);
            } else {
                showSuccess(message, participant);
            }
        }

        // Gérer erreur scan
        function handleScanError(participantId, message) {
            showError(`${message} - ID: ${participantId}`);
        }

        // Mettre à jour les statistiques
        async function updateStatistics() {
            try {
                const response = await fetch(`${config.apiUrl}/api/stats`);
                const data = await response.json();

                if (data.success) {
                    const stats = data.stats;
                    document.getElementById('scannedToday').textContent = stats.scanned_today;
                    document.getElementById('uniqueScanned').textContent = stats.unique_scanned_today;
                    document.getElementById('remainingToday').textContent = stats.remaining_today;
                }
            } catch (error) {
                console.error('Erreur stats:', error);
            }
        }

        // Ajouter aux scans récents
        async function addToRecentScans(participant) {
            try {
                const response = await fetch(`${config.apiUrl}/api/recent-scans`);
                const data = await response.json();

                if (data.success && data.scans.length > 0) {
                    const container = document.getElementById('recentScansList');
                    container.innerHTML = data.scans.map(scan => `
                        <div class="scan-item" style="border-left-color: ${scan.badge_color}">
                            <div class="scan-info">
                                <div class="scan-name">
                                    ${scan.name}
                                    ${scan.is_duplicate ? '<span class="duplicate-badge">DOUBLE</span>' : ''}
                                </div>
                                <div class="scan-details">
                                    ${scan.organization}
                                </div>
                            </div>
                            <div class="scan-time">
                                ${scan.time}
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Erreur scans récents:', error);
            }
        }

        // Mettre à jour les contrôles
        function updateScannerControls(scanning) {
            document.getElementById('startScanner').disabled = scanning;
            document.getElementById('stopScanner').disabled = !scanning;
            document.getElementById('switchCamera').disabled = scanning && cameras.length < 2;
        }

        // Jouer son de scan
        function playScanSound() {
            const audio = new Audio();
            audio.src = 'https://assets.mixkit.co/sfx/preview/mixkit-correct-answer-tone-2870.mp3';
            audio.volume = 0.3;
            audio.play().catch(e => console.log("Audio error:", e));
        }

        // Notifications SweetAlert
        function showSuccess(message, participant = null) {
            let html = `<div class="text-center"><i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                       <h5 class="mb-2">${message}</h5>`;

            if (participant) {
                html += `<div class="mt-3 p-3 bg-light rounded">
                           <div class="fw-bold">${participant.name}</div>
                           <small class="text-muted">${participant.organization}</small>
                           <div class="mt-2">
                             <span class="badge" style="background: ${participant.badge_color}">
                               ${participant.badge_type}
                             </span>
                           </div>
                         </div>`;
            }

            html += '</div>';

            Swal.fire({
                html: html,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                width: 400
            });
        }

        function showWarning(message, participant = null) {
            let html = `<div class="text-center"><i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                       <h5 class="mb-2">${message}</h5>`;

            if (participant) {
                html += `<div class="mt-3 p-3 bg-light rounded">
                           <div class="fw-bold">${participant.name}</div>
                           <small class="text-muted">${participant.organization}</small>
                           <div class="mt-2">
                             <span class="badge bg-warning">DÉJÀ SCANNÉ</span>
                             <span class="badge ms-2" style="background: ${participant.badge_color}">
                               ${participant.badge_type}
                             </span>
                           </div>
                         </div>`;
            }

            html += '</div>';

            Swal.fire({
                html: html,
                timer: 4000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                width: 400
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end'
            });
        }

        function showInfo(message) {
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: message,
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end'
            });
        }
    </script>

    <script>
        let activeSessionId = null;
        let activeSessionName = null;

        document.querySelectorAll('.session-item').forEach(item => {
            item.addEventListener('click', function() {

                activeSessionId = this.dataset.sessionId;
                activeSessionName = this.dataset.sessionName;

                // Afficher la session active
                document.getElementById('activeSessionName').innerText = activeSessionName;
                document.getElementById('activeSessionBar').classList.remove('d-none');

                // Cacher les autres sessions
                document.querySelectorAll('.session-item').forEach(el => {
                    if (el !== this) el.classList.add('d-none');
                });

                showInfo(`Session "${activeSessionName}" activée`);
            });
        });

        // Quitter la session
        document.getElementById('exitSession').addEventListener('click', function() {

            Swal.fire({
                title: 'Quitter la session ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui',
                cancelButtonText: 'Non'
            }).then(result => {
                if (result.isConfirmed) {

                    activeSessionId = null;
                    activeSessionName = null;

                    document.getElementById('activeSessionBar').classList.add('d-none');

                    document.querySelectorAll('.session-item').forEach(el => {
                        el.classList.remove('d-none');
                    });

                    showInfo('Session quittée');
                }
            });
        });
    </script>

</body>

</html>
