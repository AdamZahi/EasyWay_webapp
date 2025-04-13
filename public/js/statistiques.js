window.onload = function() {
    if (document.getElementById("evolutionChart")) {
        console.log("Données évolution des réclamations :", evolutionData);

        const options = {
            chart: {
                type: "line",
                height: 350
            },
            series: [{
                name: "Réclamations",
                data: Object.values(evolutionData)
            }],
            xaxis: {
                categories: Object.keys(evolutionData),
                title: { text: "Mois" }
            },
            yaxis: {
                title: { text: "Nombre de réclamations" }
            }
        };

        new ApexCharts(document.getElementById("evolutionChart"), options).render();
    } else {
        console.error("Élément evolutionChart introuvable !");
    }
};
