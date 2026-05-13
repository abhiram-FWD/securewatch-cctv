document.addEventListener('DOMContentLoaded', function() {
    // Request notification permission on load
    if ("Notification" in window) {
        if (Notification.permission !== "granted" && Notification.permission !== "denied") {
            Notification.requestPermission();
        }
    }

    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

    function playBeep(isEmergency) {
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }

        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);

        if (isEmergency) {
            oscillator.type = 'sawtooth';
            oscillator.frequency.setValueAtTime(800, audioCtx.currentTime); // higher pitch
            oscillator.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime + 0.2);
            gainNode.gain.setValueAtTime(1, audioCtx.currentTime); // Louder
            
            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 0.5);
            
            // Repeat fast for emergency
            setTimeout(() => {
                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.type = 'sawtooth';
                osc2.frequency.setValueAtTime(1200, audioCtx.currentTime);
                osc2.frequency.exponentialRampToValueAtTime(800, audioCtx.currentTime + 0.2);
                gain2.gain.setValueAtTime(1, audioCtx.currentTime);
                osc2.start();
                osc2.stop(audioCtx.currentTime + 0.5);
            }, 500);

        } else {
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(440, audioCtx.currentTime); // A4
            gainNode.gain.setValueAtTime(0.5, audioCtx.currentTime);
            
            oscillator.start();
            oscillator.stop(audioCtx.currentTime + 0.3);
            
            setTimeout(() => {
                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
                gain2.gain.setValueAtTime(0.5, audioCtx.currentTime);
                osc2.start();
                osc2.stop(audioCtx.currentTime + 0.3);
            }, 400);
        }
    }

    function checkNewAlerts() {
        fetch('/api/alerts/new', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                playBeep(data.has_emergency);

                // Update badge count
                const badge = document.getElementById('alert-badge-count');
                if (badge) {
                    let current = parseInt(badge.innerText) || 0;
                    badge.innerText = current + data.count;
                }

                // Show Notification
                if ("Notification" in window && Notification.permission === "granted") {
                    let title = data.has_emergency ? "🚨 EMERGENCY ALERT!" : "New Alert Received";
                    let body = data.alerts.length > 0 ? data.alerts[0].description : "A new alert requires your attention.";
                    new Notification(title, { body: body, icon: '/favicon.ico' });
                }

                // Flashing red effect for emergency
                if (data.has_emergency) {
                    document.body.style.transition = "background-color 0.5s";
                    document.body.style.backgroundColor = "#ffcccc";
                    setTimeout(() => {
                        document.body.style.backgroundColor = "";
                    }, 1000);
                }
            }
        })
        .catch(error => console.error('Error fetching alerts:', error));
    }

    // Allow user interaction to unlock audio context
    document.body.addEventListener('click', function() {
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }, { once: true });

    // Poll every 10 seconds
    setInterval(checkNewAlerts, 10000);
});
