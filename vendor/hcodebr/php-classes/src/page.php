<?php 
namespace Hcode;
//namespace rain pra instanciar a classe tpl
use Rain\Tpl;

class Page {
    private $tpl;
    private $options = [];
    private $defaults = [
        "data"=>[]
    ];
    //primeiro a ser executado
    public function __construct($opts = array(), $tpl_dir = "/views/")
    {
        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
            "debug"         => false
           );
        Tpl::configure($config);
        
        $this->tpl = new Tpl;
        
        $this->setData($this->options["data"]);
        //escreve o header html
        $this->tpl->draw("header");
    }
    //cria nossos 'assigns' 
    public function setData($data = array()){
        foreach($data as $key => $value){
            $this->tpl->assign($key, $value);
        }
    }
    //nome da pagina, dados, corpo da pagina
    public function setTpl($name, $data = array(), $returnHTML = false){
        $this->setData($data);
        return $this->tpl->draw($name, $returnHTML);
    }
  



    //ultimo a ser executado
    //footer do site
    public function __destruct()
    {
        $this->tpl->draw("footer");
    }

}

?>