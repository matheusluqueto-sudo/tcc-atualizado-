document.addEventListener("DOMContentLoaded", function () {
    const dados = window.dadosHome || {};
    const estoque = dados.estoque || [];
    const pedidos = dados.pedidos || [];

    const nomesEpis = estoque.map(function (item) { return item.nome_epi; });
    const quantidadesEpis = estoque.map(function (item) { return Number(item.quantidade_estoque) || 0; });

    const nomesPedidos = pedidos.map(function (item) { return item.status_solicitacao; });
    const quantidadesPedidos = pedidos.map(function (item) { return Number(item.total) || 0; });

    const graficoEstoque = document.getElementById("graficoEstoque");
    const graficoPedidos = document.getElementById("graficoPedidos");

    if (graficoEstoque) {
        new Chart(graficoEstoque, {
            type: "bar",
            data: {
                labels: nomesEpis,
                datasets: [{
                    label: "Quantidade",
                    data: quantidadesEpis,
                    backgroundColor: "#7015A8",
                    borderRadius: 7,
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
                                return " Quantidade: " + context.raw;
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
                        grid: { display: false },
                        ticks: {
                            font: { size: 10 }
                        }
                    }
                }
            }
        });
    }

    if (graficoPedidos) {
        new Chart(graficoPedidos, {
            type: "doughnut",
            data: {
                labels: nomesPedidos,
                datasets: [{
                    data: quantidadesPedidos,
                    backgroundColor: ["#f0c75e", "#7015A8", "#e63946", "#2e9d62"],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "65%",
                plugins: {
                    legend: {
                        position: "bottom",
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return " " + context.label + ": " + context.raw;
                            }
                        }
                    }
                }
            }
        });
    }
});