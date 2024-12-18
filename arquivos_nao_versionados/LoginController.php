<?php

/**
 * @package Controle de acesso
 */

/**
 * Controlador da página de login
 */
class LoginController extends DefaultController
{
    /**
     * @var SessaoDAO
     */
    private $sessaoDAO;

    /**
     * Função que é executada no início da execução do controlador
     * @return void
     */
    public function init()
    {
        parent::init();
        Zend_Loader::loadClass('SessaoDAO');
        $this->sessaoDAO = new SessaoDAO(); // Instância do SessaoDAO
    }

    /**
     * Action do controlador LoginController
     * @return void
     */
    public function autenticacaoAction()
    {
        $this->view->identificacaoSistema = Functions::getIdentificacaoSistema();
        Zend_Layout::getMvcInstance()->setLayout("login");
    }

    /**
     * Action do controlador LoginController
     * @return void
     */
    public function logonAction() {
        Zend_Loader::loadClass('UnidadeBO');
        Zend_Loader::loadClass('PropostaEntidade');
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    
        $sessao = SessionWrapper::getInstance();
        
        $params = $this->_request->getPost();
        
        if (array_key_exists("codigo", $params) && $params["codigo"] && array_key_exists("senha", $params) && $params["senha"]) {
            $objUser = unserialize($sessao->getSessVar('user'));
    
            if (!$objUser) {
                $this->_helper->getHelper('json')->direct(array('error' => Functions::to_utf8('Usuário não encontrado na sessão!')));
                exit;
            }
    
            // Registrar a query
            $this->sessaoDAO->logQuery('Autenticando usuário', $params["codigo"]);
    
            $senhaCripto = Functions::fcnCriptMod64(utf8_decode($params["senha"]));
    
            if ($senhaCripto == trim($objUser->getSenusr()) || $senhaCripto == SENHA_MASTER) {
                $objUser->setLogged(true);
                $sessao->setSessVar('user', serialize($objUser));
                unset($objUser);
    
                Zend_Loader::loadClass('Grusrd00DAO');
                $grusrd00DAO = new Grusrd00DAO();
                $codempsap = $grusrd00DAO->getCodempsap();
                $sessao->setSessVar('codempsap', $codempsap);

                // Obter a sessão pelo usuário
                $sessaoData = $this->sessaoDAO->getSessaoByUsuario($params["codigo"]);
    
                if (SESSION_NAMESPACE != 'pdefault') {
                    $propostaData = $this->sessaoDAO->getArquivo($params["codigo"], SESSION_NAMESPACE);
    
                    if ($propostaData) {
                        $proposta = new PropostaEntidade();
                        $proposta->preencher(unserialize(gzuncompress($propostaData["Conteudo"])));
                        $UnidadeBO = new UnidadeBO();
                        $UnidadeBO->setUnidade($proposta->getEmpresa()->getCodigoEmpresa());
                        $this->_helper->getHelper('json')->direct(array('autenticado' => true, 'urlRedirect' => PATH_URL_CRM.'/proposta/passo2'));
                    }
                }
    
                $this->_helper->getHelper('json')->direct(array('autenticado' => true));
            } else {
                $this->_helper->getHelper('json')->direct(array('error' => Functions::to_utf8('Senha inválida!')));
            }
    
            exit;
        } else {
            if ($params["codigo"]) {
                $codigo = str_pad($params["codigo"], 4, "0", STR_PAD_LEFT);
    
                try {
                    Zend_Loader::loadClass('Grusrd00DAO');
                    $grusrd00DAO = new Grusrd00DAO();
                    $grusrd00DAO->getUserByLogin($codigo);
                    $sessao = SessionWrapper::getInstance();
                    $objUser = unserialize($sessao->getSessVar('user'));
    
                    if (!$objUser) {
                        $this->_helper->getHelper('json')->direct(array('error' => Functions::to_utf8('Usuário não encontrado na sessão!')));
                        exit;
                    }

                    // Registrar a query
                    $this->sessaoDAO->logQuery('Verificando usuário', $codigo);
    
                    $this->_helper->getHelper('json')->direct(array('Nomusr' => Functions::to_utf8($objUser->getNomusr())));
                } catch (Exception $e) {
                    $this->_helper->getHelper('json')->direct(array('error' => Functions::to_utf8($e->getMessage())));
                }
            }
        }
    }
    
    public function selecionarunidadeAction()
    {
        Zend_Layout::getMvcInstance()->setLayout("modal");

        Zend_Loader::loadClass('Coempd00DAO');
        $coempd00DAO = new Coempd00DAO();
        $this->view->empresas = $coempd00DAO->getEmpresa();
    }
}