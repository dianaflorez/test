<?php
namespace app\components;

use yii\base\Component;
use yii\helpers\Html;

class ValidationComponent extends Component{
	public $content;

	public function init(){
		parent::init();
		$this->content= 'Hello Yii 2.0';
	}

	public function display($content=null){
		if($content!=null){
			$this->content= $content;
		}
		echo Html::encode($this->content);
	}

	public function verificarid($id){
		$id = Html::encode($_GET["id"]);
		if(!is_numeric($id)){
			$id = '999999999999999';
		}
		$id = str_replace( "'", "''", $id);

		return $id;
	}

}
?>
