<?php
require_once 'conexao.php';

// Verificar se a conexão com o banco de dados foi bem-sucedida
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Adicionar usuário
if (isset($_POST['add_user'])) {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    if (!empty($nome) && !empty($email)) {
        $sql = "INSERT INTO usuarios (usu_nome, usu_email) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nome, $email);

        if ($stmt->execute()) {
            echo "<p>Usuário cadastrado com sucesso!</p>";
        } else {
            echo "<p>Erro ao cadastrar usuário: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Editar usuário
if (isset($_POST["edit_user"])) {
    $usu_cod = $_POST['usu_cod'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    if (!empty($usu_cod) && !empty($nome) && !empty($email)) {
        $sql = "UPDATE usuarios SET usu_nome = ?, usu_email = ? WHERE usu_cod = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome, $email, $usu_cod);

        if ($stmt->execute()) {
            echo "<p>Usuário atualizado com sucesso!</p>";
        } else {
            echo "<p>Erro ao atualizar usuário: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Excluir usuário
if (isset($_POST["delete_user"])) {
    $usu_cod = $_POST['usu_cod'];

    if (!empty($usu_cod)) {
        $sql = "DELETE FROM usuarios WHERE usu_cod = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $usu_cod);

        if ($stmt->execute()) {
            echo "<p>Usuário excluído com sucesso!</p>";
        } else {
            echo "<p>Erro ao excluir usuário: " . $stmt->error . "</p>";
        }

        $stmt->close();
    }
}

// Consultar os usuários para exibir na tabela
$usuarios = $conn->query("SELECT * FROM usuarios");
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuários</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f4f4f4; }
        .container { width: 90%; max-width: 700px; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { margin-bottom: 20px; text-align: center; color: #333; }
        form { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
        label { font-weight: bold; color: #333; }
        input[type="text"], input[type="email"] { padding: 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%; }
        button { padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
        button:hover { background-color: #0056b3; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #007bff; color: white; }
        .actions { display: flex; gap: 5px; justify-content: center; }
        .actions form { display: inline; }
        .actions input[type="text"], .actions input[type="email"] { width: 150px; margin-right: 5px; }
        .message { text-align: center; color: #28a745; margin-bottom: 15px; }
        
        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.4); }
        .modal-content { background-color: white; margin: 10% auto; padding: 20px; border-radius: 4px; width: 300px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; }
        .close:hover, .close:focus { color: black; text-decoration: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Usuários</h2>

        <!-- Mensagem de sucesso/erro -->
        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <!-- Formulário para Adicionar Usuário -->
        <form method="POST">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <button type="submit" name="add_user">Cadastrar</button>
        </form>

        <!-- Lista de Usuários -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['usu_cod']) ?></td>
                        <td><?= htmlspecialchars($usuario['usu_nome']) ?></td>
                        <td><?= htmlspecialchars($usuario['usu_email']) ?></td>
                        <td class="actions">
                            <!-- Botão para abrir o modal -->
                            <button onclick="openModal(<?= htmlspecialchars($usuario['usu_cod']) ?>, '<?= htmlspecialchars($usuario['usu_nome']) ?>', '<?= htmlspecialchars($usuario['usu_email']) ?>')">Editar</button>

                            <!-- Formulário para Excluir Usuário -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="usu_cod" value="<?= htmlspecialchars($usuario['usu_cod']) ?>">
                                <button type="submit" name="delete_user">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal de Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Editar Usuário</h3>
            <form method="POST">
                <input type="hidden" id="usu_cod" name="usu_cod">
                <label for="edit_nome">Nome:</label>
                <input type="text" id="edit_nome" name="nome" required>

                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required>

                <button type="submit" name="edit_user">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <script>
        // Função para abrir o modal
        function openModal(cod, nome, email) {
            document.getElementById('usu_cod').value = cod;
            document.getElementById('edit_nome').value = nome;
            document.getElementById('edit_email').value = email;
            document.getElementById('editModal').style.display = 'block';
        }

        // Função para fechar o modal
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Fechar o modal se o usuário clicar fora dele
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeModal();
            }
        }
    </script>
</body>
</html>
