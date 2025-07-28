// This file contains JavaScript to generate a notification beep sound
// Since we can't include actual audio files, we'll generate a beep programmatically

export function createNotificationSound() {
    // Create a short beep sound using Web Audio API
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.setValueAtTime(800, audioContext.currentTime); // 800Hz tone
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime); // 30% volume
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2); // Fade out
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.2); // 200ms duration
}

// For browsers that don't support Web Audio API, use a data URL audio
export function playNotificationBeep() {
    try {
        createNotificationSound();
    } catch (error) {
        // Fallback: play a system beep if available
        if ('Audio' in window) {
            // Create a simple beep using data URL
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmmbQARJm9vwzn0vBSN+yO/eizEIFlm16eJ3VgsJVKnn8bZpHgU4k9fw0oIxCCJzwe+8iDMLGV254OeOTgwJR5zd8sFdJQNHltjwznoUDFGp4+7ErWIcBzei3/LvdCUFO4DN8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmIcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmIcBziR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmIcBziR1/LMeSwFJHfH8N2QQAoTXbTp66hVFApGn+DyvmIcBziR1/LMeSwFJHfH8N2QQAoTXbTp66hVFApGn+DyvmIcBziR1/LMeSwFJHfH8N2QQAoTXbTp66hVFApGn+DyvmbL3');
            audio.volume = 0.3;
            audio.play().catch(() => {}); // Ignore errors
        }
    }
}
