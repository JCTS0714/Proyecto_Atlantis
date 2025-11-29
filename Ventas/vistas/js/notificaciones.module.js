/**
 * Módulo de notificaciones: responsable de polling, fetch y marcado en lote.
 * Exponer API mínima: init(options), start(), stop(), registerRenderer(fn)
 */
(function(window, $) {
  var POLL_INTERVAL = 30 * 1000; // 30s por defecto
  var COOLDOWN_MINUTES = 30; // default cooldown for repeated notifications (minutes)
  var pollTimer = null;
  var notifiedToday = new Set();
  var renderer = null;

  // Persistido en localStorage para evitar repetir la misma notificación muy seguido
  var STORAGE_KEY = 'notificaciones_timestamps';

  function loadLocalTimestamps() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (!raw) return {};
      return JSON.parse(raw);
    } catch (e) {
      console.error('Error parseando timestamps de localStorage', e);
      return {};
    }
  }

  function saveLocalTimestamps(obj) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(obj));
    } catch (e) {
      console.error('Error guardando timestamps en localStorage', e);
    }
  }

  function setLocalTimestamp(id, ts) {
    var map = loadLocalTimestamps();
    map[id] = ts;
    saveLocalTimestamps(map);
  }

  function getLocalTimestamp(id) {
    var map = loadLocalTimestamps();
    return map && map[id] ? new Date(map[id]).getTime() : null;
  }

  function getUsuarioId() {
    var el = document.getElementById('usuario_id');
    return el ? el.value : null;
  }

  function fetchNotificaciones() {
    var usuarioId = getUsuarioId();
    if (!usuarioId) return;
    $.ajax({
      url: 'ajax/calendario.ajax.php',
      type: 'POST',
      data: { accion: 'obtener_para_notificar', usuario_id: usuarioId },
      dataType: 'json'
    }).done(function(response) {
      if (response && Array.isArray(response.eventos)) {
        // Filtrar con cooldown para evitar repetir notificaciones muy seguido
        var eventos = response.eventos || [];
        var cooldownMs = (COOLDOWN_MINUTES || 0) * 60 * 1000;
        var now = Date.now();
        var eventosParaMostrar = [];

        eventos.forEach(function(ev) {
          try {
            var id = parseInt(ev.id, 10);
            // Obtener timestamp del servidor si existe
            var serverTs = null;
            if (ev.ultima_notificacion) {
              var parsed = Date.parse(ev.ultima_notificacion);
              if (!isNaN(parsed)) serverTs = parsed;
            }
            // Obtener timestamp local (persistido)
            var localTs = getLocalTimestamp(id);

            var isFirstTime = (!serverTs && !localTs);
            var lastTs = Math.max(serverTs || 0, localTs || 0);

            if (isFirstTime) {
              // Primera vez: dejamos que salga
              eventosParaMostrar.push(ev);
            } else {
              // Repetida: sólo mostrar si pasó el cooldown
              if (now - lastTs >= cooldownMs) {
                eventosParaMostrar.push(ev);
              }
            }
          } catch (e) {
            // En caso de fallo en parsing, dejar que se muestre para no perder notificación
            eventosParaMostrar.push(ev);
          }
        });

        if (typeof renderer === 'function') {
          try { renderer(eventosParaMostrar); } catch(e) { console.error('Renderer error', e); }
        }

        // Marcar en lote las que hemos mostrado para actualizar timestamps locales y servidor
        marcarVistasEnLote(eventosParaMostrar);
      }
    }).fail(function(xhr, status, err) {
      console.error('Error fetchNotificaciones', status, err);
    });
  }

  function marcarVistasEnLote(eventos) {
    if (!Array.isArray(eventos) || eventos.length === 0) return;
    // Formato SQL compatible 'YYYY-MM-DD HH:MM:SS'
    var ahoraIso = new Date().toISOString().slice(0,19).replace('T',' ');
    var ids = [];
    eventos.forEach(function(ev) {
      var id = parseInt(ev.id, 10);
      if (id && !notifiedToday.has(id)) {
        notifiedToday.add(id);
        ids.push(id);
        // actualizar timestamp local inmediato
        try { setLocalTimestamp(id, ahoraIso); } catch (e) { console.error('setLocalTimestamp error', e); }
      }
    });
    if (ids.length === 0) return;
    $.ajax({
      url: 'ajax/calendario.ajax.php',
      type: 'POST',
      data: { accion: 'marcar_notificaciones_vistas', ids: JSON.stringify(ids), fecha: ahoraIso },
      dataType: 'json'
    }).done(function(resp){
      // console.log('marcarVistasEnLote ok', resp);
    }).fail(function(xhr, status, err){
      console.error('Error marcarVistasEnLote', status, err);
    });
  }

  function start() {
    if (pollTimer) return;
    fetchNotificaciones();
    pollTimer = setInterval(fetchNotificaciones, POLL_INTERVAL);
  }

  function stop() {
    if (!pollTimer) return;
    clearInterval(pollTimer);
    pollTimer = null;
  }

  // Visibility API
  document.addEventListener('visibilitychange', function() {
    if (document.hidden) stop(); else start();
  });

  // API pública
  window.NotificationsModule = {
    init: function(opts) {
      if (opts) {
        if (opts.pollInterval) POLL_INTERVAL = opts.pollInterval;
        if (typeof opts.cooldownMinutes === 'number') COOLDOWN_MINUTES = opts.cooldownMinutes;
        if (opts.storageKey) STORAGE_KEY = opts.storageKey;
      }
    },
    start: start,
    stop: stop,
    registerRenderer: function(fn) { renderer = fn; }
  };

})(window, jQuery);
