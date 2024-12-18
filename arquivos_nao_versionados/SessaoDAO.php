<?php

Zend_Loader::loadClass("ConexaoSessao");

class SessaoDAO extends ConexaoSessao {

    public function __construct() {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function __destruct() {
        //parent::__destruct();
    }


    public function logQuery($query){    
        $logFile = '/var/log/apache2/sessao.log'; // Defina o caminho para o arquivo de log
        $currentDateTime = date('Y-m-d H:i:s');
        $logMessage = "[{$currentDateTime}] Query: {$query}\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public function getSessaoByUsuario_() {
        try {
            $this->db = Zend_Registry::get("sessao");
            $sql = new Zend_Db_Expr("SELECT arquivo AS Conteudo, status, gravando FROM arquivo WHERE idfusr='" . Site::getUserSystem()->getIdfusr() . "' AND status <>'D' AND namespace='" . SESSION_NAMESPACE . "'");
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - getSessaoByUsuario');
        }
    }

    public function getSessaoByUsuario() {
        try {
            $this->db = Zend_Registry::get("sessao");
            $userId = Site::getUserSystem()->getIdfusr();
    
            if (!$userId) {
                throw new Exception("Usuário não encontrado na sessão!");
            }
    
            $sql = new Zend_Db_Expr("SELECT arquivo AS Conteudo, status, gravando FROM arquivo WHERE idfusr='" . $userId . "' AND status <>'D' AND namespace='" . SESSION_NAMESPACE . "'");
            $this->logQuery($query);
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } 
        catch (Exception $ex) {
            //echo $ex->getMessage();
            error_log("Erro em getSessaoByUsuario: " . $ex->getMessage(), 0);
            die(' - getSessaoByUsuario');
        }
    }

    public function getSessaoByProcesso() {
        try {
            $this->db = Zend_Registry::get("sessao");
            $sql = new Zend_Db_Expr("SELECT status, namespace, idfusr, arquivo AS Conteudo FROM arquivo WHERE status <>'D' AND namespace='" . SESSION_NAMESPACE . "' AND idfusr <> '" . Site::getUserSystem()->getIdfusr() . "'");
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - getSessaoByProcesso');
        }
    }

    public function getTodosArquivos($idfemp) {
        try {
            $sql = new Zend_Db_Expr("SELECT id, arquivo AS Conteudo, status, idfemp, idfusr, namespace  FROM arquivo WHERE status <>  'D' AND idfemp='" . $idfemp . "' ");
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - getTodosArquivos');
        }
    }

    public function getArquivos() {
        try {
            $sql = new Zend_Db_Expr("SELECT arquivo AS Conteudo, idfemp, idfusr, namespace, dathorics, datultalt, status FROM arquivo");
            $this->logQuery($sql);
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - getArquivos');
        }
    }

    public function getArquivo($idfusr = null, $namespace = SESSION_NAMESPACE) {
        $this->db = Zend_Registry::get("sessao");
        try {
            if ($idfusr != null) {
                $sql = new Zend_Db_Expr("SELECT arquivo AS Conteudo, status FROM arquivo WHERE idfusr='" . $idfusr . "' AND namespace='" . $namespace . "' ");
            } 
            else {
                $sql = new Zend_Db_Expr("SELECT arquivo AS Conteudo, status FROM arquivo WHERE idfusr='" . Site::getUserSystem()->getIdfusr() . "' AND namespace='" . $namespace . "'");
            }
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - getArquivo');
        }
    }

    public static function apagarArquivoMarcadoExclusao() {
        $sessaoDAO = new SessaoDAO();
        $arquivo = $sessaoDAO->getArquivo();
        if ($arquivo['status'] == 'D') {
            $sessaoDAO->deletarArquivo();
        }
        $sessaoDAO->__destruct();
    }

    public function alterarArquivo($arquivo) {
        $this->db = Zend_Registry::get("sessao");
        try {
            $sql = new Zend_Db_Expr("UPDATE arquivo SET arquivo = ? 
                WHERE idfusr = ?  AND namespace='" . SESSION_NAMESPACE . "'");

            $stmt = $this->db->prepare($sql);

            $stmt->execute(array($arquivo, Site::getUserSystem()->getIdfusr()));
            return true;
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(- "alterarArquivo");
        }
    }

	public function getEmGravacao() {
        try {
            $this->db = Zend_Registry::get("sessao");
            $sql = new Zend_Db_Expr("SELECT gravando FROM arquivo WHERE idfusr='" . Site::getUserSystem()->getIdfusr() . "' AND namespace='" . SESSION_NAMESPACE . "'");
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $retorno = $stmt->fetch(PDO::FETCH_ASSOC);
            return $retorno['gravando'];
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - getEmGravacao');
        }
    }
    public function marcarEmGravacao($flag) {
        try {
            $sql = new Zend_Db_Expr("UPDATE arquivo SET gravando ='" . $flag . "' 
                WHERE idfusr='" . Site::getUserSystem()->getIdfusr() . "' AND status='A' AND namespace='" . SESSION_NAMESPACE . "'");
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return true;
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - marcarEmGravacao');
        }
    }
    
    public function alterarStatusArquivo($status) {
        try {
            $sql = new Zend_Db_Expr("UPDATE arquivo SET status ='" . $status . "' 
                WHERE idfusr='" . Site::getUserSystem()->getIdfusr() . "' AND status='A' AND namespace='" . SESSION_NAMESPACE . "'");
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return true;
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - alterarStatusArquivo');
        }
    }

    public function deletarArquivo($namespace = SESSION_NAMESPACE) {
        try {
            $sql = new Zend_Db_Expr("DELETE FROM arquivo WHERE idfusr = ?  AND namespace = ? ");
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array(Site::getUserSystem()->getIdfusr(), $namespace));
            return true;
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die();
        }
    }

    public function deletarArquivoPorUsuario($idfusr, $namespace = SESSION_NAMESPACE) {
        try {
            $sql = new Zend_Db_Expr("DELETE FROM arquivo WHERE idfusr = ?  AND namespace = ? ");
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array($idfusr, $namespace));
            return true;
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - deletarArquivoPorUsuario');
        }
    }

    public function deletarTodosArquivos($idfemp) {
        try {
            $sql = new Zend_Db_Expr("DELETE FROM arquivo WHERE idfemp='" . $idfemp . "'");
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return true;
        } 
        catch (Exception $ex) {
            throw $ex;
        }
    }

    public function deletaPropostasStatusD() {
        try {
            $sql = new Zend_Db_Expr("DELETE FROM arquivo WHERE status='D' ");
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return true;
        } 
        catch (Exception $ex) {
            throw $ex;
        }
    }
    public function deletarSessoes() {
        try {
            $sql = new Zend_Db_Expr("DELETE FROM sessoes ");
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return true;
        } 
        catch (Exception $ex) {
            throw $ex;
        }
    }

    public function criaArquivo($arquivo) {
        $this->db = Zend_Registry::get("sessao");
        try {
            $sql = new Zend_Db_Expr("INSERT INTO arquivo(idfusr, idfemp, status, namespace, arquivo, cor) 
                                        VALUES(?, ?, ?, ?, ?,?)");
            $stmt = $this->db->prepare($sql);
            // print_R(array(Site::getUserSystem()->getIdfusr(), Site::getUnidade()->getIdfemp(), 'A', $arquivo ))
            // Cores do CNWeb
            $cor = $this->getProximaBgColorPorUsuario();
            $stmt->execute(array(Site::getUserSystem()->getIdfusr(), Site::getUnidade()->getIdfemp(), 'A', SESSION_NAMESPACE, $arquivo, $cor));
            return true;
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - criaArquivo');
        }
    }

    public function gravarLogVortice($pProcesso, $pIdfusr, $pCodcli, $pNumppt, $pLink) {
        try {
            $sql = new Zend_Db_Expr("INSERT INTO loglink(PROCVORT, IDFUSR, CODCLI, NUMPPT, LINK)
                                    VALUES(?, ?, ?, ?, ?)");
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array($pProcesso,$pIdfusr,$pCodcli,$pNumppt,$pLink));
            return true;
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - gravarLogVortice');
        }
    }
    public function gravarLogWebService($pIdfusr, $pNamespace, $pWebservice, $pParams, $pResult) {
        try {
            $sql = new Zend_Db_Expr("INSERT INTO logwebservice(idfusr, namespace, params, result)
                                    VALUES(?, ?, ?, ?)");
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array($pIdfusr, $pNamespace, $pWebservice, $pParams, $pResult));
            return true;
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - gravarLogWebservice');
        }
    }
    public function getProximaBgColorPorUsuario() {
        $this->db = Zend_Registry::get("sessao");
        try {
            $sql = new Zend_Db_Expr("
                SELECT 
                    cor.id AS cor, 
                    COUNT(arquivo.cor) AS qtd
                FROM cor 
                LEFT JOIN arquivo 
                    ON cor.id = arquivo.cor 
                        AND arquivo.idfusr='" . Site::getUserSystem()->getIdfusr() . "'
                        AND status <> 'D'
                GROUP BY 
                    cor.id 
                ORDER BY 
                    qtd, cor.id
                LIMIT 1 ");

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $bgcolor = $stmt->fetch(PDO::FETCH_ASSOC);

            // Cores do CNWeb
            $sessao = SessionWrapper::getInstance();
            if ($sessao->getSessVar('pBgC') == "") {
                $sessao->setSessVar('pBgC', $bgcolor['cor']);
            }

            return $bgcolor['cor'];
        } 
        catch (Exception $ex) {
            echo $ex->getMessage();
            die(' - getProximaBgColorPorUsuario');
        }
    }

}