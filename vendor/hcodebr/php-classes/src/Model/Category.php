<?php 
//todas nossas classes model vao extxender a classe model
namespace Hcode\Model;


use \Hcode\DB\Sql;
use \Hcode\Mailer;
use \Hcode\Model;

class Category extends Model{
    
    public static function listAll(){
        $sql = new Sql();
        //o usuario precisa de uma pessoa, tem um id person lá que é outra table, dai vamos unir as tabelas(inner join), usando o idperson que tem nas duas
        return  $sql->select("SELECT * FROM tb_categories ORDER BY descategory");


    }

    public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
			":idcategory" => $this->getidcategory(),
			":descategory" => $this->getdescategory()
			
		));

		$this->setData($results[0]);
	}

	public function get($idcategory)
	{
		$sql = new sql();

		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [':idcategory'=>$idcategory]);

		$this->setData($results[0]);

	}  

	public function delete()
	{
		$sql = new sql();

		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", ['idcategory'=>$this->getidcategory()]);
	} 
}



?>