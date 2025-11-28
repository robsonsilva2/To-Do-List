<?php
    // Início da sessão
    require_once 'Conexao/conexao.php';
    session_start();

    $mensagem = '';
    $usuario = '';
    $email='';

    try {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $senha_digitada = $_POST['senha'];
        
            // Busca o usuário pelo e-mail
            $sql = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        
             // Verifica a senha
            if ($usuario && password_verify($senha_digitada, $usuario['senha'])) {

                //Senha correta! Salva na sessão
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];


                // Redireciona para a página de tarefas
                header("Location: tarefas.php");
                exit;
            } else {
                $mensagem = "E-mail ou senha incorretos.";
            }
        }
    } catch (Exception $e) {
        echo "<p>Erro na conexão: ".$e->getMessage(). "</p>";
    }
?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/index.css">
    <link rel="shortcut icon" href="images/favicon.svg" type="image/svg">
    <title>Login</title>
</head>
<body>
    
    <?php if (!$usuario && !empty($mensagem)): ?>
        <p style="color:red;"><?= "Usuário não cadastrado"?></p>
    <?php else :?>
        <p style="color:red;"><?= htmlspecialchars($mensagem) ?></p>
    <?php endif; ?>

    <div>
        <h1>Login</h1>
        <form id="login" method="POST">
            <label>Email: </label>
            <input type="email" name="email" class="control_form" id="inputEmail" placeholder="Digite o seu email" required><br>
            <label>Senha: </label>
            <input type="password" name="senha" class="control_form" placeholder="Digite a sua senha" id="inputSenha" required><br>
            <button id="buttonEntrar" type="submit">Entrar</button>
        </form>
        <br>
    </div>
    <button id="buttonRegistrar">
        <a href="registro.php">Registre-se</a>
    </button>
    <br>  
</body>
</html>