const ctx = document.getElementById('graficoEstoque');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: nomesEpis,
        datasets: [{
            label: 'Quantidade em estoque',
            data: quantidadesEpis,
            borderWidth: 1,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});

const pesquisa = document.getElementById('pesquisaEpi');

pesquisa.addEventListener('keyup', function () {
    const filtro = pesquisa.value.toLowerCase();
    const linhas = document.querySelectorAll('#tabelaEstoque tbody tr');

    linhas.forEach(function (linha) {
        const texto = linha.innerText.toLowerCase();
        linha.style.display = texto.includes(filtro) ? '' : 'none';
    });
});