<?php 
namespace Hcode;
//namespace rain pra instanciar a classe tpl
use Rain\Tpl;

// a classe page vai ser responsavel por criar nossos te
class Page { 
    private $tpl;
    private $options = [];
    private $defaults = [
        "header"=>true,
        "footer"=>false,
        "data"=>[]
    ];
    //(primeiro a ser executado)a funcao construct recebe dois parametros: 1- um array vazio, 2: um arry com uma chave, e vamos fundir os dois mais tarde
    public function __construct($opts = array(), $tpl_dir = "/views/")
    {
        //no construct eu recebo os dados pelo array $opts, depois passo eles pra valores no meu array options que tem a chave "data" e por fim uso o array_merge pra fundir os dois
        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
            "debug"         => false
           );
        Tpl::configure($config);
        
        $this->tpl = new Tpl;
        //a funcao setData faz os assigns dos dados do meu objeto tpl, pra isso eu passo pra ela o array options, que é o resultado da soma do array $default e do $opts 
        $this->setData($this->options["data"]);
        //escreve o header html(ele busca pelo metodo configure, meu arquivo header no diretorio especificado dinamicamente no parametro $config)
        if($this->options["header"] === true)  $this->tpl->draw("header");
           
        
   
    }
     //cria nossos 'assigns' 
    public function setData($data = array()){
        foreach($data as $key => $value){
            $this->tpl->assign($key, $value);
        }
    }
    //busca o nome do template que sera carregado, passando o nome do arquivo e retorna a pagina
    public function setTpl($name, $data = array(), $returnHTML = false){
        $this->setData($data);
        return $this->tpl->draw($name, $returnHTML);
    }
  



    //ultimo a ser executado
    //footer do site
    public function __destruct()
    {
        if($this->options["footer"] === true) $this->tpl->draw("footer");
    }

}

?>