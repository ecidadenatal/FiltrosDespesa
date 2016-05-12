<?
//MODULO: caixa
//CLASSE DA ENTIDADE saltesdepart
class cl_saltesdepart {
  // cria variaveis de erro
  var $rotulo     = null;
  var $query_sql  = null;
  var $numrows    = 0;
  var $numrows_incluir = 0;
  var $numrows_alterar = 0;
  var $numrows_excluir = 0;
  var $erro_status= null;
  var $erro_sql   = null;
  var $erro_banco = null;
  var $erro_msg   = null;
  var $erro_campo = null;
  var $pagina_retorno = null;
  // cria variaveis do arquivo
  var $sequencial = 0;
  var $saltes = 0;
  var $depart = 0;
  // cria propriedade com as variaveis do arquivo
  var $campos = "
                 sequencial = int4 = Sequencial
                 saltes = int4 = Conta Tesouraria
                 depart = int4 = Departamento
                 ";
  //funcao construtor da classe
  function cl_saltesdepart() {
    //classes dos rotulos dos campos
    $this->rotulo = new rotulo("saltesdepart");
    $this->pagina_retorno =  basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]);
  }
  //funcao erro
  function erro($mostra,$retorna) {
    if(($this->erro_status == "0") || ($mostra == true && $this->erro_status != null )){
      echo "<script>alert(\"".$this->erro_msg."\");</script>";
      if($retorna==true){
        echo "<script>location.href='".$this->pagina_retorno."'</script>";
      }
    }
  }
  // funcao para atualizar campos
  function atualizacampos($exclusao=false) {
    if($exclusao==false){
      $this->sequencial = ($this->sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sequencial"]:$this->sequencial);
      $this->saltes = ($this->saltes == ""?@$GLOBALS["HTTP_POST_VARS"]["saltes"]:$this->saltes);
      $this->depart = ($this->depart == ""?@$GLOBALS["HTTP_POST_VARS"]["depart"]:$this->depart);
    }else{
      $this->sequencial = ($this->sequencial == ""?@$GLOBALS["HTTP_POST_VARS"]["sequencial"]:$this->sequencial);
    }
  }
  // funcao para Inclusão
  function incluir ($sequencial){
    $this->atualizacampos();
    if($this->saltes == null ){
      $this->erro_sql = " Campo Conta Tesouraria não informado.";
      $this->erro_campo = "saltes";
      $this->erro_banco = "";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
    if($this->depart == null ){
      $this->erro_sql = " Campo Departamento não informado.";
      $this->erro_campo = "depart";
      $this->erro_banco = "";
      $this->erro_msg   = "Usúario: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
    if($sequencial == "" || $sequencial == null ){
      $result = db_query("select nextval('plugins.saltesdepart_sequencial_seq')");
      if($result==false){
        $this->erro_banco = str_replace("\n","",@pg_last_error());
        $this->erro_sql   = "Verifique o cadastro da sequencia: saltesdepart_sequencial_seq do campo: sequencial";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
      $this->sequencial = pg_result($result,0,0);
    }else{
      $result = db_query("select last_value from plugins.saltesdepart_sequencial_seq");
      if(($result != false) && (pg_result($result,0,0) < $sequencial)){
        $this->erro_sql = " Campo sequencial maior que último número da sequencia.";
        $this->erro_banco = "Sequencia menor que este número.";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }else{
        $this->sequencial = $sequencial;
      }
    }
    if(($this->sequencial == null) || ($this->sequencial == "") ){
      $this->erro_sql = " Campo sequencial não declarado.";
      $this->erro_banco = "Chave Primaria zerada.";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
    $sql = "insert into plugins.saltesdepart(
                                       sequencial
                                      ,saltes
                                      ,depart
                       )
                values (
                                $this->sequencial
                               ,$this->saltes
                               ,$this->depart
                      )";
    $result = db_query($sql);
    if($result==false){
      $this->erro_banco = str_replace("\n","",@pg_last_error());
      if( strpos(strtolower($this->erro_banco),"duplicate key") != 0 ){
        $this->erro_sql   = "Departamento de controle da conta ($this->sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_banco = "Departamento de controle da conta já Cadastrado";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      }else{
        $this->erro_sql   = "Departamento de controle da conta ($this->sequencial) não Incluído. Inclusão Abortada.";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      }
      $this->erro_status = "0";
      $this->numrows_incluir= 0;
      return false;
    }

    $this->erro_banco = "";
    $this->erro_sql = "Inclusão efetuada com Sucesso\\n";
    $this->erro_sql .= "Valores : ".$this->sequencial;
    $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
    $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
    $this->erro_status = "1";
    $this->numrows_incluir= pg_affected_rows($result);
    return true;
  }
  // funcao para alteracao
  public function alterar ($sequencial=null) {
    $this->atualizacampos();
    $sql = " update plugins.saltesdepart set ";
    $virgula = "";
    if(trim($this->sequencial)!="" || isset($GLOBALS["HTTP_POST_VARS"]["sequencial"])){
      $sql  .= $virgula." sequencial = $this->sequencial ";
      $virgula = ",";
      if(trim($this->sequencial) == null ){
        $this->erro_sql = " Campo Sequencial não informado.";
        $this->erro_campo = "sequencial";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if(trim($this->saltes)!="" || isset($GLOBALS["HTTP_POST_VARS"]["saltes"])){
      $sql  .= $virgula." saltes = $this->saltes ";
      $virgula = ",";
      if(trim($this->saltes) == null ){
        $this->erro_sql = " Campo Conta Tesouraria não informado.";
        $this->erro_campo = "saltes";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    if(trim($this->depart)!="" || isset($GLOBALS["HTTP_POST_VARS"]["depart"])){
      $sql  .= $virgula." depart = $this->depart ";
      $virgula = ",";
      if(trim($this->depart) == null ){
        $this->erro_sql = " Campo Departamento não informado.";
        $this->erro_campo = "depart";
        $this->erro_banco = "";
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "0";
        return false;
      }
    }
    $sql .= " where ";
    if($sequencial!=null){
      $sql .= " sequencial = $this->sequencial";
    }

    $result = db_query($sql);
    if (!$result) {
      $this->erro_banco = str_replace("\n","",@pg_last_error());
      $this->erro_sql   = "Departamento de controle da conta não Alterado. Alteração Abortada.\\n";
      $this->erro_sql .= "Valores : ".$this->sequencial;
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      $this->numrows_alterar = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Departamento de controle da conta não foi Alterado. Alteração Executada.\\n";
        $this->erro_sql .= "Valores : ".$this->sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Alteração efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$this->sequencial;
        $this->erro_msg   = "Usário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_alterar = pg_affected_rows($result);
        return true;
      }
    }
  }
  // funcao para exclusao
  public function excluir ($sequencial=null,$dbwhere=null) {


    $sql = " delete from plugins.saltesdepart
                    where ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($sequencial)){
        if (!empty($sql2)) {
          $sql2 .= " and ";
        }
        $sql2 .= " sequencial = $sequencial ";
      }
    } else {
      $sql2 = $dbwhere;
    }
    $result = db_query($sql.$sql2);
    if ($result == false) {
      $this->erro_banco = str_replace("\n","",@pg_last_error());
      $this->erro_sql   = "Departamento de controle da conta não Excluído. Exclusão Abortada.\\n";
      $this->erro_sql .= "Valores : ".$sequencial;
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      $this->numrows_excluir = 0;
      return false;
    } else {
      if (pg_affected_rows($result) == 0) {
        $this->erro_banco = "";
        $this->erro_sql = "Departamento de controle da conta não Encontrado. Exclusão não Efetuada.\\n";
        $this->erro_sql .= "Valores : ".$sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_excluir = 0;
        return true;
      } else {
        $this->erro_banco = "";
        $this->erro_sql = "Exclusão efetuada com Sucesso\\n";
        $this->erro_sql .= "Valores : ".$sequencial;
        $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
        $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
        $this->erro_status = "1";
        $this->numrows_excluir = pg_affected_rows($result);
        return true;
      }
    }
  }
  // funcao do recordset
  public function sql_record($sql) {
    $result = db_query($sql);
    if (!$result) {
      $this->numrows    = 0;
      $this->erro_banco = str_replace("\n","",@pg_last_error());
      $this->erro_sql   = "Erro ao selecionar os registros.";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
    $this->numrows = pg_num_rows($result);
    if ($this->numrows == 0) {
      $this->erro_banco = "";
      $this->erro_sql   = "Record Vazio na Tabela:saltesdepart";
      $this->erro_msg   = "Usuário: \\n\\n ".$this->erro_sql." \\n\\n";
      $this->erro_msg   .=  str_replace('"',"",str_replace("'","",  "Administrador: \\n\\n ".$this->erro_banco." \\n"));
      $this->erro_status = "0";
      return false;
    }
    return $result;
  }
  // funcao do sql
  public function sql_query ($sequencial = null,$campos = "*", $ordem = null, $dbwhere = "") {

    $sql  = "select {$campos}";
    $sql .= "  from plugins.saltesdepart ";
    $sql .= "       inner join db_depart on db_depart.coddepto = saltesdepart.depart ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($sequencial)) {
        $sql2 .= " where saltesdepart.sequencial = $sequencial ";
      }
    } else if (!empty($dbwhere)) {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if (!empty($ordem)) {
      $sql .= " order by {$ordem}";
    }
    return $sql;
  }
  // funcao do sql
  public function sql_query_file ($sequencial = null, $campos = "*", $ordem = null, $dbwhere = "") {

    $sql  = "select {$campos} ";
    $sql .= "  from plugins.saltesdepart ";
    $sql2 = "";
    if (empty($dbwhere)) {
      if (!empty($sequencial)){
        $sql2 .= " where saltesdepart.sequencial = $sequencial ";
      }
    } else if (!empty($dbwhere)) {
      $sql2 = " where $dbwhere";
    }
    $sql .= $sql2;
    if (!empty($ordem)) {
      $sql .= " order by {$ordem}";
    }
    return $sql;
  }

  /**
   * Gera sql para buscar as contas nas quais o usuário informado tem permissão.
   * @param int $iUsuario id do usuário
   * @return string
   */
  public function sql_query_contas_usuario($iUsuario) {

    $sql =  " select k13_conta as saltes";
    $sql .= "   from saltes ";
    $sql .= "        left join  plugins.saltesdepart on saltes = k13_conta  ";
    $sql .= "        left join db_depusu on depart = coddepto ";
    $sql .= "  where (id_usuario = {$iUsuario} ";
    $sql .= "    and coddepto = ".db_getsession('DB_coddepto').")";
    $sql .= "     or depart is null; ";

    return $sql;
  }




}
