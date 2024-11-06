<?php
require_once 'conexao.php';

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Adicionar tarefa
if (isset($_POST['add_task'])) {
    $setor = trim($_POST['setor']);
    $prioridade = trim($_POST['prioridade']);
    $descricao = trim($_POST['descricao']);
    $status = trim($_POST['status']);

    if (!empty($setor) && !empty($prioridade) && !empty($descricao) && !empty($status)) {
        $sql = "INSERT INTO tarefas (tarefa_setor, tarefa_prioridade, tarefa_descricao, tarefa_status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $setor, $prioridade, $descricao, $status);

        if ($stmt->execute()) {
            echo "<p>Tarefa cadastrada com sucesso!</p>";
        } else {
            echo "<p>Erro ao cadastrar tarefa: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Editar tarefa
if (isset($_POST["edit_task"])) {
    $tarefa_cod = isset($_POST['tarefa_cod']) ? $_POST['tarefa_cod'] : '';
    $tarefa_setor = isset($_POST['tarefa_setor']) ? trim($_POST['tarefa_setor']) : '';
    $tarefa_prioridade = isset($_POST['tarefa_prioridade']) ? trim($_POST['tarefa_prioridade']) : '';
    $tarefa_descricao = isset($_POST['tarefa_descricao']) ? trim($_POST['tarefa_descricao']) : '';
    $tarefa_status = isset($_POST['tarefa_status']) ? trim($_POST['tarefa_status']) : '';

    if (!empty($tarefa_cod) && !empty($tarefa_setor) && !empty($tarefa_prioridade) && !empty($tarefa_descricao) && !empty($tarefa_status)) {
        $sql = "UPDATE tarefas SET tarefa_setor = ?, tarefa_prioridade = ?, tarefa_descricao = ?, tarefa_status = ? WHERE tarefa_cod = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $tarefa_setor, $tarefa_prioridade, $tarefa_descricao, $tarefa_status, $tarefa_cod);

        if ($stmt->execute()) {
            echo "<p>Tarefa atualizada com sucesso!</p>";
        } else {
            echo "<p>Erro ao atualizar tarefa: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Excluir tarefa
if (isset($_POST["delete_task"])) {
    $tarefa_cod = isset($_POST['tarefa_cod']) ? $_POST['tarefa_cod'] : '';

    if (!empty($tarefa_cod)) {
        $sql = "DELETE FROM tarefas WHERE tarefa_cod = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tarefa_cod);

        if ($stmt->execute()) {
            echo "<p>Tarefa excluída com sucesso!</p>";
        } else {
            echo "<p>Erro ao excluir tarefa: " . $stmt->error . "</p>";
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
    <title>Cadastro e Edição de Tarefas</title>
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
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ddd;
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
        .actions {
            display: flex;
            justify-content: space-evenly;
        }
        .actions button {
            margin-left: 5px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Tarefa</h2>

        <!-- Formulário para Adicionar Tarefa -->
        <form method="POST">
            <label for="setor">Setor:</label>
            <input type="text" id="setor" name="setor" placeholder="Digite o setor" required>

            <label for="prioridade">Prioridade:</label>
            <select id="prioridade" name="prioridade" required>
                <option value="Alta">Alta</option>
                <option value="Média">Média</option>
                <option value="Baixa">Baixa</option>
            </select>

            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" placeholder="Digite a descrição da tarefa" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="Em andamento">Em andamento</option>
                <option value="Pendente">Pendente</option>
                <option value="Concluído">Concluído</option>
            </select>

            <button type="submit" name="add_task">Cadastrar Tarefa</button>
        </form>

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
                            <!-- Botões de Editar e Excluir -->
                            <button class="editBtn" data-id="<?= htmlspecialchars($tarefa['tarefa_cod']) ?>"
                                    data-setor="<?= htmlspecialchars($tarefa['tarefa_setor']) ?>"
                                    data-prioridade="<?= htmlspecialchars($tarefa['tarefa_prioridade']) ?>"
                                    data-descricao="<?= htmlspecialchars($tarefa['tarefa_descricao']) ?>"
                                    data-status="<?= htmlspecialchars($tarefa['tarefa_status']) ?>">Editar</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="tarefa_cod" value="<?= htmlspecialchars($tarefa['tarefa_cod']) ?>">
                                <button type="submit" name="delete_task">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Modal de Edição -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Editar Tarefa</h2>
                <form method="POST">
                    <input type="hidden" id="tarefa_cod" name="tarefa_cod">

                    <label for="tarefa_setor">Setor:</label>
                    <input type="text" id="tarefa_setor" name="tarefa_setor" required>

                    <label for="tarefa_prioridade">Prioridade:</label>
                    <select id="tarefa_prioridade" name="tarefa_prioridade" required>
                        <option value="Alta">Alta</option>
                        <option value="Média">Média</option>
                        <option value="Baixa">Baixa</option>
                    </select>

                    <label for="tarefa_descricao">Descrição:</label>
                    <input type="text" id="tarefa_descricao" name="tarefa_descricao" required>

                    <label for="tarefa_status">Status:</label>
                    <select id="tarefa_status" name="tarefa_status" required>
                        <option value="Em andamento">Em andamento</option>
                        <option value="Pendente">Pendente</option>
                        <option value="Concluído">Concluído</option>
                    </select>

                    <button type="submit" name="edit_task">Atualizar Tarefa</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Script para abrir o modal de edição
        const editBtns = document.querySelectorAll('.editBtn');
        const modal = document.getElementById('editModal');
        const closeBtn = document.querySelector('.close');

        editBtns.forEach(button => {
            button.addEventListener('click', () => {
                const tarefaCod = button.getAttribute('data-id');
                const setor = button.getAttribute('data-setor');
                const prioridade = button.getAttribute('data-prioridade');
                const descricao = button.getAttribute('data-descricao');
                const status = button.getAttribute('data-status');

                // Preencher os campos do formulário no modal com os dados
                document.getElementById('tarefa_cod').value = tarefaCod;
                document.getElementById('tarefa_setor').value = setor;
                document.getElementById('tarefa_prioridade').value = prioridade;
                document.getElementById('tarefa_descricao').value = descricao;
                document.getElementById('tarefa_status').value = status;

                // Mostrar o modal
                modal.style.display = "block";
            });
        });

        // Fechar o modal
        closeBtn.addEventListener('click', () => {
            modal.style.display = "none";
        });

        // Fechar o modal se o usuário clicar fora da área do modal
        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>
</body>
</html>
