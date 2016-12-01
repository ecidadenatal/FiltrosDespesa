<?php
/**
 * E-cidade Software Publico para Gestão Municipal
 *   Copyright (C) 2015 DBSeller Serviços de Informática Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa é software livre; você pode redistribuí-lo e/ou
 *   modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a versão 2 da
 *   Licença como (a seu critério) qualquer versão mais nova.
 *   Este programa e distribuído na expectativa de ser útil, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia implícita de
 *   COMERCIALIZAÇÃO ou de ADEQUAÇÃO A QUALQUER PROPÓSITO EM
 *   PARTICULAR. Consulte a Licença Pública Geral GNU para obter mais
 *   detalhes.
 *   Você deve ter recebido uma cópia da Licença Pública Geral GNU
 *   junto com este programa; se não, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   Cópia da licença no diretório licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt
 */

/**
 * Controle de permissões de empenho /Dotacoes do usuario
 * Class PermissaoUsuarioEmpenho
 */
class PermissaoUsuarioEmpenho {

  /**
   * Verifica permissão de manutenção de Dotações
   */
  const PERMISSAO_MANUTENCAO = "M";

  /**
   * Verifica permissao de consulta das Dotações
   */
  const PERMISSAO_CONSULTA = "C";

  /**
   * Permissão para consulta e manutenção
   */
  const PERMISSAO_MANUTENCAO_CONSULTA = "";


  /**
   * Retorna todas as dotações que o usuário possui
   * @param int    $iUsuario Código do Usuário
   * @param int    $iAno Ano da permissão
   * @param string $sTipoPermissao
   * @param string $sWhere clausula adicional de where
   * @return array codigo das dotações
   */
  public static function getDotacoesUsuario($iUsuario, $iAno, $sTipoPermissao, $sWhere = '') {

    $aDotacoes         = array(0);
    $oPermissaoUsuario = new cl_permusuario_dotacao($iAno, $iUsuario, null, null, null, $sTipoPermissao, null, $sWhere);
    
    $rsQuery           = $oPermissaoUsuario->recordset;
    if ($rsQuery) {

      $iNumeroLinhas = pg_num_rows($rsQuery);

      for($i = 0; $i < $iNumeroLinhas; $i++) {

        $oDotacao = db_utils::fieldsMemory($rsQuery, $i);
        array_push($aDotacoes, $oDotacao->o58_coddot);
      }
    }
    return $aDotacoes;
  }

  /**
   * Verifica se o usuário tem permissão para efetuar manutenções no empenho Informado
   * @param EmpenhoFinanceiro $oEmpenho Instância do Empenho
   * @param                   $iUsuario Código do usuário
   * @param                   $iAno ano da permissão
   * @return bool
   */
  public static function temPermissaoNoEmpenho(EmpenhoFinanceiro $oEmpenho, $iUsuario, $iAno) {

    if (empty($oEmpenho)) {
      return false;
    }

    $aDotacoes = PermissaoUsuarioEmpenho::getDotacoesUsuario($iUsuario, $iAno, PermissaoUsuarioEmpenho::PERMISSAO_MANUTENCAO);
    return in_array($oEmpenho->getDotacao()->getCodigo(), $aDotacoes);
  }


  /**
   * Retorna uma string sql indicando quais dotações o usuário possui permissão
   * @param $iUsuario
   * @param $iAnoUsu
   * @param $sTipoPermissao
   * @return string
   */
  public static function getDotacoesPorAnoDoUsuario($iUsuario, $iAnoUsu, $sTipoPermissao = "") {

    if (!empty($sTipoPermissao)) {
      $sTipoPermissao = "db20_tipoperm='{$sTipoPermissao}' and";
    }

    $sqle = " SELECT 'USUARIO' AS TIPO ,DB_PERMEMP.*
                FROM DB_PERMEMP
                     INNER JOIN DB_USUPERMEMP U ON U.DB21_CODPERM = DB20_CODPERM AND U.DB21_ID_USUARIO = $iUsuario
                     LEFT OUTER JOIN DB_DEPARTORG D ON DB20_ORGAO = D.DB01_ORGAO AND D.DB01_ANOUSU = {$iAnoUsu}
              WHERE {$sTipoPermissao}  DB20_ANOUSU = {$iAnoUsu}";

    $sqle .= "
         UNION
         SELECT 'SETOR' AS TIPO ,DB_PERMEMP.*
          FROM DB_PERMEMP

         INNER JOIN DB_DEPUSU  ON ID_USUARIO = $iUsuario
         INNER JOIN DB_DEPUSUEMP  ON DB22_CODPERM = DB20_CODPERM AND DB22_CODDEPTO = CODDEPTO
         WHERE {$sTipoPermissao} DB20_ANOUSU = $iAnoUsu
        ";

    $rsDadosPermissaoUsuario = db_query($sqle);
    $iTotalPermissoes        = pg_numrows($rsDadosPermissaoUsuario);
    if ($iTotalPermissoes == 0) {
      return '(orgao = -1)';
    }
    $aListasPermissoes = array();
    for ($i = 0; $i < $iTotalPermissoes; $i ++) {

      $aCamposWhere  = array();
      $oDadosDotacao = db_utils::fieldsmemory($rsDadosPermissaoUsuario, $i);

      if ($oDadosDotacao->db20_orgao > 0) {
        $aCamposWhere[] = " o58_orgao = {$oDadosDotacao->db20_orgao}";
      }

      if ($oDadosDotacao->db20_unidade > 0) {
        $aCamposWhere[] = "o58_unidade = {$oDadosDotacao->db20_unidade}";
      }

      if ($oDadosDotacao->db20_funcao > 0) {
        $aCamposWhere[] = " o58_funcao = {$oDadosDotacao->db20_funcao}";
      }
      if ($oDadosDotacao->db20_subfuncao > 0) {
        $aCamposWhere[] = " o58_subfuncao = {$oDadosDotacao->db20_subfuncao}";
      }
      if ($oDadosDotacao->db20_programa > 0) {
        $aCamposWhere[] = " o58_programa = {$oDadosDotacao->db20_programa} ";
      }
      if ($oDadosDotacao->db20_projativ > 0) {
        $aCamposWhere[] = " o58_projativ = {$oDadosDotacao->db20_projativ}";
      }

      if ($oDadosDotacao->db20_codele > 0) {

        $aCamposWhere[] = " o58_codele = {$oDadosDotacao->db20_codele}";
      }

      if ($oDadosDotacao->db20_codigo > 0) {
        $aCamposWhere[] = " o58_codigo = {$oDadosDotacao->db20_codigo} ";
      }

      $aListasPermissoes[] = "(".implode(" and ", $aCamposWhere).")";
    }

    $sClausulaRestricao = "(".implode(" OR ", $aListasPermissoes)." )";
    return $sClausulaRestricao;
  }
}
