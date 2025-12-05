(function(){
  const KEY = 'site-theme'; // 'dark' | 'light'
  function applyTheme(theme){
    document.documentElement.setAttribute('data-theme', theme);
  }
  function getStoredTheme(){
    try{ return localStorage.getItem(KEY); }catch(e){ return null; }
  }
  function storeTheme(theme){
    try{ localStorage.setItem(KEY, theme); }catch(e){}
  }

  const stored = getStoredTheme();
  if(stored){
    applyTheme(stored);
  } else if(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches){
    applyTheme('dark');
  } else {
    applyTheme('light');
  }

  // Exponer toggle
  window.toggleSiteTheme = function(next){
    const current = document.documentElement.getAttribute('data-theme') || 'light';
    const result = next || (current === 'dark' ? 'light' : 'dark');
    applyTheme(result);
    storeTheme(result);
    return result;
  };

  // SweetAlert2 mixin con customClass por defecto
  if(window.Swal){
    window.SwalTheme = Swal.mixin({
      customClass: {
        popup: 'swal2-custom-popup',
        title: 'swal2-custom-title',
        content: 'swal2-custom-content',
        confirmButton: 'swal2-custom-confirm',
        cancelButton: 'swal2-custom-cancel'
      },
      buttonsStyling: false
    });
  }
})();
