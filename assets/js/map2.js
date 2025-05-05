function getPlaceNameFromOpenCage(lat, lng, callback) {
    const apiKey = 'ff72fda5ad874ffca77411b17e2e0b30';
    const url = `https://api.opencagedata.com/geocode/v1/json?q=${lat}+${lng}&key=${apiKey}&language=fr`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.results && data.results.length > 0) {
                const result = data.results[0];
                const countryCode = result.components.country_code;
                
                if (countryCode !== 'tn') {
                    showNotification("Veuillez placer le marqueur uniquement en Tunisie.");
                    return;
                }

                const displayName = result.formatted;
                callback(displayName);
            } else {
                showNotification("Lieu introuvable.");
            }
        })
        .catch(error => {
            console.error('Erreur lors de la géolocalisation :', error);
            showNotification("Erreur lors de la récupération du lieu.");
        });
}

document.addEventListener("DOMContentLoaded", function () {
    const map = L.map('map', {
        center: [36.776, 10.195],
        zoom: 10,
        maxZoom: 18,
        minZoom: 3,
        maxBounds: [
            [33.0, 7.0],
            [40.0, 13.0]
        ],
        zoomControl: true,
        scrollWheelZoom: true,
        doubleClickZoom: true,
        boxZoom: true,
        keyboard: true,
        dragging: true,
        touchZoom: true,
        tap: true,
        preferCanvas: false,
        crs: L.CRS.EPSG3857,
        renderer: L.canvas()
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const customIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/10029/10029528.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
        shadowSize: [41, 41]
    });

    const markers = [];

    // Function to display a brief custom notification
    function showNotification(message, isRose = false) {
        const notification = document.createElement('div');
        notification.className = 'custom-notification';
        if (isRose) {
            notification.classList.add('rose'); // Optional for styling different notifications
        }
        notification.innerHTML = message;
        document.body.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.classList.add('fade-out');
        }, 3000);

        setTimeout(() => {
            notification.remove();
        }, 3500);
    }

    map.on('click', function (e) {
        if (map.getZoom() !== map.getMaxZoom()) {
            showNotification("Veuillez zoomer complètement pour placer un marqueur.");
            return;
        }
    
        if (markers.length >= 2) {
            showNotification("Vous ne pouvez placer que 2 marqueurs.");
            return;
        }
    
        getPlaceNameFromOpenCage(e.latlng.lat, e.latlng.lng, function (placeName) {
            // Auto-fill input fields if available
            const departInput = document.querySelector('#ligne_depart');
            const arretInput = document.querySelector('#ligne_arret');
            if (departInput && arretInput) {
                if (markers.length === 0) departInput.value = placeName;
                else if (markers.length === 1) arretInput.value = placeName;
            }
    
            const marker = L.marker([e.latlng.lat, e.latlng.lng], {
                icon: customIcon,
                draggable: true
            }).addTo(map);
    
            marker.bindPopup(
                `<b>${placeName}</b><br><i>Cliquez sur le marqueur pour le supprimer</i>`
            ).openPopup();
    
            marker.on('click', function () {
                map.removeLayer(marker);
                const index = markers.indexOf(marker);
                if (index !== -1) markers.splice(index, 1);
            });
    
            markers.push(marker);
        });
    });
    
});
