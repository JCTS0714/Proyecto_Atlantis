/**
 * THEME TOGGLE - ATLANTIS CRM
 * Control de modo oscuro/claro con persistencia
 * @version 1.1
 * @date 2025-12-03
 */

(function() {
  'use strict';

  // ========================================
  // CONFIGURACIÓN
  // ========================================
  const STORAGE_KEY = 'atlantis_theme';
  const DARK_THEME = 'dark';
  const LIGHT_THEME = 'light';

  // ========================================
  // INICIALIZACIÓN
  // ========================================
  function init() {
    // Cargar tema guardado o usar modo claro por defecto
    const savedTheme = localStorage.getItem(STORAGE_KEY);
    
    if (savedTheme) {
      // Si hay tema guardado, usarlo
      setTheme(savedTheme);
    } else {
      // Por defecto: modo claro
      setTheme(LIGHT_THEME);
    }
  }

  // ========================================
  // CAMBIAR TEMA
  // ========================================
  function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(STORAGE_KEY, theme);
    
    // Actualizar icono del botón en el header
    const icon = document.getElementById('themeIcon');
    if (icon) {
      if (theme === DARK_THEME) {
        icon.className = 'fa fa-sun-o';
      } else {
        icon.className = 'fa fa-moon-o';
      }
    }
    
    // Actualizar title del botón
    const btn = document.getElementById('btnThemeToggle');
    if (btn) {
      btn.title = theme === DARK_THEME ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
    }

    // Emitir evento personalizado
    document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: theme } }));
  }

  // ========================================
  // TOGGLE TEMA
  // ========================================
  function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === DARK_THEME ? LIGHT_THEME : DARK_THEME;
    setTheme(newTheme);
  }

  // ========================================
  // API PÚBLICA
  // ========================================
  window.ThemeToggle = {
    setTheme: setTheme,
    toggleTheme: toggleTheme,
    getTheme: function() {
      return document.documentElement.getAttribute('data-theme') || LIGHT_THEME;
    },
    isDark: function() {
      return this.getTheme() === DARK_THEME;
    }
  };

  // ========================================
  // INICIAR CUANDO DOM ESTÉ LISTO
  // ========================================
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
