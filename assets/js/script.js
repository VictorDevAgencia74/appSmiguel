document.addEventListener('DOMContentLoaded', function() {
    // Função escolher quantidade de linhas da tabela.
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        var rowsPerPage = this.value;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../config/dados.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('tableContent').innerHTML = xhr.responseText;
            }
        };
        xhr.send('rows=' + encodeURIComponent(rowsPerPage));
    });
});