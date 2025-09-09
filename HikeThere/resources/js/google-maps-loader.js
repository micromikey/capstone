// Central Google Maps loader: ensures single script injection and shared promise
(() => {
  function injectScript(libraries) {
    return new Promise((resolve, reject) => {
      if (window.google && window.google.maps) {
        resolve(window.google.maps);
        return;
      }
      const meta = document.querySelector('meta[name="google-maps-api-key"]');
      const apiKey = meta ? meta.content : '';
      if (!apiKey) {
        reject(new Error('Missing google-maps-api-key meta tag'));
        return;
      }
      const params = new URLSearchParams({ key: apiKey, libraries, v: 'weekly', callback: '__initGmaps' });
      const script = document.createElement('script');
      script.src = 'https://maps.googleapis.com/maps/api/js?' + params.toString();
      script.async = true;
      script.defer = true;
      script.onerror = () => reject(new Error('Failed loading Google Maps JS'));
      window.__initGmaps = () => { resolve(window.google.maps); };
      document.head.appendChild(script);
    });
  }

  window.loadGoogleMaps = function(options = {}) {
    const libs = options.libraries || 'places,geometry';
    if (window.google && window.google.maps) return Promise.resolve(window.google.maps);
    if (!window.__gmapsLoader) {
      window.__gmapsLoader = injectScript(libs).catch(err => { window.__gmapsLoader = null; throw err; });
    }
    return window.__gmapsLoader;
  };
})();

export {}; // ESM friendly
