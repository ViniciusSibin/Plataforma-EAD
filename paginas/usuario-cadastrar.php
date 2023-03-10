<?php 

if(isset($_POST['enviar'])){
    include('lib/conexao.php');
    include('lib/funcoes.php');
    include('lib/protect.php');
    protect(1);

    $erro = false;
    
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = limpar_texto($_POST['telefone']);
    $nascimento = implode('-', array_reverse(explode('/',$_POST['nascimento'])));
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmarSenha'];
    $admin = $_POST['admin'];
    
    if(verificaUsuario($nome, 5, 70, 'nome')){
        $erro = verificaUsuario($titulo, 5, 70, 'nome');
    }
    
    if(empty($email)){
        $erro = "O campo e-mail deve ser preenchido!";
    } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $erro = "O e-mail está fora do padrão solicitado!";
    } else if(strlen($email) < 10 || strlen($email) > 40){
        $erro = "O e-mail deve conter entre 10 e 40 caracteres!";
    }

    if(empty($telefone)){
        $erro = "O campo telefone deve ser preenchido!";
    } elseif(strlen($telefone) != 11){
        $erro = "Telefone incorreto!";
    }

    if(empty($nascimento)){
        $erro = "O campo Data de Nascimento não pode estar vazia!";
    } elseif(strlen($nascimento) != 10){
        $erro = "A data de nascimento está fora do padrão, EX: dd/mm/aaaa!";
    }
    
    if(verificaSenha($senha)){
        $erro = verificaSenha($senha);
    }

    if($senha != $confirmarSenha){
        $erro = "As senhas não coincidem";
    }

    if(!$erro){
        $pathFotoUsuario = "";
        $arq = $_FILES['fotoPerfil'];
        if(!empty($arq['name']) && !empty($arq['size'])){
            $pathFotoUsuario = uploadArquivo ($arq['error'], $arq['size'], $arq['name'], $arq['tmp_name'], "assets/images/perfil/");
            if($pathFotoUsuario == 1){
                $erro = "Imagem com erro!";
            } else if($pathFotoUsuario == 2) {
                $erro = "Arquivo muito grande!! Max: 2MB";
            } else if($pathFotoUsuario == 3) {
                $erro = "Tipo de arquivo não aceito, tipos aceitos:<br> <b>jpg</b>, <b>png</b>";
                
            }
        }

        if(!$erro){
            $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

            $sqlCadastroUsuario = "INSERT INTO usuarios (nome, email, senha, telefone, nascimento, fotoPerfil, admin, dataCadastro) VALUES ('$nome', '$email', '$senhaCriptografada', '$telefone' ,'$nascimento' ,'$pathFotoUsuario', '$admin' , NOW())";
            $sqlCadastroUsuarioQuery = $mysqli->query($sqlCadastroUsuario) or die($mysqli->error);
            if($sqlCadastroUsuarioQuery){
                die("<script>location.href=\"index.php?pagina=usuario-gerenciar\";</script>");
            }
        }
    }
}

?>
<div class="auth-body mr-auto ml-auto col-md-7">
    <form class="md-float-material" method="POST" enctype="multipart/form-data">
        <?php if(isset($erro) > 0 && $erro){ ?>
            <div class="alert alert-danger">
                <?php foreach($erro as $e){?>
                <b>ERRO:</b> <?php echo $e;?><br>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="auth-box">
            <div class="col-md-12">
                <h3 class="text-left txt-primary">Cadastrar novo usuário</h3>
                <p class="m-t-30 text-left" style="color: black;">Informe os dados abaixo para cadastrar o usuário!</p>
            </div>
            <hr/>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nome:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Nome do usuário" name="nome" value="<?php if(isset($_POST['nome'])) echo $_POST['nome'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">E-mail:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Ex: exemplo@exemplo.com" name="email" value="<?php if(isset($_POST['email'])) echo $_POST['email'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Telefone:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Ex:(44)98765-4321" name="telefone" value="<?php if(isset($_POST['telefone'])) echo $_POST['telefone'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Data Nascimento:</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Ex:00/00/000" name="nascimento" value="<?php if(isset($_POST['nascimento'])) echo $_POST['nascimento'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Senha:</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" placeholder="Senha" name="senha">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Confirmar Senha:</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" placeholder="Confirmar Senha" name="confirmarSenha">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Administrador? </label>
                <div class="col-sm-10">
                    <div><input type="radio" name="admin" value="1" <?php if((isset($_POST['admin']) && $_POST['admin'])) echo "checked";?> > SIM</div>
                    <div><input type="radio" name="admin" value="0" <?php if((isset($_POST['admin']) && !$_POST['admin'])) echo "checked";?> > NÃO</div>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Foto de Perfil: </label>
                <div class="col-sm-10">
                    <input type="file" name="fotoPerfil">
                </div>
            </div>
            <div class="row m-t-20">
                <div class="col-md-6">
                    <a href="index.php?pagina=gerenciarUsuarios" class="btn btn-danger btn-md btn-block waves-effect text-center m-b-20">Cancelar</a>
                </div>
                <div class="col-md-6">
                    <button type="submit" name="enviar" value="0" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Enviar</button>
                </div>
            </div>
        </div>
    </form>
</div>