<?php 
session_start();
// o slim é responsavel pelo endereço (pelas rotas) da minha navegacao
require_once("vendor/autoload.php");
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Slim\Slim;
use \Hcode\Model\User;
use \Hcode\Model\Category;


$app = new Slim();


$app->config('debug', true);

$app->get('/', function() {
    
	$page = new Page();
	$page->setTpl("index");

});

$app->get('/admin', function() {
	User::verifyLogin();	

	$page = new PageAdmin();
	$page->setTpl("index");

});

$app->get('/admin/login', function(){
	$page = new PageAdmin(["header"=>false,"footer"=>false]);
	$page->setTpl("login");

});
$app->post('/admin/login', function(){
	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;
});
$app->get('/admin/logout', function(){
	User::logout();

	header("Location: /admin/login");
	exit;
});

//vai listar todos os usuarios
$app->get("/admin/users/", function(){
	User::verifyLogin();
	$page =  new PageAdmin();

	$users = User::listAll();
	

	$page->setTpl("users", array("users"=>$users));	

});
//cadastrar
//rota que vai receber os dados get no formulario e enviar via post para ser salvo em outra rota no caso
$app->get("/admin/users/create", function(){
	User::verifyLogin();
	$page =  new PageAdmin();
	$page->setTpl("users-create");
});	
//rota pra deletar o usuario usando o metodo delete
$app->get("/admin/users/:iduser/delete", function($iduser){
	User::verifyLogin();
	$user = new User();

	$user->get((int)$iduser);
	$user->delete();
	header("Location: /admin/users");
	exit;


});
//rota que vai receber os dados do banco e listar os dados do usuario pelo id dele
$app->get("/admin/users/:iduser", function($iduser){
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);

	$page = new PageAdmin();
	
	$page->setTpl("users-update", array("user"=>$user->getValues()));

});
//rota para salvar de fato os dados do usuario e cria-lo
$app->post("/admin/users/create", function(){
	User::verifyLogin();

	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
	
	// usamos os nomes dos campos no html igual os campos da tabela no banco de dados 
	$user->setData($_POST);
	$user->save();
	header("Location: /admin/users");
	exit;
	

});
//rota para salvar a edicao(note que so muda o metodo de envio)
$app->post("/admin/users/:iduser", function($iduser){
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");
	exit;
});

$app->get("/admin/forgot", function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");	

});

$app->post("/admin/forgot", function(){

	User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});

$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");	

});
$app->get("/admin/forgot/reset", function(){
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
});
$app->post("/admin/forgot/reset", function(){
	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::SetForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");


});

$app->get("/admin/categories", function(){

	User::verifyLogin();

	$categories = Category::listAll();
	
	$page = new PageAdmin();

	$page->setTpl("categories", ['categories'=>$categories]);

});

$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");

});

$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;

});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();
	
	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header("Location: /admin/categories");
	exit;
	
});

$app->get("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", ['category'=>$category->getValues()]);

});

$app->post("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();
	
	header("Location: /admin/categories");
	exit;
});






$app->run();

 ?>