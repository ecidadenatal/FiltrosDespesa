<?php
/**
 * E-cidade Software Publico para Gest�o Municipal
 *   Copyright (C) 2015 DBSeller Servi�os de Inform�tica Ltda
 *                          www.dbseller.com.br
 *                          e-cidade@dbseller.com.br
 *   Este programa � software livre; voc� pode redistribu�-lo e/ou
 *   modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme
 *   publicada pela Free Software Foundation; tanto a vers�o 2 da
 *   Licen�a como (a seu crit�rio) qualquer vers�o mais nova.
 *   Este programa e distribu�do na expectativa de ser �til, mas SEM
 *   QUALQUER GARANTIA; sem mesmo a garantia impl�cita de
 *   COMERCIALIZA��O ou de ADEQUA��O A QUALQUER PROP�SITO EM
 *   PARTICULAR. Consulte a Licen�a P�blica Geral GNU para obter mais
 *   detalhes.
 *   Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral GNU
 *   junto com este programa; se n�o, escreva para a Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *   02111-1307, USA.
 *   C�pia da licen�a no diret�rio licenca/licenca_en.txt
 *                                 licenca/licenca_pt.txt
 */

class PermissaoUsuarioConta {

  /**
   * Busca as contas nas quais o usu�rio informado tem permiss�o.
   * @param int $iUsuario id do usu�rio para busca.
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