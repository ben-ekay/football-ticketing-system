// ============================================
// GoalTicket - QR Scanner
// ============================================
// Uses the html5-qrcode library to access the camera
// and decode QR codes on the fly.

let html5QrCode = null;
let isScanning = false;
let lastScannedToken = null;
let cooldownUntil = 0;

// Stats counters
let validCount = 0;
let invalidCount = 0;

const statusEl = document.getElementById('scan-status');
const startBtn = document.getElementById('scan-start');
const stopBtn = document.getElementById('scan-stop');
const validEl = document.getElementById('count-valid');
const invalidEl = document.getElementById('count-invalid');

function setStatus(type, icon, main, sub) {
    statusEl.className = 'scan-status ' + type;
    statusEl.innerHTML = `
        <div class="icon">${icon}</div>
        <div class="main-text">${main}</div>
        <div class="sub-text">${sub || ''}</div>
    `;
}

function updateStats() {
    validEl.textContent = validCount;
    invalidEl.textContent = invalidCount;
}

async function onScanSuccess(decodedText) {
    const now = Date.now();

    // Cooldown: ignore same token within 3 seconds to avoid re-scanning
    if (decodedText === lastScannedToken && now < cooldownUntil) {
        return;
    }

    lastScannedToken = decodedText;
    cooldownUntil = now + 3000;

    setStatus('idle', '⏳', 'Validating...', '');

    try {
        const response = await fetch('api/validate_ticket.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_token: decodedText })
        });

        const data = await response.json();

        if (data.ok && data.status === 'valid') {
            validCount++;
            setStatus('valid', '✓',
                'Entry granted',
                `${data.holder} · vs ${data.opposition}`
            );
            playBeep(true);
        } else if (data.status === 'already_used') {
            invalidCount++;
            setStatus('invalid', '✗',
                'Already used',
                `Holder: ${data.holder} · Scanned at: ${data.scanned_at}`
            );
            playBeep(false);
        } else if (data.status === 'wrong_day') {
            invalidCount++;
            setStatus('invalid', '✗',
                'Wrong day',
                `Ticket is for ${data.match_date}`
            );
            playBeep(false);
        } else {
            invalidCount++;
            setStatus('invalid', '✗',
                'Invalid ticket',
                data.reason || 'Not recognised'
            );
            playBeep(false);
        }

        updateStats();

    } catch (err) {
        setStatus('invalid', '⚠️', 'Connection error', err.message);
    }
}

function onScanFailure(error) {
    // Called many times per second when no QR in view — ignore silently
}

function playBeep(success) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = success ? 880 : 220;
        gain.gain.value = 0.1;
        osc.start();
        setTimeout(() => { osc.stop(); }, success ? 200 : 400);
    } catch (e) {
        // No audio support — silent fail
    }
}

async function startScanner() {
    if (isScanning) return;

    html5QrCode = new Html5Qrcode("qr-reader");

    try {
        await html5QrCode.start(
            { facingMode: "environment" }, // back camera on phones
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            onScanSuccess,
            onScanFailure
        );

        isScanning = true;
        startBtn.style.display = 'none';
        stopBtn.style.display = 'inline-block';
        setStatus('idle', '📷', 'Ready to scan', 'Point the camera at a QR code');

    } catch (err) {
        setStatus('invalid', '⚠️',
            'Camera error',
            'Could not access camera. Check permissions.'
        );
        console.error(err);
    }
}

async function stopScanner() {
    if (!isScanning || !html5QrCode) return;

    await html5QrCode.stop();
    html5QrCode.clear();
    isScanning = false;
    startBtn.style.display = 'inline-block';
    stopBtn.style.display = 'none';
    setStatus('idle', '⏸️', 'Scanner stopped', 'Click Start to scan again');
}

// Wire up the buttons
document.addEventListener('DOMContentLoaded', () => {
    startBtn.addEventListener('click', startScanner);
    stopBtn.addEventListener('click', stopScanner);
});
