<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function __construct($application)
    {
        parent::__construct($application);

        $this->_loadDefaults();
        $this->_getDB();
        self::_configSessao();
        $this->_getParameters();
        $this->_verificaNavegadorValido();
       // $this->sendResponse();
    }

    protected function _loadDefaults()
    {
        Zend_Loader::loadClass('CRMPlugin');
        Zend_Loader::loadClass("Site");
        Zend_Loader::loadClass("LogError");
        Zend_Loader::loadClass("SessionWrapper");
        Zend_Loader::loadClass("Zend_Controller_Front");
        Zend_Loader::loadClass("DefaultController");
        Zend_Loader::loadClass("User");
        Zend_Loader::loadClass("Functions");
        Zend_Loader::loadClass("Unidade");
        Zend_Loader::loadClass('Entidade');
		Zend_Loader::loadClass('Temporizador');
		Zend_Loader::loadClass('CrmPlugin');
    }

    protected function _getDB()
    {
        $params = $this->_options["resources"]["db"]["params"];
        $adapter = Zend_Db::factory($this->_options["resources"]["db"]["adapter"], $params);
        Zend_Registry::set('db', $adapter);

        // mysql  - sessao
        $adapter = Zend_Db::factory($this->_options["sessao"]["adapter"], $this->_options["sessao"]["config"]);
        Zend_Registry::set('sessao', $adapter);

        // vortice
       /* $params = $this->_options["vorticedb"]["db"]["params"];

        $adapter = Zend_Db::factory($this->_options["vorticedb"]["db"]["adapter"], $params);
        Zend_Registry::set('vorticedb', $adapter);*/
    }
    
 	public static function connectSessao()
    {
    	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        // mysql  - sessao
        $adapter = Zend_Db::factory($config->sessao->adapter, $config->sessao->config->toArray());
        Zend_Registry::set('sessao', $adapter);
    }

    protected function _getParameters()
    {
        //Número de resultados por página do grid
        define('NUMERO_DE_LINHAS_POR_PAGINA', $this->_options["parameters"]["grid"]["numero_de_linhas_por_pagina"]);
        define('CAMINHO_FOTO_PRODUTO', $this->_options["parameters"]["paths"]["foto"]);
        define('CAMINHO_PDF_PRODUTO', $this->_options["parameters"]["paths"]["pdf"]);
        if (isset($this->_options["parameters"]["pathslocais"]))    {
            define('CAMINHOLOCAL_PDF_PRODUTO', $this->_options["parameters"]["pathslocais"]["pdf"]);
        }
        define('EMAIL_REPORT_ERROR', $this->_options["parameters"]["log"]["report"]);
        define('WS_IMPOSTO_WSDL', $this->_options["ws"]["imposto"]["wsdl"]);
        define('WS_FRETE_WSDL', $this->_options["ws"]["frete"]["wsdl"]);
        define('WS_SAP_WSDL', $this->_options["ws"]["sap"]["wsdl"]);
        define('WS_EXPRESSAO_WSDL', $this->_options["ws"]["expressao"]["wsdl"]);
        define('WS_DOCUMENTOS_WSDL', $this->_options["ws"]["documentos"]["wsdl"]);
        define('WS_BOLETO_URL', $this->_options["ws"]["boleto"]["url"]);
        if ( array_key_exists('boletox' , $this->_options["ws"]) ) {
            define('WS_BOLETOX_URL', $this->_options["ws"]["boletox"]["url"]);
        } else {
            define('WS_BOLETOX_URL', '' );
        }
        if (isset($this->_options["parameters"]["email"])) {
            define('EMAIL_HOST_SMTP', $this->_options["parameters"]["email"]["smtp"]);
            define('EMAIL_USERNAME', $this->_options["parameters"]["email"]["username"]);
            define('EMAIL_PASSWORD', $this->_options["parameters"]["email"]["password"]);
        }
        define('SCHEMA', $this->_options["resources"]["db"]["params"]["schema"]);
        define('SCHEMA_WEB', $this->_options["resources"]["db"]["params"]["schemaWeb"]);
        
        define('SCHEMA_WMS', $this->_options["resources"]["db"]["params"]["schemaWms"]);
        define('SCHEMA_CRM', $this->_options["vorticedb"]["db"]["params"]["schema"]);
        define('SCHEMA_DOCS', $this->_options["resources"]["db"]["params"]["schemaDocs"]);
		if (isset($this->_options["resources"]["db"]["params"]["schemaHist"])){
			define('SCHEMA_HIST', $this->_options["resources"]["db"]["params"]["schemaHist"]);
		}
        if ( array_key_exists("frete", $this->_options["parameters"]) ) {
            define('VALOR_MINIMO_SEDEX', $this->_options["parameters"]["frete"]["val_min_sedex"]);
        } else {
            define('VALOR_MINIMO_SEDEX', 0 );
        }
        
        define('FRETE_TAXA_CUBAGEM', 300 );
        define('FRETE_PERCENTUAL_CUBAGEM', 1.1 );
        
        define('ACS_PPT_ATENDIMENTO_ATIVO_CPR', 'CWPED1D4' );
        define('ACS_PPT_ATENDIMENTO_ATIVO_RVD', 'CWPED1D5' );

        define('IDFUSRVED_ATENDIMENTO_ATIVO_CPR', '3777' );
        define('IDFUSRVED_ATENDIMENTO_ATIVO_RVD', '3778' );
        
        define('CARTEIRA_ATENDIMENTO_ATIVO_CPR', '' );
        define('CARTEIRA_ATENDIMENTO_ATIVO_RVD', '' );

        define('ACS_HIDE_CHAR', 'GRDAD100' );

        if(array_key_exists("senhaMaster", $this->_options["parameters"])){
        	 define('SENHA_MASTER', $this->_options["parameters"]["senhaMaster"]);
        }else{
        	 define('SENHA_MASTER', 'CAS)%Y');
        }
        define('WS_LINKPAGAMENTO_URL', $this->_options["ws"]["linkpagamento"]["url"]);
        define('WS_BOLETOITAU_URL', $this->_options["ws"]["boletoitau"]["url"]);
        define('WS_PIX_URL', $this->_options["ws"]["pix"]["url"]);

		define('WS_FRETE_LINCROS', $this->_options["ws"]["frete"]["lincros"]);
		define('CUBAGEM_MESMA_EMBALAGEM_FRETE_LINCROS', 0);
        define('PESO_MESMA_EMBALAGEM_FRETE_LINCROS', 0);        
        
        define('ACS_END_ENTREGA', 'CWPED113' );
        define('SCHEMA_CEP', $this->_options["resources"]["db"]["params"]["schemaCep"]);
    }
//
//    protected function sendResponse(){
//    	$response = new Zend_Controller_Response_Http();
//    	$response->setHeader("Accept-Encoding", "gzip, deflate");  
//		$response->setHeader("X-Compression", "gzip");  
//		$response->setHeader("Content-Encoding", "gzip");  
//		$response->setHeader("Content-type", "text/xml");  
//		$response->setHeader("Cache-Control", "must-revalidate");
//		$filter = new Zend_Filter_Compress('Gz');
//		$compressed = $filter->filter($conteudo);
//		$response->setBody($compressed);
//		$response->sendResponse();
//    }
//    
    
    protected static function _configSessao()
    {
        Zend_Loader::loadClass("Zend_Db_Table_Abstract");
        Zend_Loader::loadClass("Zend_Session_SaveHandler_DbTable");
        $dbSessao = Zend_Registry::get("sessao");
        //	$dbSessao->getConnection();

        Zend_Db_Table_Abstract::setDefaultAdapter($dbSessao);

        $configSession = array(
            'name' => 'sessoes',
            'primary' => 'id',
            'modifiedColumn' => 'modificacao',
            'dataColumn' => 'dados',
            'lifetimeColumn' => 'tempovida'//,
        );

        $sessionHandler = new Zend_Session_SaveHandler_DbTable($configSession);

        $sessionOptions = array('strict' => 'on',
            //	  'save_handler' => 'user',
            //	  'name' => 'PHPSESSID',
            'gc_probability' => 1,
            'gc_divisor' => 100,
            //	   'gc_maxlifetime' => 300000,
            'hash_function' => 1,
            'hash_bits_per_character' => 4,
            'use_only_cookies' => 'on'//,
        //    'use_trans_sid' => 'on'
        );
        $seconds = 14400;
       // Zend_Session::rememberMe( $seconds );
        $sessionHandler->setLifetime( $seconds )
                ->setOverrideLifetime(true);

        Zend_Session::setOptions($sessionOptions);
        Zend_Session::setSaveHandler($sessionHandler);
    }
    
    protected function _verificaNavegadorValido(){
	  $front = Zend_Controller_Front::getInstance();
	  
	  $naoVerifica = stristr($_SERVER['REQUEST_URI'], '/fechamento/') || stristr($_SERVER['REQUEST_URI'], '/webservices') || stristr($_SERVER['REQUEST_URI'], '/wsrf/') || stristr($_SERVER['REQUEST_URI'], '/financiamento/') || stristr($_SERVER['REQUEST_URI'], '/ws-receitafederal/') || stristr($_SERVER['REQUEST_URI'], '/ws-sintegra/');
    	$browsers = $this->_options["parameters"]["browsers"];
    	if(!(preg_match('/('.$browsers.')/i',$_SERVER['HTTP_USER_AGENT']))){
    		
    		//header("Location: http://".$_SERVER['HTTP_HOST']."/error/browserinvalido/");
    		if(!($naoVerifica)){
    			die('Este browser está definido inv&aacute;lido para o conagemweb');
    		}

        }	
	//  print_R(stripos($_SERVER['REQUEST_URI'], '/vortice/'));die('fim');
	  $urlRotaSemProcesso = (stripos($_SERVER['REQUEST_URI'], '/vortice/') === 0 || stripos($_SERVER['REQUEST_URI'], '/fechamento/') === 0 || stripos($_SERVER['REQUEST_URI'], '/webservices') === 0 || stripos($_SERVER['REQUEST_URI'], '/wsrf/') === 0 || stripos($_SERVER['REQUEST_URI'], '/financiamento/' )=== 0 ||stripos($_SERVER['REQUEST_URI'], '/ws-receitafederal/') === 0 ||stripos($_SERVER['REQUEST_URI'], '/ws-sintegra/') === 0);
	//  var_dump(stripos('/pedido/aceitefinan', $_SERVER['REQUEST_URI']));die; 
	  if($urlRotaSemProcesso){
	  	//	print_r($_GET);die('entrou');
	  		$rotaSemProcesso = true;
	  }else{
	  	   $router = $front->getRouter();
	       $rotaComProcesso = new Zend_Controller_Router_Route(
		        ':proccrm/:controller/:action/*',
		        array(
		            'proccrm' => 'default',
		            'controller' => 'index',
		            'action'    => 'index'
		        )/*,
		        array('proccrm' => '\d+')*/
		   );
	       $router->addRoute('default', $rotaComProcesso);
	       $rotaSemProcesso = false;
	  }
    	
    	$front->registerPlugin(new CRMPlugin($rotaSemProcesso));	
	} 
    
}