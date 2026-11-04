// The first theme is chosen by the inline script in the header partial, before CSS loads.
// This module only reacts to the toggle button and to system theme changes.
const storageKey = 'theme';
const themeToggle = document.querySelector('.theme-toggle');
const themeLabel = themeToggle?.querySelector('.theme-toggle__label') ?? null;
const colorSchemeQuery = window.matchMedia('(prefers-color-scheme: dark)');
const reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

function getCurrentTheme() {
    return document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
}

function getSavedTheme() {
    try {
        const theme = localStorage.getItem(storageKey);
        return theme === 'light' || theme === 'dark' ? theme : null;
    } catch (error) {
        return null;
    }
}

function saveTheme(theme) {
    try {
        localStorage.setItem(storageKey, theme);
    } catch (error) {
        // Storage can be blocked (private mode); the theme still applies to this page.
    }
}

// The label names the action, not the current state: "Dark" switches to dark.
function syncThemeToggle() {
    if (!themeToggle) {
        return;
    }

    const isDark = getCurrentTheme() === 'dark';
    themeToggle.setAttribute('aria-pressed', String(isDark));
    themeToggle.setAttribute('aria-label', isDark ? 'Switch to light theme' : 'Switch to dark theme');

    if (themeLabel) {
        themeLabel.textContent = isDark ? 'Light' : 'Dark';
    }
}

function applyTheme(theme) {
    document.documentElement.dataset.theme = theme;
    syncThemeToggle();
}

// Fade only where the browser supports View Transitions and the user hasn't asked for less motion;
// everywhere else the theme switches instantly, as before.
function setTheme(theme, remember = true) {
    if (remember) {
        saveTheme(theme);
    }

    if (typeof document.startViewTransition === 'function' && !reducedMotionQuery.matches) {
        document.startViewTransition(() => applyTheme(theme));
        return;
    }

    applyTheme(theme);
}

themeToggle?.addEventListener('click', () => {
    setTheme(getCurrentTheme() === 'dark' ? 'light' : 'dark');
});

// A saved choice wins over the system setting, so only follow the system while nothing is saved.
colorSchemeQuery.addEventListener('change', (event) => {
    if (getSavedTheme() !== null) {
        return;
    }

    setTheme(event.matches ? 'dark' : 'light', false);
});

syncThemeToggle();
