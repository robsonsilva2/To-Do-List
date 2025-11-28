<?php 
session_start();
require_once 'Conexao/conexao.php';

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Entrada de dados
    $nome = $_POST['criar_nome'] ?? '';
    $email = $_POST['criar_email'] ?? '';
    $senha = $_POST['criar_senha'] ?? '';

    // Verificação simples
    if (empty($nome) || empty($email) || empty($senha)) {
        $mensagem = "Por favor, preencha todos os campos.";
    } else {

        // Hash da senha
        $hash_senha = password_hash($senha, PASSWORD_DEFAULT);

        try {
            // Verificar email duplicado
            $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $check->execute([$email]);

            if ($check->rowCount() > 0) {
                $mensagem = "Este email já está registrado.";
            } else {

                // Cadastro
                $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $hash_senha]);
                
                $mensagem = "Usuário criado com sucesso!";
            }

        } catch (PDOException $e) {
            $mensagem = "Erro ao registrar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/lista_tarefas/style/registro.css">
    <link rel="shortcut icon" href="images/favicon.svg" type="image/svg">
    <title>Registro Usuários</title>
</head>
<body>

    <h1>Registro de Usuário</h1>

    <?php if (!empty($mensagem)): ?>
        <p style="color:red; font-weight: bold;"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>
    <br>

    <div id="registrador">
        <form action="" method="POST">

            <label>Nome:</label>
            <input type="text" name="criar_nome" placeholder="Digite seu nome" required>

            <label>Email:</label>
            <input type="email" name="criar_email" placeholder="Digite seu email" required>

            <label>Senha:</label>
            <input type="password" name="criar_senha" placeholder="Digite sua senha" required>

            <div id="registrar">
                <button type="submit" id="registrarButton">Registrar</button>
            </div>
        </form>
    </div>
    <br>
    <button id="voltarButton">
        <a href="index.php">Voltar</a>
    </button>
    

</body>
</html>
