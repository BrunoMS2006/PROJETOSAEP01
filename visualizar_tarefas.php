<?php
require_once 'conexao.php';

// Verifica se houve erro de conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Alterar status da tarefa
if (isset($_POST['update_status'])) {
    $tarefa_cod = $_POST['tarefa_cod'];
    $status = $_POST['status'];

    if (!empty($tarefa_cod) && !empty($status)) {
        $sql = "UPDATE tarefas SET tarefa_status = ? WHERE tarefa_cod = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $tarefa_cod);

        if ($stmt->execute()) {
            echo "<p>Status da tarefa atualizado com sucesso!</p>";
        } else {
            echo "<p>Erro ao atualizar status: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Consultar as tarefas para exibir na tabela
$tarefas = $conn->query("SELECT * FROM tarefas");
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Tarefas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: auto;
            padding-top: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f4f4f4;
        }

        .actions form {
            display: inline;
        }

        .actions select, .actions button {
            margin-left: 5px;
        }

        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Visualizar Tarefas</h2>

        <!-- Lista de Tarefas -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Setor</th>
                    <th>Prioridade</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($tarefa = $tarefas->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($tarefa['tarefa_cod']) ?></td>
                        <td><?= htmlspecialchars($tarefa['tarefa_setor']) ?></td>
                        <td><?= htmlspecialchars($tarefa['tarefa_prioridade']) ?></td>
                        <td><?= htmlspecialchars($tarefa['tarefa_descricao']) ?></td>
                        <td><?= htmlspecialchars($tarefa['tarefa_status']) ?></td>
                        <td class="actions">
                            <!-- Formulário para alterar o status -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="tarefa_cod" value="<?= htmlspecialchars($tarefa['tarefa_cod']) ?>">
                                
                                <label for="status">Novo Status:</label>
                                <select name="status" required>
                                    <option value="Em andamento" <?= $tarefa['tarefa_status'] == 'Em andamento' ? 'selected' : '' ?>>Em andamento</option>
                                    <option value="Concluída" <?= $tarefa['tarefa_status'] == 'Concluída' ? 'selected' : '' ?>>Concluída</option>
                                    <option value="Pendente" <?= $tarefa['tarefa_status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                </select>

                                <button type="submit" name="update_status">Alterar Status</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
