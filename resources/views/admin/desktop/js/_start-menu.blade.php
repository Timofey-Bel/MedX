<script>
{{-- ============================================ --}}
{{-- Start Menu Functions - Windows 10 Style --}}
{{-- ============================================ --}}

/**
 * Toggle the Start Menu visibility
 * Prevents event bubbling to avoid immediate closure
 * 
 * @param {Event} event - Click event object
 */
function toggleStartMenu(event) {
    if (event) {
        event.stopPropagation();
    }
    var menu = document.getElementById('start-menu-modal');
    menu.classList.toggle('open');
}

/**
 * Close the Start Menu
 * Called when clicking outside the menu
 */
function closeStartMenu() {
    var menu = document.getElementById('start-menu-modal');
    menu.classList.remove('open');
}
</script>