document.addEventListener('DOMContentLoaded', function() {
    // Fetch para obter as ocorrências para o gráfico 1
    fetch('/../views/relatorios/get_ocorrencias.php')
        .then(response => response.json())
        .then(data => {
            console.log('Dados do gráfico 1:', data); // Depurar os dados recebidos
            const labels = data.map(item => item.ocorrencia);
            const quantities = data.map(item => item.quantidade);

            const ctx = document.getElementById('ocorrenciasChart').getContext('2d');
            if (ctx) {
                const ocorrenciasChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ocorrências Tratadas',
                            data: quantities,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                console.error('Elemento com id "ocorrenciasChart" não encontrado.');
            }
        })
        .catch(error => console.error('Erro ao buscar dados:', error));

    // Fetch para obter as ocorrências não tratadas para o gráfico 2
    fetch('/../views/relatorios/get_ocorrenciasNaoTratadas.php')
        .then(response => response.json())
        .then(data => {
            console.log('Dados do gráfico 2:', data); // Depurar os dados recebidos
            const labels = data.map(item => item.ocorrencia);
            const quantities = data.map(item => item.quantidade);

            const ctx2 = document.getElementById('ocorrenciasChartNaoTratadas').getContext('2d');
            if (ctx2) {
                const ocorrenciasChartNaoTratadas = new Chart(ctx2, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Ocorrências Não Tratadas',
                            data: quantities,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                console.error('Elemento com id "ocorrenciasChartNaoTratadas" não encontrado.');
            }
        })
        .catch(error => console.error('Erro ao buscar dados:', error));

    // Fetch para obter a contagem total de ocorrências
    fetch('/../views/relatorios/get_total_ocorrencias.php')
        .then(response => response.json())
        .then(data => {
            console.log('Total de ocorrências:', data); // Depurar os dados recebidos
            const totalOcorrencias = data.total_ocorrencias;

            // Exemplo de como atualizar um elemento no DOM com o total de ocorrências
            const ocorrenciasElement = document.getElementById('totalOcorrencias');
            if (ocorrenciasElement) {
                ocorrenciasElement.textContent = `${totalOcorrencias}`;
            } else {
                console.error('Elemento com id "totalOcorrencias" não encontrado.');
            }
        })
        .catch(error => console.error('Erro ao buscar dados:', error));

    // Fetch para obter a contagem total de ocorrências sem tratativas
    fetch('/../views/relatorios/get_total_ocorrenciasSemTratar.php')
        .then(response => response.json())
        .then(data => {
            console.log('Total de ocorrências sem tratar:', data); // Depurar os dados recebidos
            const totalOcorrencias = data.total_ocorrencias;

            // Exemplo de como atualizar um elemento no DOM com o total de ocorrências
            const ocorrenciasElement = document.getElementById('totalOcorrenciasSemTratar');
            if (ocorrenciasElement) {
                ocorrenciasElement.textContent = `${totalOcorrencias}`;
            } else {
                console.error('Elemento com id "totalOcorrenciasSemTratar" não encontrado.');
            }
        })
        .catch(error => console.error('Erro ao buscar dados:', error));

    // Função para obter o valor total de ocorrências de evasão recuperado
    function getValorEvasaoRecuperado() {
        fetch('/../views/relatorios/get_valor_evasao_recuperado.php')
            .then(response => response.json())
            .then(data => {
                console.log('Valor de evasão recuperado:', data); // Adicione esta linha para depurar os dados recebidos

                if (data.success) {
                    // Atualize o DOM com o valor recuperado
                    const ocorrenciasElement = document.getElementById('ocorrenciasValorRecuperado');
                    if (ocorrenciasElement) {
                        ocorrenciasElement.textContent = `R$ ${data.valor_a_pagar}`;
                    } else {
                        console.error('Elemento com id "ocorrenciasValorRecuperado" não encontrado.');
                    }
                } else {
                    console.error('Erro: ', data.message);
                }
            })
            .catch(error => console.error('Erro ao buscar dados:', error));
    }

    // Chama a função para obter o valor de evasão recuperado
    getValorEvasaoRecuperado();

    // Função para obter o valor total de ocorrências de evasão devido
    function getValorEvasaoDevido() {
        fetch('/../views/relatorios/get_valor_evasao_devido.php')
            .then(response => response.json())
            .then(data => {
                console.log('Valor de evasão devido:', data); // Adicione esta linha para depurar os dados recebidos

                if (data.success) {
                    // Atualize o DOM com o valor devido
                    const ocorrenciasElement = document.getElementById('ocorrenciasValorDevido');
                    if (ocorrenciasElement) {
                        ocorrenciasElement.textContent = `R$ ${data.valor_a_pagar}`;
                    } else {
                        console.error('Elemento com id "ocorrenciasValorDevido" não encontrado.');
                    }
                } else {
                    console.error('Erro: ', data.message);
                }
            })
            .catch(error => console.error('Erro ao buscar dados:', error));
    }

    // Chama a função para obter o valor de evasão devido
    getValorEvasaoDevido();
});