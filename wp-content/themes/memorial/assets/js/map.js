/* מפת מקומות ההנצחה: Leaflet + OpenStreetMap */
(function () {
  'use strict';
  if (!window.L) { return; }
  var L = window.L;

  var pinIcon = function (approx) {
    return L.divIcon({ className: '', html: '<div class="memorial-pin' + (approx ? ' memorial-pin--approx' : '') + '"></div>', iconSize: [34, 34], iconAnchor: [17, 34], popupAnchor: [0, -30] });
  };
  var tiles = function (map) {
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
    }).addTo(map);
  };
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; };

  // מפה של מקום בודד
  var one = document.getElementById('bench-map');
  if (one) {
    var lat = parseFloat(one.getAttribute('data-lat')), lng = parseFloat(one.getAttribute('data-lng'));
    var approx = one.getAttribute('data-approx') === '1';
    var m = L.map(one, { scrollWheelZoom: false }).setView([lat, lng], approx ? 12 : 15);
    tiles(m);
    if (approx) {
      L.circle([lat, lng], { radius: 2500, color: '#1f2b45', weight: 2, dashArray: '6 6', fillColor: '#f2b632', fillOpacity: .25 }).addTo(m);
    } else {
      L.marker([lat, lng], { icon: pinIcon(false), title: one.getAttribute('data-title') || '' }).addTo(m);
    }
  }

  // מפה ראשית
  var big = document.getElementById('benches-map');
  if (big && window.MEMORIAL_MAP) {
    var pts = window.MEMORIAL_MAP.points || [];
    // מרכז ברירת מחדל: ישראל; המפה מתכווננת אוטומטית לנקודות
    var map = L.map(big, { scrollWheelZoom: false }).setView([31.5, 34.9], 7);
    tiles(map);
    var bounds = [];
    pts.forEach(function (p) {
      var html = (p.img ? '<img src="' + esc(p.img) + '" alt="">' : '') +
        '<strong>' + esc(p.title) + '</strong>' + (p.region ? '<br><small>' + esc(p.region) + '</small>' : '') +
        (p.approx ? '<br><small>מיקום משוער</small>' : '') + '<br><a href="' + esc(p.url) + '">לעמוד המקום</a>';
      if (p.approx) {
        L.circle([p.lat, p.lng], { radius: 2500, color: '#1f2b45', weight: 2, dashArray: '6 6', fillColor: '#f2b632', fillOpacity: .25 }).addTo(map);
      }
      L.marker([p.lat, p.lng], { icon: pinIcon(p.approx), title: p.title }).addTo(map).bindPopup(html, { minWidth: 180 });
      bounds.push([p.lat, p.lng]);
    });
    if (bounds.length > 1) { map.fitBounds(bounds, { padding: [50, 50], maxZoom: 11 }); }
    else if (bounds.length === 1) { map.setView(bounds[0], 11); }
    big.addEventListener('focusin', function () { map.scrollWheelZoom.enable(); });
    big.addEventListener('mouseleave', function () { map.scrollWheelZoom.disable(); });
    big.addEventListener('click', function () { map.scrollWheelZoom.enable(); });
  }
})();
