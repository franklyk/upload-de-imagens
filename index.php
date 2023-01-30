<?php

include("connect.php");

if(isset($_GET['deletar'])){
    $id = intval($_GET['deletar']);
    $sql_query = $mysqli->query("SELECT * FROM arquivo WHERE id = '$id'") or die($mysqli->error);
    $arquivo = $sql_query->fetch_assoc();

    if(unlink($arquivo['path'])) {
        $deu_certo = $mysqli->query("DELETE FROM arquivo WHERE id = '$id'") or die($mysqli->error);
        if($deu_certo)    
        echo "<p>Arquivo excluido com sucesso!!";
    }
}
    function enviarArquivo($error, $size, $name, $tmp_name) {

    include("connect.php");

    if($error)
        die("Falha ao enviar!!");

    if($size > 2097152)
        die("Tamanho máximo suportado: 2Mb");


    $pasta = "b_img/"; 
    $nomeDoArqivo = $name;
    $novoNomeDoArquivo = uniqid();
    $extensao = strtolower(pathinfo($nomeDoArqivo, PATHINFO_EXTENSION));


    if($extensao != "jpg" && $extensao != "png")
        die("Tipo de arquivo não suportado!");
    $path = $pasta . $novoNomeDoArquivo . "." . $extensao;
    $concluido = move_uploaded_file($tmp_name, $path);
    if($concluido){
        $mysqli->query("INSERT INTO arquivo (path, nome) VALUES('$path','$nomeDoArqivo')") or die ($mysqli->error);
        return true;
    }else{
        return false;
    }
}


if(isset($_FILES['arquivo'])){
    $arquivo = $_FILES['arquivo'];
    $enviado = true;
    foreach($arquivo['name'] as $index => $arq)
        $deu_certo = enviarArquivo($arquivo['error'][$index], $arquivo['size'][$index], $arquivo['name'][$index], $arquivo["tmp_name"][$index]);
        if(!$deu_certo)
            $enviado = false;
    if($deu_certo)
        echo "<p>Arquivos enviados com sucesso!</p>";
    else
        echo "<p>Falha ao wnviar!</p>";    
}   


$sql_query = $mysqli->query("SELECT * FROM arquivo") or die($mysqli->error);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>TesteBD</title>
</head>
<body>
    <form enctype="multipart/form-data" action="" method="post">
        <label for="arquivo">Selecione o arquivo</label>
        <input multiple type="file" name="arquivo[]" ><br><br>
        <button name="upload" type="submit">Enviar Arquivo</button>
    </form>
    <div class="grid-container">
        <h1>Lista de Arquivos</h1>
        <!-- <table border="1" cellpadding="10">
            <thead>
                <th>Preview</th>
                <th>Arquivo</th>
                <th>Data de Envio</th>
            </thead>
            <tbody> -->
                <?php
                while($arquivo = $sql_query->fetch_assoc()){
                ?>
                <div class="container">
                    <div class="images">
                        <img height="100" src="<?= $arquivo['path']; ?>" alt="">
                    </div>
                    <div class="text">
                        <p>
                            <a href="<?= $arquivo['path']; ?>"><?= $arquivo['nome']; ?></a>
                        </p>
                        <p>
                        <?= date("d/m/Y H:i", strtotime($arquivo['data_upload'])); ?>
                        </p>
                    </div>
                    <button><a href="index.php?deletar=<?= $arquivo['id'];?>">Excluir</a></button>
                </div>
                <!-- <tr>
                    <td><img height="100" src="<?php echo $arquivo['path']; ?>" alt=""></td>
                    <td><a target="_blank" href="<?php echo $arquivo['path']; ?>"><?php echo $arquivo['nome']; ?></a></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($arquivo['data_upload'])); ?></</td>
                    <th><a href="index.php?deletar=<?php echo $arquivo['id']; ?>">Deletar</a></th>
                </tr> -->
                <?php
                }
                ?>
            <!-- </tbody>
        </table>
         -->
    </div>
</body>
</html>