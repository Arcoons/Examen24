<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Notas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Registro de Notas</h2>
        <button id="add-column" class="btn btn-primary mb-2">Agregar Nota (+)</button>
        <button id="save-data" class="btn btn-success mb-2">Guardar Notas</button>
        <table class="table table-bordered" id="grades-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Nota 1</th>
                    <th>Nota 2</th>
                    <th>Nota 3</th>
                    <th>Promedio</th>
                    <th>Equivalencia</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aquí se cargarán las filas dinámicamente -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            // Agregar columnas dinámicamente
            $('#add-column').on('click', function () {
                const newColumnIndex = $('#grades-table thead th').length - 2;
                $('#grades-table thead tr').append(`<th>Nota ${newColumnIndex}</th>`);
                $('#grades-table tbody tr').each(function () {
                    $(this).append(`<td><input type="number" step="0.1" min="0" max="5" class="form-control nota"></td>`);
                });
            });

            // Validar entrada de notas
            $('#grades-table').on('input', '.nota', function () {
                const nota = parseFloat($(this).val());
                if (isNaN(nota) || nota < 0 || nota > 5) {
                    alert('Error: La nota debe estar entre 0 y 5.');
                    $(this).val(''); // Limpiar valor inválido
                } else {
                    const row = $(this).closest('tr');
                    recalcularPromedioYEquivalencia(row);
                }
            });

            // Recalcular promedio y equivalencia
            function recalcularPromedioYEquivalencia(row) {
                const notas = [];
                row.find('.nota').each(function () {
                    notas.push(parseFloat($(this).val()) || 0);
                });
                const promedio = calcularPromedio(notas);
                row.find('.promedio').text(promedio);
                row.find('.equivalencia').text(calcularEquivalencia(promedio));
            }

            // Navegación con flechas y tecla ENTER
            $('#grades-table').on('keydown', '.nota', function (e) {
                const inputs = $('#grades-table').find('.nota');
                const index = inputs.index(this);

                if (e.key === 'ArrowRight') {
                    inputs.eq(index + 1).focus();
                    e.preventDefault();
                } else if (e.key === 'ArrowLeft') {
                    inputs.eq(index - 1).focus();
                    e.preventDefault();
                } else if (e.key === 'ArrowDown') {
                    const colIndex = $(this).closest('td').index();
                    const nextRow = $(this).closest('tr').next();
                    nextRow.find('td').eq(colIndex).find('.nota').focus();
                    e.preventDefault();
                } else if (e.key === 'ArrowUp') {
                    const colIndex = $(this).closest('td').index();
                    const prevRow = $(this).closest('tr').prev();
                    prevRow.find('td').eq(colIndex).find('.nota').focus();
                    e.preventDefault();
                } else if (e.key === 'Enter') {
                    inputs.eq(index + 1).focus();
                    e.preventDefault();
                }
            });

            // Guardar datos usando AJAX
            $('#save-data').on('click', function () {
                const rows = [];
                $('#grades-table tbody tr').each(function () {
                    const row = {
                        codigo: $(this).find('.codigo').text(),
                        nombre: $(this).find('.nombre').text(),
                        notas: []
                    };
                    $(this).find('.nota').each(function () {
                        row.notas.push(parseFloat($(this).val()) || 0);
                    });
                    rows.push(row);
                });

                $.ajax({
                    url: 'backend.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(rows),
                    success: function (response) {
                        alert('Datos guardados exitosamente');
                    },
                    error: function () {
                        alert('Error al guardar los datos');
                    }
                });
            });

            // Cargar datos al iniciar
            function loadData() {
                $.ajax({
                    url: 'backend.php',
                    method: 'GET',
                    success: function (data) {
                        const rows = JSON.parse(data);
                        rows.forEach(row => {
                            const notas = [row.nota1, row.nota2, row.nota3];
                            const promedio = calcularPromedio(notas);
                            const equivalencia = calcularEquivalencia(promedio);

                            const newRow = `<tr>
                                <td class="codigo">${row.codigo}</td>
                                <td class="nombre">${row.nombre}</td>
                                <td><input type="number" value="${row.nota1}" class="form-control nota"></td>
                                <td><input type="number" value="${row.nota2}" class="form-control nota"></td>
                                <td><input type="number" value="${row.nota3}" class="form-control nota"></td>
                                <td class="promedio">${promedio}</td>
                                <td class="equivalencia">${equivalencia}</td>
                            </tr>`;
                            $('#grades-table tbody').append(newRow);
                        });
                    },
                    error: function () {
                        alert('Error al cargar los datos');
                    }
                });
            }

            // Funciones auxiliares
            function calcularPromedio(notas) {
                const sum = notas.reduce((acc, curr) => acc + (curr || 0), 0);
                return (notas.length > 0) ? (sum / notas.length).toFixed(2) : 0;
            }

            function calcularEquivalencia(promedio) {
                if (promedio < 2) return 'Bajo';
                if (promedio < 3) return 'Básico';
                if (promedio < 4) return 'Alto';
                return 'Superior';
            }

            // Llamar a la función para cargar datos al iniciar
            loadData();
        });
    </script>
</body>
</html>