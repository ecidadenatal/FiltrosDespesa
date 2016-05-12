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

class PermissaoUsuarioConta {

  /**
   * Busca as contas nas quais o usuário informado tem permissão.
   * @param int $iUsuario id do usuário para busca.
   * @return array
   */
  public static function getContasUsuario($iUsuario) {

    $aContasComPermissao = array(0);
    $oDaoDepartamentos   = new cl_saltesdepart();
    $sSqlDepartamentos   = $oDaoDepartamentos->sql_query_contas_usuario($iUsuario);
    $rsDepartamentos     = $oDaoDepartamentos->sql_record($sSqlDepartamentos);

    if (!$rsDepartamentos) {
      return $aContasComPermissao;
    }

    $iTotalContas = $oDaoDepartamentos->numrows;
    for ($iConta = 0; $iConta < $iTotalContas; $iConta++) {
      array_push($aContasComPermissao, db_utils::fieldsMemory($rsDepartamentos, $iConta)->saltes);
    }

    return $aContasComPermissao;
  }

  /**
   * @param integer $iCodigoConta
   * @param array   $aDepartamentos
   *
   * @throws \Exception
   */
  public static function salvarVinculos($iCodigoConta, array $aDepartamentos) {

    if (!empty($iCodigoConta)) {

      $oDaoDepartamentoSaltes = new cl_saltesdepart();
      $oDaoDepartamentoSaltes->excluir(null, "saltes = {$iCodigoConta}");
      unset($oDaoDepartamentoSaltes);
    }

    foreach ($aDepartamentos as $iCodigoDepartamento) {

      $oDaoDepartamentoSaltes = new cl_saltesdepart();
      $oDaoDepartamentoSaltes->depart = $iCodigoDepartamento;
      $oDaoDepartamentoSaltes->saltes = $iCodigoConta;
      $oDaoDepartamentoSaltes->incluir(null);
      if ($oDaoDepartamentoSaltes->erro_status == 0) {
        throw new Exception ($oDaoDepartamentoSaltes->erro_msg);
      }
    }
  }

}