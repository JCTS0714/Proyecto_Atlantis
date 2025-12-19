/* DevTools: Autollenar modales (solo usuario dev) */
(function () {
  if (!window.__DEVTOOLS__ || !window.__DEVTOOLS__.enabled) return;
  if (typeof window.jQuery === 'undefined') return;

  var $ = window.jQuery;

  function randInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  function choice(arr) {
    return arr[randInt(0, arr.length - 1)];
  }

  function randomDigits(len) {
    var s = '';
    for (var i = 0; i < len; i++) s += String(randInt(0, 9));
    return s;
  }

  function randomWord(minLen, maxLen) {
    var letters = 'abcdefghijklmnopqrstuvwxyz';
    var len = randInt(minLen, maxLen);
    var out = '';
    for (var i = 0; i < len; i++) out += letters[randInt(0, letters.length - 1)];
    return out;
  }

  function randomName() {
    var first = ['Carlos', 'Juan', 'Luis', 'Ana', 'Maria', 'Rosa', 'Pedro', 'Jose', 'Lucia', 'Sofia'];
    var last = ['Perez', 'Gomez', 'Rodriguez', 'Sanchez', 'Torres', 'Vargas', 'Flores', 'Castro', 'Diaz', 'Ramirez'];
    return choice(first) + ' ' + choice(last);
  }

  function randomCompany() {
    var parts = ['Atlantis', 'Soluciones', 'Grupo', 'Servicios', 'Comercial', 'Innova', 'Tech', 'Peru', 'Andes', 'Pacifico'];
    return choice(parts) + ' ' + choice(parts) + ' ' + randInt(1, 999);
  }

  function randomEmail() {
    var user = (randomWord(5, 8) + '.' + randomWord(4, 7) + randInt(1, 99)).toLowerCase();
    var domain = choice(['example.com', 'mail.com', 'empresa.pe', 'atlantis.local']);
    return user + '@' + domain;
  }

  function todayISO() {
    var d = new Date();
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    var dd = String(d.getDate()).padStart(2, '0');
    return d.getFullYear() + '-' + mm + '-' + dd;
  }

  function fillTextLike($el) {
    var id = ($el.attr('id') || '').toLowerCase();
    var name = ($el.attr('name') || '').toLowerCase();
    var key = id + ' ' + name;

    if ($el.attr('type') === 'email' || key.indexOf('email') >= 0 || key.indexOf('correo') >= 0) {
      $el.val(randomEmail());
      return;
    }

    if (key.indexOf('telefono') >= 0 || key.indexOf('tlf') >= 0 || key.indexOf('cel') >= 0 || $el.attr('type') === 'tel') {
      $el.val('9' + randomDigits(8));
      return;
    }

    if (key.indexOf('ruc') >= 0) {
      $el.val('20' + randomDigits(9));
      return;
    }

    if (key.indexOf('usuario') >= 0 || key.indexOf('user') >= 0) {
      $el.val((randomWord(5, 8) + randInt(1, 99)).toLowerCase());
      return;
    }

    if (key.indexOf('contras') >= 0 || $el.attr('type') === 'password') {
      $el.val('Dev' + randomDigits(6) + 'A');
      return;
    }

    if (key.indexOf('empresa') >= 0 || key.indexOf('comerc') >= 0) {
      $el.val(randomCompany());
      return;
    }

    if (key.indexOf('nombre') >= 0 || key.indexOf('titular') >= 0) {
      $el.val(randomName());
      return;
    }

    // fallback
    $el.val('Dev ' + randomWord(6, 10) + ' ' + randInt(1, 999));
  }

  function fillDate($el) {
    $el.val(todayISO());
  }

  function fillTextarea($el) {
    $el.val('Generado por DevTools ' + new Date().toISOString());
  }

  function fillSelect($el) {
    var opts = $el.find('option').filter(function () {
      var v = $(this).attr('value');
      return typeof v !== 'undefined' && String(v).trim() !== '';
    });
    if (opts.length) {
      var $opt = $(opts.get(randInt(0, opts.length - 1)));
      $el.val($opt.attr('value')).trigger('change');
    }
  }

  function parseSelect2Results(resp) {
    if (!resp) return [];
    if (resp.results && Array.isArray(resp.results)) return resp.results;
    if (Array.isArray(resp)) {
      return resp.map(function (it) {
        if (!it) return null;
        var id = (it.id != null) ? it.id : (it.value != null ? it.value : null);
        var text = it.text || it.label || it.nombre || it.empresa || (id != null ? String(id) : null);
        return (id != null && text != null) ? { id: id, text: text } : null;
      }).filter(Boolean);
    }
    return [];
  }

  function fetchSelect2Option($sel) {
    return new Promise(function (resolve, reject) {
      var s2 = $sel.data('select2');
      if (!s2 || !s2.options || !s2.options.options || !s2.options.options.ajax) {
        resolve(null);
        return;
      }

      var ajax = s2.options.options.ajax;
      var url = ajax.url;
      if (typeof url === 'function') {
        try { url = url(); } catch (e) { /* ignore */ }
      }
      if (!url) {
        resolve(null);
        return;
      }

      var term = choice(['a', 'e', 'i', 'o', 'u', 'an', 'ca', 'pe', 'so']);
      var dataObj = { term: term };
      if (typeof ajax.data === 'function') {
        try { dataObj = ajax.data({ term: term }); } catch (e) { dataObj = { term: term }; }
      }

      // Fallback: algunos endpoints usan q
      if (dataObj && typeof dataObj === 'object') {
        if (!('term' in dataObj) && !('q' in dataObj)) {
          dataObj.term = term;
        }
      }

      $.ajax({
        url: url,
        method: (ajax.type || 'GET'),
        dataType: 'json',
        data: dataObj,
        timeout: 8000
      }).done(function (resp) {
        var results = parseSelect2Results(resp);
        if (!results.length && dataObj && 'term' in dataObj) {
          // Segundo intento usando q
          $.ajax({
            url: url,
            method: (ajax.type || 'GET'),
            dataType: 'json',
            data: { q: dataObj.term },
            timeout: 8000
          }).done(function (resp2) {
            var r2 = parseSelect2Results(resp2);
            resolve(r2.length ? choice(r2) : null);
          }).fail(function () { resolve(null); });
          return;
        }
        resolve(results.length ? choice(results) : null);
      }).fail(function (xhr) {
        reject(xhr);
      });
    });
  }

  function setSelect2Value($sel, item) {
    if (!item) return;
    try {
      var opt = new Option(item.text, item.id, true, true);
      $sel.append(opt).trigger('change');
    } catch (e) {
      // ignore
    }
  }

  async function fillModal($modal) {
    // Inputs
    $modal.find('input').each(function () {
      var $el = $(this);
      var type = ($el.attr('type') || 'text').toLowerCase();
      if ($el.is(':disabled') || $el.is('[readonly]')) return;
      if (type === 'hidden') return;
      // Evitar llenar inputs ocultos por CSS (ej: inputs legacy ocultos cuando se usa Select2)
      if (!$el.is(':visible')) return;
      if (type === 'checkbox') {
        $el.prop('checked', Math.random() > 0.5).trigger('change');
        return;
      }
      if (type === 'radio') {
        return;
      }
      if (type === 'date') {
        fillDate($el);
        return;
      }
      fillTextLike($el);
    });

    // Textareas
    $modal.find('textarea').each(function () {
      var $el = $(this);
      if ($el.is(':disabled') || $el.is('[readonly]')) return;
      fillTextarea($el);
    });

    // Selects (incluye select2)
    var selects = $modal.find('select');
    for (var i = 0; i < selects.length; i++) {
      var $sel = $(selects[i]);
      if ($sel.is(':disabled')) continue;

      // Select2 AJAX
      if ($sel.data('select2')) {
        try {
          var item = await fetchSelect2Option($sel);
          if (item) {
            setSelect2Value($sel, item);
            // Incidencias (crear/editar): asegurar hidden idClienteSeleccionado + texto legacy
            var $hiddenIdCliente = $modal.find('input[type="hidden"]#idClienteSeleccionado, input[type="hidden"][name="idClienteSeleccionado"]').first();
            if ($hiddenIdCliente.length) {
              $hiddenIdCliente.val(item.id);
            }

            var $hiddenEditarIdCliente = $modal.find('input[type="hidden"]#editarIdClienteSeleccionado, input[type="hidden"][name="editarIdClienteSeleccionado"]').first();
            if ($hiddenEditarIdCliente.length) {
              $hiddenEditarIdCliente.val(item.id);
            }

            // Compat: input de texto oculto que algunos validadores leen
            var $txtNuevoNombreCliente = $modal.find('input#nuevoNombreCliente, input[name="nuevoNombreCliente"]').first();
            if ($txtNuevoNombreCliente.length) {
              $txtNuevoNombreCliente.val(item.text);
            }

            var $txtEditarNombreCliente = $modal.find('input#editarNombreCliente, input[name="editarNombreCliente"]').first();
            if ($txtEditarNombreCliente.length) {
              $txtEditarNombreCliente.val(item.text);
            }

            // Heurística general previa (otros modales)
            var $hiddenCliente = $modal.find('input[type="hidden"][id*="IdClienteSeleccionado"], input[type="hidden"][name*="IdClienteSeleccionado"]').first();
            if ($hiddenCliente.length && !$hiddenCliente.val()) {
              $hiddenCliente.val(item.id);
            }
            continue;
          }
        } catch (e) {
          // ignore
        }
      }

      fillSelect($sel);
    }

    // Prioridades comunes
    var $prio = $modal.find('select[id*="Prioridad"], select[name*="Prioridad"], select[id*="prioridad"], select[name*="prioridad"]');
    if ($prio.length) {
      $prio.each(function () {
        var $s = $(this);
        if ($s.val()) return;
        var candidates = $s.find('option').map(function () { return $(this).attr('value'); }).get().filter(Boolean);
        if (candidates.length) $s.val(choice(candidates)).trigger('change');
      });
    }
  }

  function ensureButton($modal) {
    var $footer = $modal.find('.modal-footer').first();
    if (!$footer.length) return;

    if ($footer.find('[data-devtools-autofill="1"]').length) return;

    var $btn = $('<button type="button" class="btn btn-default" data-devtools-autofill="1">Autollenar</button>');
    $btn.on('click', function () {
      fillModal($modal);
    });

    // Insertar al inicio del footer para no desplazar el botón submit
    $footer.prepend($btn);
  }

  // Hook: cada vez que se muestre un modal, inyectar botón
  $(document).on('shown.bs.modal', '.modal', function () {
    ensureButton($(this));
  });

  // También para modales ya visibles al cargar
  $('.modal.in').each(function () { ensureButton($(this)); });
})();
