<script>
{{-- ============================================ --}}
{{-- Clock Functions - Функции часов --}}
{{-- ============================================ --}}

/**
 * Update the clock display in the taskbar
 * Shows current time and date in Russian format
 */
function updateClock() {
    var now = new Date();
    document.getElementById('clock-time').textContent = now.toLocaleTimeString('ru-RU', {hour: '2-digit', minute: '2-digit'});
    document.getElementById('clock-date').textContent = now.toLocaleDateString('ru-RU');
}
</script>