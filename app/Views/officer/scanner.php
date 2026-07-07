<?php $pageTitle = 'QR Scanner'; ?>

<section class="dashboard-hero">
    <div class="dashboard-hero__panel">
        <div class="dashboard-hero__eyebrow">Camera scan</div>
        <h1 class="dashboard-hero__title">Scan ticket QR codes with a clearer interface.</h1>
        <p class="dashboard-hero__copy">
            Point the camera at a passenger ticket QR code or paste a token manually when camera access is unavailable.
        </p>
    </div>

    <aside class="dashboard-hero__insight">
        <div>
            <div class="mini-label">Scanner</div>
            <div class="mini-value">QR verification</div>
            <div class="mini-note">Designed for fast boarding desk checks.</div>
        </div>
    </aside>
</section>

<section class="dashboard-grid dashboard-grid--two">
    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Live scanner</div>
                <h2 class="page-title mt-1">Camera feed</h2>
                <p>Use the camera or enter a ticket token manually.</p>
            </div>
        </div>
        <div class="ratio ratio-16x9 rounded-4 overflow-hidden bg-dark">
            <video id="scannerVideo" autoplay playsinline class="w-100 h-100"></video>
        </div>
        <div class="mt-3 d-flex flex-wrap gap-2">
            <button class="btn btn-primary" id="startScannerBtn" type="button">Start scanner</button>
            <button class="btn btn-outline-secondary" id="stopScannerBtn" type="button">Stop scanner</button>
        </div>
        <div class="mt-3">
            <label class="form-label">Manual QR payload or ticket number</label>
            <input type="text" id="manualScanInput" class="form-control" placeholder="Paste token or URL">
        </div>
    </div>

    <div class="panel-card">
        <div class="section-heading mb-3">
            <div>
                <div class="mini-label">Scan result</div>
                <h2 class="page-title mt-1">Verification output</h2>
                <p>Results appear here after a successful scan.</p>
            </div>
        </div>
        <div id="scanResult" class="empty-state">
            <i class="bi bi-qr-code-scan"></i>
            <h4>Waiting for a scan</h4>
            <p class="text-muted mb-0">Point the camera at a passenger ticket QR code.</p>
        </div>
    </div>
</section>

<script>
(() => {
    const baseUrl = <?= json_encode(rtrim(url(''), '/')) ?>;
    const video = document.getElementById('scannerVideo');
    const startBtn = document.getElementById('startScannerBtn');
    const stopBtn = document.getElementById('stopScannerBtn');
    const manualInput = document.getElementById('manualScanInput');
    const resultBox = document.getElementById('scanResult');
    let stream = null;
    let detector = null;
    let scanning = false;

    const renderResult = (payload, ok = true) => {
        if (!ok) {
            resultBox.innerHTML = `<div class="text-danger fw-semibold">${payload}</div>`;
            return;
        }
        resultBox.innerHTML = `
            <div class="fw-semibold mb-2">${payload.passenger_name ?? 'Passenger'}</div>
            <div class="text-muted mb-1"><strong>Booking:</strong> ${payload.booking_number ?? '-'}</div>
            <div class="text-muted mb-1"><strong>Seat:</strong> ${payload.seat_number ?? '-'}</div>
            <div class="text-muted mb-1"><strong>Status:</strong> ${payload.status ?? '-'}</div>
        `;
    };

    const resolveTicket = (value) => {
        let token = value;
        const match = String(value).match(/tickets\/verify\/([^/?#]+)/);
        if (match) token = match[1];
        fetch(`${baseUrl}/tickets/verify/${encodeURIComponent(token)}`)
            .then(response => response.json())
            .then(data => {
                if (!data.found) {
                    renderResult('No ticket was found for that scan.', false);
                    return;
                }
                renderResult(data.ticket, true);
            })
            .catch(() => renderResult('Unable to verify the scanned code.', false));
    };

    const stopScanner = async () => {
        scanning = false;
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    };

    const startScanner = async () => {
        if (!('BarcodeDetector' in window)) {
            renderResult('Camera scanning is not supported in this browser. Use the manual input field instead.', false);
            return;
        }
        detector = detector || new BarcodeDetector({ formats: ['qr_code'] });
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        video.srcObject = stream;
        scanning = true;

        const loop = async () => {
            if (!scanning || !video.videoWidth) {
                if (scanning) requestAnimationFrame(loop);
                return;
            }
            try {
                const codes = await detector.detect(video);
                if (codes.length > 0) {
                    await stopScanner();
                    resolveTicket(codes[0].rawValue);
                    return;
                }
            } catch (error) {
                renderResult('Scanner error: ' + error.message, false);
            }
            if (scanning) requestAnimationFrame(loop);
        };
        requestAnimationFrame(loop);
    };

    startBtn?.addEventListener('click', () => startScanner().catch(error => renderResult(error.message, false)));
    stopBtn?.addEventListener('click', () => stopScanner());
    manualInput?.addEventListener('change', () => {
        if (manualInput.value.trim()) resolveTicket(manualInput.value.trim());
    });
})();
</script>
