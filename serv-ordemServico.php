<?php include('header.php'); ?>

<?php

if(isset($_POST['cmd'])){
    $cmd = $_POST['cmd'];

    if($cmd == "add"){
        $query = 'insert into tbl_ordemServico(cliente,descricao,solicitacao,prevEntrega,status) values(
            '.$_POST['cliente'].',
            "'.$_POST['descricao'].'",
            "'.$_POST['solicitacao'].'",
            "'.$_POST['previsao'].'",
            '.$_POST['status'].'
        )';
        $con->query($query);
        redirect($con->error);
    }
    elseif($cmd == "edt"){
        $query = 'update tbl_ordemServico set
            cliente = '.$_POST['cliente'].',
            descricao = "'.$_POST['descricao'].'",
            solicitacao = "'.$_POST['solicitacao'].'",
            prevEntrega = "'.$_POST['previsao'].'",
            status = '.$_POST['status'].'
            where id = '.$_POST['id'].'
        ';
        $con->query($query);
        redirect($con->error);
    }
}
elseif(isset($_GET['del'])){
    $con->query('delete from tbl_ordemServico where id = '.$_GET['del']);
    #redirect($con->error);
}

?>
<script>
    async function imprimir(){
        const divPrint = document.getElementById('tablePrint');
        newWin = window.open('');
        newWin.document.write('<link href="./main.css" rel="stylesheet">');
        newWin.document.write('<link href="./assets/css/print.css" rel="stylesheet">');
        newWin.document.write('<button class="btn m-2 bg-primary noPrint" onclick="window.print();window.close()"><i class="fa fa-print text-white"></i></button><br><br><h5 class="mb-3">Clientes Cadastrados</h5>');
        newWin.document.write(divPrint.outerHTML);
        //await new Promise(r => setTimeout(r, 150));
        //newWin.print();
        //newWin.close();
    }
    $(document).ready(function(){
        $("#campoPesquisa").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tablePrint tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

<!-- cabeçalho da página -->
<div class="app-page-title">

    <div class="page-title-wrapper">

        <div class="page-title-heading">

            <div class="page-title-icon">
                <i class="fas fa-newspaper icon-gradient bg-happy-itmeo"></i>
            </div>
            <div>
                <span>Cadastro de ordens de serviços</span>
                <div class="page-title-subheading">
                    Campo para adição, remoção e edição de ordens de serviços
                </div>
            </div>

        </div>
        <div class="page-title-actions">

            <button class="btn-shadow mr-3 btn btn-dark" id="btn-modal" type="button" data-toggle="modal" data-target="#mdl-cliente">
                <i class="fas fa-user-plus"></i>
            </button>

            <div class="d-inline-block dropdown">
                <button class="btn-shadow dropdown-toggle btn btn-info" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="btn-icon-wrapper pr-2 opacity-7">
                        <i class="fa fa-business-time fa-w-20"></i>
                    </span>
                    Ações
                </button>
                <div class="dropdown-menu dropdown-menu-right" tabindex="-1" role="menu" x_placement="bottom-end">
                    <ul class="nav flex-column">
                        <li class="nav-item">

                            <a class="nav-link text-dark" onclick="imprimir()">
                                Imprimir
                            </a>
                            <a class="nav-link text-dark" target="_blank" href="exp.php?tbl=ordemServico">
                                Exportar
                            </a>
                        
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- fim cabeçalho-->

<!-- conteúdo -->
<div class="content">

    <div class="row">
        <div class="col">
            
            <div class="card main-card mb-3">
                <div class="card-body">

                    <h5 class="card-title">Ordens de serviços</h5>
                    <input type="text" class="mb-2 form-control w-25" placeholder="Pesquisar" id="campoPesquisa">

                    <table class="table mb-0 table-striped table-hover" id="tablePrint">
                        <thead >
                            <tr>
                                <th style="width:2%">ID</th>
                                <th style="width:26%">Nome</th>
                                <th>Descrição</th>
                                <th style="width:14%">Data de solicitação</th>
                                <th style="width:14%">Previsão de entrega</th>
                                <th style="width:6%">status</th>
                                <th class="noPrint"></th>
                                <th class="noPrint"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $resp = $con->query('select * from tbl_ordemServico');
                            
                                while($row = $resp->fetch_assoc()){
                                    $nome = $con->query('select razaoSocial_nome from tbl_clientes where id = '.$row['cliente'])->fetch_assoc()['razaoSocial_nome'];
                                    $status = '';
                                    $corStatus = '';
                                    switch($row['status']){
                                        case 1:
                                            $status = 'Aguardando';
                                            $corStatus = 'focus';
                                        break;
                                        case 2:
                                            $status = 'Em andamento';
                                            $corStatus = 'info';
                                        break;
                                        case 3:
                                            $status = 'Aguardando aprovação';
                                            $corStatus = 'warning';
                                        break;
                                        case 4:
                                            $status = 'Finalizado';
                                            $corStatus = 'success';
                                        break;
                                    }
                                    
                                    echo '
                                        <tr>
                                            <td>'.str_pad($row['id'],3,"0",STR_PAD_LEFT).'</td>
                                            <td>'.$nome.'</td>
                                            <td>'.$row['descricao'].'</td>
                                            <td>'.date('d / m / Y', strtotime($row['solicitacao'])).'</td>
                                            <td>'.date('d / m / Y', strtotime($row['prevEntrega'])).'</td>
                                            <td><div class="badge badge-'.$corStatus.' p-2">'.$status.'</div></td>
                                            <td class="noPrint text-center"><a href="?edt='.$row['id'].'" class="btn"><i class="fas fa-user-edit icon-gradient bg-happy-itmeo"></i></a></td>
                                            <td class="noPrint text-center"><a href="?del='.$row['id'].'" class="btn"><i class="fas fa-trash icon-gradient bg-happy-itmeo"></i></a></td>
                                        </tr>
                                    ';
                                }
                        
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>

</div>
<!-- fim conteúdo -->

<?php include('footer.php');?>

<!-- modal -->
<div class="modal show" tabindex="-1" role="dialog" id="mdl-cliente">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar novo cliente</h5>
                <button type="button" class="close" onclick="location.href='?'" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post">

                    <?php
                        if(isset($_GET['edt'])){
                            $resp = $con->query('select * from tbl_ordemServico where id = '.$_GET['edt']);
                            $ordemServico = $resp->fetch_assoc();
                        }
                    ?>

                    <input type="hidden" value="<?php echo isset($_GET['edt'])?'edt':'add';?>" name="cmd">
                    <input type="hidden" value="<?php echo $_GET['edt'];?>" name="id" id="id">

                    <div class="row">
                        <div class="col">
                            <label for="cliente">Cliente<span class="ml-2 text-danger">*</span></label>
                            <select class="form-control mb-3" name="cliente" id="cliente" required>
                                <option <?php echo isset($_GET['edt'])? '':'selected';?> disabled>Selecione o cliente</option>
                                <?php
                                    $resp = $con->query('select id, razaoSocial_nome from tbl_clientes where tipoCliente="on"');
                                    while($row = $resp->fetch_assoc()){
                                        $selected = $ordemServico['cliente'] == $row['id']? 'selected':'';
                                        echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['razaoSocial_nome'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="status">Status</label>
                            <select class="form-control mb-3" name="status" id="status">
                                <option value="1" <?php echo $ordemServico['status'] == '2' || !isset($_GET['edt'])? 'selected':'';?>>Aguardando</option>
                                <option value="2" <?php echo $ordemServico['status'] == '2'? 'selected':'';?>>Em andamento</option>
                                <option value="3" <?php echo $ordemServico['status'] == '3'? 'selected':'';?>>Aguardando aprovação</option>
                                <option value="4" <?php echo $ordemServico['status'] == '4'? 'selected':'';?>>Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label for="solicitacao">Data de Solicitação</label>
                            <input type="date" value="<?php echo $ordemServico['solicitacao'];?>" class="form-control mb-3" name="solicitacao" id="solicitacao">
                        </div>
                        <div class="col">
                            <label for="previsao">Previsão de entrega</label>
                            <input type="date" value="<?php echo $ordemServico['prevEntrega'];?>" class="form-control mb-3" name="previsao" id="previsao">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label for="descricao">Descrição</label>
                            <textarea class="form-control mb-3" name="descricao" id="descricao" style="resize:none;"><?php echo $ordemServico['descricao'];?></textarea>
                        </div>
                    </div>

                    <input id="needs-validation" class="d-none" type="submit" value="enviar">

                </form>
                <script>
                    $(document).ready(function(){
                        $("#cpfResponsavel").mask('000.000.000-00', {reverse: true});
                        $("#telefoneEmpresa").mask('(99) 99999-9999');
                        $("#telefoneWhatsapp").mask('(99) 99999-9999');
                        $("#cep").mask('99999-999');
                    });
                </script>

            </div>
            <div class="modal-footer">
                <p class="text-start"><span class="ml-2 text-danger">*</span> Campos obrigatórios</p>
                <button type="button" class="btn btn-secondary" onclick="location.href='?'">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('needs-validation').click();"><?php echo isset($_GET['edt'])? 'Atualizar':'Salvar';?></button>
            </div>
        </div>
    </div>
</div>
<!-- fim modal -->

<div id="toast-container" class="toast-top-center">
    <div id="toast-success" class="toast toast-success" aria-live="polite" style="opacity: 0.899999;display:none;">
        <div class="toast-title">Sucesso!</div>
    </div>
    <div id="toast-error" class="toast toast-error hidden" aria-live="polite" style="opacity: 0.899999;display:none;">
        <div class="toast-title">Erro!</div>
    </div>
    <?php
        if(isset($_GET['s']))
            echo "<script>loadToast(true);</script>";
        else if(isset($_GET['e']))
            echo "<script>loadToast(false);</script>";
    ?>
</div>

<?php if(isset($_GET['edt'])) echo "<script>$('#btn-modal').click()</script>"; ?>