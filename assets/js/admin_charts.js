// Data untuk grafik adalah contoh (hardcoded).
// Nantinya, ini bisa diisi dengan data dinamis dari database menggunakan PHP.

// 1. Area Chart (Pendapatan)
var ctxArea = document.getElementById("myAreaChart");
if (ctxArea) {
    var myAreaChart = new Chart(ctxArea, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul"],
            datasets: [{
                label: "Pendapatan",
                lineTension: 0.3,
                backgroundColor: "rgba(0, 23, 156, 0.05)",
                borderColor: "rgba(0, 23, 156, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(0, 23, 156, 1)",
                pointBorderColor: "rgba(0, 23, 156, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(0, 23, 156, 1)",
                pointHoverBorderColor: "rgba(0, 23, 156, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: [0, 1000000, 500000, 1500000, 1000000, 2000000, 2500000],
            }],
        },
        options: {
            maintainAspectRatio: false,
            // Opsi lain untuk kustomisasi
        }
    });
}


// 2. Pie Chart (Distribusi Paket)
var ctxPie = document.getElementById("myPieChart");
if (ctxPie) {
    var myPieChart = new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ["Pemula Cerdas (Basic)", "Investor Andal (Premium)", "Sultan Finansial (Ultimate)"],
            datasets: [{
                data: [55, 30, 15], // persentase contoh
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'], // Hijau, Kuning, Merah
                hoverBackgroundColor: ['#17a673', '#d4a123', '#c43326'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            cutoutPercentage: 80,
        },
    });
}