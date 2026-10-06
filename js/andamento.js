document.addEventListener("DOMContentLoaded", function () {
    const pendentes = window.dadosAndamento.pendentes;
    const emAndamento = window.dadosAndamento.emAndamento;
    const total = pendentes + emAndamento;

    const graficoRosca = document.getElementById("graficoRosca");
    const graficoBarras = document.getElementById("graficoBarras");

    if (graficoRosca) {
        new Chart(graficoRosca, {
            type: "doughnut",
            data: {
                labels: ["Pendentes", "Em andamento"],
                datasets: [{
                    data: [pendentes, emAndamento],
                    backgroundColor: ["#f0c75e", "#7015A8"],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "68%",
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            usePointStyle: true,
                            padding: 18,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const valor = context.raw;
                                const porcentagem = total > 0 ? ((valor / total) * 100).toFixed(1) : 0;
                                return " " + context.label + ": " + valor + " (" + porcentagem + "%)";
                            }
                        }
                    }
                }
            }
        });
    }

    if (graficoBarras) {
        new Chart(graficoBarras, {
            type: "bar",
            data: {
                labels: ["Pendentes", "Em andamento"],
                datasets: [{
                    label: "Solicitações",
                    data: [pendentes, emAndamento],
                    backgroundColor: ["#f0c75e", "#7015A8"],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return " Solicitações: " + context.raw;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: "rgba(0,0,0,.06)" }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    const filtro = document.getElementById("filtroAndamento");
    const processos = document.querySelectorAll(".andamento-item");

    if (filtro) {
        filtro.addEventListener("change", function () {
            const selecionado = this.value;
            processos.forEach(function (processo) {
                const status = processo.getAttribute("data-status");
                processo.style.display = selecionado === "todos" || status === selecionado ? "grid" : "none";
            });
        });
    }
});