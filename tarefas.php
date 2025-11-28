<?php
// Início da sessão
session_start();

require_once 'Conexao/conexao.php';

// Verificando se o usuário está logado
if(!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nome = htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8');

// Adicionar nova tarefa
if(isset($_POST['tarefa']) && !isset($_POST['editar_id'])) {
    $descricao = htmlspecialchars($_POST['tarefa']);
    $sql = "INSERT INTO tarefas (usuario_id, descricao) VALUES (?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id, $descricao]);
}

// Marcar como feita
if(isset($_GET['concluida'])) {
    $id = $_GET['concluida'];
    $sql = "UPDATE tarefas SET concluida = 1 WHERE id = ? AND usuario_id = ?";
    $stmt=$pdo->prepare($sql);
    $stmt->execute([$id, $usuario_id]);

    header("Location: tarefas.php");
    exit;
}

// Buscando a tarefa no bd para editar
$editando = false;
$descricao_editar = "";
$id_editar = 0;

if (isset($_GET['editar'])) {
    $id_editar = $_GET['editar'];
    $sql = $pdo->prepare("SELECT descricao FROM tarefas WHERE id = ? AND usuario_id = ?");
    $sql->execute([$id_editar, $usuario_id]);
    $tarefa = $sql->fetch(PDO::FETCH_ASSOC);

    if ($tarefa) {
        $editando = true;
        $descricao_editar = $tarefa['descricao'];
    }
}

// Salvando a edição de tarefas no bd
if (isset($_POST['editar_id'])) {
    $id = $_POST['editar_id'];
    $descricao = htmlspecialchars($_POST['tarefa']);

    $sql = "UPDATE tarefas SET descricao = ? WHERE id = ? AND usuario_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$descricao, $id, $usuario_id]);

    header("Location: tarefas.php");
    exit;
}

// Excluindo a tarefa do bd
if(isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    $sql = "DELETE FROM tarefas WHERE id = ? AND usuario_id = ?";
    $stmt=$pdo->prepare($sql);
    $stmt->execute([$id, $usuario_id]);

    header("Location: tarefas.php");
    exit;
}

// Listando as tarefas
$sql = $pdo->prepare("SELECT * FROM tarefas WHERE usuario_id = ?");
$sql->execute([$usuario_id]);
$tarefas = $sql->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style/tarefas.css">
    <link rel="shortcut icon" href="images/favicon.svg" type="image/svg">
</head>
<body>
    <div class="form">
        <div class="boasvindas">
            <h2>Olá, <?= $nome ?>!</h2>
            <button class="logout"><a href="logout.php">Logout</a></button>
        </div>
        
        <!-- FORMULÁRIO ADICIONAR / EDITAR -->
        <div class="descricao">
            <form method="post" id="descricao">
                <?php if ($editando): ?>
                    <input type="text" name="tarefa" value="<?= $descricao_editar ?>" required>
                    <input type="hidden" name="editar_id" value="<?= $id_editar ?>">
                    <button><i class="fa-solid fa-check"></i></button>
                <?php else: ?>
                    <input type="text" name="tarefa" placeholder="Digite a sua tarefa aqui" required>
                    <button><i class="fa-solid fa-plus"></i></button>
                <?php endif; ?>
            </form>
        </div>

        <!-- LISTA DE TAREFAS -->
        <div class="tarefas">
            <ul>
                <?php foreach ($tarefas as $t): ?>
                    <li>
                        <?php if ($t['concluida']): ?>
                            <s><?= $t['descricao'] ?></s>
                        <?php else: ?>
                            <?= $t['descricao'] ?>
                            <a href="?concluida=<?= $t['id'] ?>">Concluir</a>
                            <a href="?editar=<?= $t['id'] ?>">Editar</a>
                        <?php endif; ?>
                        <a href="?excluir=<?= $t['id'] ?>">Excluir</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>
