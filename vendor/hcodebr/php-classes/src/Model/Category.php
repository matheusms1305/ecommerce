<?php 
//todas nossas classes model vao extxender a classe model
namespace Hcode\Model;


use \Hcode\DB\Sql;
use \Hcode\Mailer;
use \Hcode\Model;

class Category extends Model{
    
    public static function listAll(){

        $sql = new Sql();
      
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

		Category::update_file();
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

		Category::update_file();
	} 

	public static function update_file()
	{

		$categories = Category::listAll();

		$html = [];

		foreach ($categories as $row)
		{
			array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT']. DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));

	}
}



?>