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
    public function __construct($opts = array())
    {
        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/",
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache",
            "debug"         => false
           );
        Tpl::configure($config);
        
        $this->tpl = new Tpl;
        
        $this->setData($this->options["data"]);
        
        $this->tpl->draw("header");
    }
    //cria nossos 'assigns' 
    public function setData($data = array()){
        foreach($data as $key => $value){
            $this->tpl->assign($key, $value);
        }
    }
    //nome da pagina, dados, e o return do metodo draw
    public function setTpl($name, $data = array(), $returnHTML = false){
        $this->setData($data);
        return $this->tpl->draw($name, $returnHTML);
    }
  



    //ultimo a ser executado
    public function __destruct()
    {
        $this->tpl->draw("footer");
    }

}

?>