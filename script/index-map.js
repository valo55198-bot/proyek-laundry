(function() {
    var mapEl = document.getElementById('orderMap');
    if (!mapEl) return;

    var defaultLat = -6.3293, defaultLng = 108.3243;
    var map = L.map('orderMap', { scrollWheelZoom: false }).setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19
    }).addTo(map);

    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateCoords(lat, lng) {
        document.getElementById('cf_lat').value = lat.toFixed(7);
        document.getElementById('cf_lng').value = lng.toFixed(7);
    }

    // ─── REVERSE GEOCODE ─────────────────────────────────────────
    var _geocodeTimer = null;
    var _addressManuallyEdited = false;

    function reverseGeocode(lat, lng, force) {
        if (_addressManuallyEdited && !force) return;

        var input = document.getElementById('cf_alamat');
        if (input && !input.value) {
            input.placeholder = "Mencari alamat...";
        }

        // Debounce: tunda 0.6 detik
        clearTimeout(_geocodeTimer);
        _geocodeTimer = setTimeout(function() {
            if (input && !input.value) {
                input.value = "Mencari alamat...";
            }
            
            fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1', {
                headers: { 'Accept-Language': 'id' }
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (_addressManuallyEdited && !force) return;
                if (data && data.display_name) {
                    if (input) {
                        input.value = data.display_name;
                    }
                }
            })
            .catch(function() {
                if (input && input.value === "Mencari alamat...") {
                    input.value = "";
                    input.placeholder = "Masukkan alamat lengkap...";
                }
            });
        }, 600);
    }

    // Deteksi ketikan manual di kolom alamat
    var alamatInput = document.getElementById('cf_alamat');
    if (alamatInput) {
        alamatInput.addEventListener('input', function() {
            _addressManuallyEdited = true;
        });
    }

    // Click on map
    map.on('click', function(e) {
        _addressManuallyEdited = false; // Reset flag karena user sengaja pilih titik baru
        marker.setLatLng(e.latlng);
        updateCoords(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng, true);
    });

    // Drag marker
    marker.on('dragend', function() {
        var pos = marker.getLatLng();
        updateCoords(pos.lat, pos.lng);
        reverseGeocode(pos.lat, pos.lng, true);
    });

    // ─── ADDRESS SEARCH ───────────────────────────────────────────
    var searchInput = document.getElementById('mapSearchInput');
    var suggestBox  = document.getElementById('mapSearchSuggestions');
    var searchTimer = null;

    function showSuggestions(results) {
        suggestBox.innerHTML = '';
        if (!results.length) {
            suggestBox.innerHTML = '<div style="padding:10px 14px;color:#9ca3af;font-size:.83rem;">Tidak ditemukan hasil.</div>';
            suggestBox.style.display = 'block';
            return;
        }
        results.forEach(function(item) {
            var div = document.createElement('div');
            div.textContent = item.display_name;
            div.style.cssText = 'padding:10px 14px;cursor:pointer;font-size:.83rem;border-bottom:1px solid #f5f0f3;transition:background .15s;line-height:1.4;';
            div.onmouseenter = function() { this.style.background = '#fdf5f7'; };
            div.onmouseleave = function() { this.style.background = ''; };
            div.addEventListener('click', function() {
                var lat = parseFloat(item.lat);
                var lng = parseFloat(item.lon);
                map.setView([lat, lng], 17);
                marker.setLatLng([lat, lng]);
                updateCoords(lat, lng);
                document.getElementById('cf_alamat').value = item.display_name;
                searchInput.value = item.display_name;
                suggestBox.style.display = 'none';
            });
            suggestBox.appendChild(div);
        });
        suggestBox.style.display = 'block';
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var q = this.value.trim();
        if (q.length < 3) { suggestBox.style.display = 'none'; return; }
        searchTimer = setTimeout(function() {
            fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5&countrycodes=id', {
                headers: { 'Accept-Language': 'id' }
            })
            .then(function(r) { return r.json(); })
            .then(showSuggestions)
            .catch(function() {});
        }, 400);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#mapSearchInput') && !e.target.closest('#mapSearchSuggestions')) {
            suggestBox.style.display = 'none';
        }
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') suggestBox.style.display = 'none';
    });

    // ─── GPS BUTTON ───────────────────────────────────────────────
    var _watchId        = null;
    var _accuracyCircle = null;
    var _gpsTimeout     = null;

    function stopGPS(btn) {
        if (_watchId !== null) {
            navigator.geolocation.clearWatch(_watchId);
            _watchId = null;
        }
        clearTimeout(_gpsTimeout);
        _gpsTimeout = null;
        btn.innerHTML = '<i class="fas fa-crosshairs"></i> GPS';
        btn.disabled  = false;
    }

    function removeAccuracyCircle() {
        if (_accuracyCircle) {
            _accuracyCircle.remove();
            _accuracyCircle = null;
        }
    }

    document.getElementById('btnGeolocate').addEventListener('click', function() {
        var btn = this;

        if (!navigator.geolocation) {
            alert('Browser Anda tidak mendukung fitur geolokasi.');
            return;
        }

        // Klik lagi saat GPS aktif = stop
        if (_watchId !== null) {
            stopGPS(btn);
            removeAccuracyCircle();
            return;
        }

        // Reset flag manual edit agar GPS bisa update alamat
        _addressManuallyEdited = false;

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mencari...';
        btn.disabled  = false;

        var bestAccuracy = Infinity;
        var firstFix     = true;
        var stableCount  = 0;   // hitung berapa kali akurasi sudah bagus berturut-turut

        // Batas waktu maksimal 60 detik
        _gpsTimeout = setTimeout(function() {
            stopGPS(btn);
            console.log('GPS timeout — menggunakan posisi terbaik yang tersedia.');
        }, 60000);

        _watchId = navigator.geolocation.watchPosition(
            function(pos) {
                var lat = pos.coords.latitude;
                var lng = pos.coords.longitude;
                var acc = pos.coords.accuracy; // meter

                // Update tampilan akurasi di tombol
                btn.innerHTML = '<i class="fas fa-location-dot"></i> ~' + Math.round(acc) + 'm';

                // Pertama kali dapat sinyal: langsung pindah ke lokasi user
                if (firstFix) {
                    map.setView([lat, lng], 18);
                    firstFix = false;
                }

                // Selalu update marker & koordinat setiap ada sinyal baru
                marker.setLatLng([lat, lng]);
                updateCoords(lat, lng);

                // Gambar lingkaran radius akurasi
                removeAccuracyCircle();
                _accuracyCircle = L.circle([lat, lng], {
                    radius: acc,
                    color: '#c67a89',
                    fillColor: '#f2a0b8',
                    fillOpacity: 0.15,
                    weight: 1.5
                }).addTo(map);

                // Update alamat jika akurasi membaik (termasuk saat pertama kali mendapat sinyal)
                if (acc < bestAccuracy) {
                    bestAccuracy = acc;
                    reverseGeocode(lat, lng, true); // force = bypass manual-edit guard
                }

                // Hitung berapa update berturut-turut dengan akurasi bagus
                if (acc <= 15) {
                    stableCount++;
                } else {
                    stableCount = 0; // reset jika melebar lagi (misal pindah indoor)
                }

                // Stop otomatis jika:
                // - Sudah sangat akurat (≤ 10m), ATAU
                // - Sudah stabil 3x berturut-turut di bawah 15m
                if (acc <= 10 || stableCount >= 3) {
                    removeAccuracyCircle(); // hapus lingkaran, posisi sudah terkunci
                    stopGPS(btn);
                    btn.innerHTML = '<i class="fas fa-circle-check"></i> GPS ✓';
                }
            },
            function(err) {
                clearTimeout(_gpsTimeout);
                _watchId = null;

                var msg = 'Gagal mendapatkan lokasi.';
                if (err.code === 1) msg = 'Akses lokasi ditolak. Izinkan di pengaturan browser.';
                else if (err.code === 2) msg = 'Lokasi tidak tersedia. Pastikan GPS aktif.';
                else if (err.code === 3) msg = 'Timeout. Coba lagi di tempat lebih terbuka.';

                if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost') {
                    msg += '\n\nCatatan: Fitur GPS memerlukan koneksi HTTPS yang aman.';
                }

                alert(msg);
                btn.innerHTML = '<i class="fas fa-crosshairs"></i> GPS';
                btn.disabled  = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,   // naikkan dari 10s → 15s, lebih toleran di kondisi sinyal lemah
                maximumAge: 0     // selalu minta posisi segar, jangan pakai cache
            }
        );
    });

    // Fix map sizing
    var resizeObserver = new ResizeObserver(function() { map.invalidateSize(); });
    resizeObserver.observe(mapEl);
})();