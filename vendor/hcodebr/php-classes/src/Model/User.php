<?php 
//todas nossas classes model vao extxender a classe model
namespace Hcode\Model;

use \Hcode\DB\Sql;
use HCODE\Mailer;
use \Hcode\Model;

class User extends Model{
    const SESSION = "User";
    const SECRET = "HcodePHP7_Secret";//16 caracteres
    const SECRET_IV = "HcodePhp7_Secret_IV";

    //metódo login beg /////////////////////////////////////////////////
    public static function login($login, $password){

            $sql = new Sql();
            
            $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(":LOGIN"=>$login));
            //busca toda a linha da coluna com o id especificado
            if(count($results) === 0){
        
                throw new \Exception("Usuário inexistente ou senha inválida.",1);
            }
            
            $data = $results[0];
            
            
            //////////////////VALIDA DADOS////////////////////
            if($data["despassword"] === $password)
            {

                $user = new User();
                $user->setData($data);
                
                $_SESSION[User::SESSION] = $user->getValues();
                

                return $user;
            }else {
            throw new \Exception("Usuário inexistente ou senha inválida");
            }

        }
        //metodo login end//////////////////////////////////////////////
    public static function verifyLogin($inadmin = true)
    {
       if
       (    //se existir a sessao    
            !isset($_SESSION[User::SESSION])
            ||
            //se a sessao nao estiver vazia
            !$_SESSION[User::SESSION]
            ||
            //se o login pode acessar a administracao
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
       ){
            header("Location: /admin/login");
            exit;
       }


    }
    public static function logout(){
        $_SESSION[User::SESSION] = null;

    }
    public static function listAll(){
        $sql = new Sql();
        //o usuario precisa de uma pessoa, tem um id person lá que é outra table, dai vamos unir as tabelas(inner join), usando o idperson que tem nas duas
        return  $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING (idperson) ORDER BY b.desperson");


    }
    //vamos criar um select, vamos chamar uma procedure pra inserir uma pessoa primeiro(vai ser gerado um id da pessoa), e vamos descobrir qual foi o id da pessoa criado na tabela de usuarios(chave estrangeira). Vamos pegar o id do usuario que retornou e fazer um select nos dados que estao no banco de dados, e trazer de volta
    public function save(){
        $sql = new Sql();
        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>$this->getdespassword(),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));

        $this->setData($results[0]);
    }
    public function get($iduser)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array
        (
            "iduser"=>$iduser
        ));
        
        $this->setData($results[0]);

    }
    public function update()
    {
        $sql = new Sql();
        $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
            ":iduser"=>$this->getiduser(),
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>$this->getdespassword(),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
        ));

        $this->setData($results[0]);
    }
    public function delete()
    {
        $sql = new sql();
        $sql->query("CALL sp_users_delete(:iduser)", array(
            ":iduser"=>$this->getiduser()
        ));
    }
    public static function getForgot($email)
	{

		$sql = new Sql();

		$results = $sql->select("
			SELECT *
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :email;
		", array(
			":email" => $email
		));

		if (count($results) === 0) {

			throw new \Exception("Não foi possível recuperar a senha.");
		} else {

			$data = $results[0];

			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser" => $data['iduser'],
				":desip" => $_SERVER['REMOTE_ADDR']
			));

			if (count($results2) === 0) {

				throw new \Exception("Não foi possível recuperar a senha.");
			} else {

				$dataRecovery = $results2[0];

				$code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

				$code = base64_encode($code);

			
				$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";
				

				$mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
					"name" => $data['desperson'],
					"link" => $link
				));

				$mailer->send();

				return $link;
			}
		}
	}

}



?>