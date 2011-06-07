<?php 


/**
* @name         Site Class 
*
* @uses          Site
* @Package       Site
* @subpackage    Site
* @author        Mahdi Pedram
* @author		 Scott Haselton
* @category      Backend
* @access        Mexo Programming Team
* @version       1.0
* @since         2010 - 12 - 21  2:36 AM ( Tuesday )
* @copyright     Mexo LLC
*
*
*
*
*/


session_start(); // start up your PHP session! 

require_once(LiteFrame::GetFileSystemPath()."includes/SiteHelper.class.php");

class Site extends SiteHelper{
	
	
	/**
	 * 
	 * Contructor that dynamically handles types of parameters that could be passed.
	 * @access public
	 * @param mixed
	 * 
	 * @return mixed
	 */
	public function __construct(){
		$args = func_get_args();
		if( count($args) === 1 && $args[0] == 'jsonrpc' ){
			$this->jsonrpc();
			return;
		}	
		try{
			
			$this->process( func_get_args() );
			
		}catch ( SystemException $e ){			
			SiteHelper::Debug($e->__toString());
			LiteFrame::WriteLog( $e->__toString(), $e->getMessage() );
			exit();
			
		}catch( Exception $e){
			SiteHelper::Debug($e->__toString());
			LiteFrame::WriteLog( $e->__toString(), $e->getMessage() );
			exit();
			
		}
		
	} /* </ __construct >  */
  
	/**
	 * This starts the loading of the user objects that have been passed by the controller
	 * @access private
	 * @param mixed $args
	 * @return null
	 */
	private function process( $args ){

			parent::__construct();
			$requiredObjects = ObjectModule::GetArguments( $args );
			
			if(!LiteFrame::IsAjax()) {  $this->loadObjects( self::$staticObjects ); }
			
			if( !empty( $requiredObjects ) ){
			
				$this->loadObjects( $requiredObjects );
			
			}		
			
			$this->setObjectsForTemplate(); 
		
	}/* </ process > */
	
	/**
	 * 
	 * handles the normalized parameters that were passed into the 
	 * class by the controller and starts to load them into the framework
	 * individually
	 * 
	 * @param string $requiredObjects
	 * @access private
	 * 
	 */
	private function loadObjects( $requiredObjects ){
   
      foreach($requiredObjects as $obj){ 
      	
      	$this->generateObject( $obj, lcfirst($obj) );
      
      } 
		  
  } /* </ loadObjects >  */	
	
  	/**
	 * Taking each individual object that was passed in by the controller 
	 * (either from array, string, to multi string parameters) and trys in instantiation 
	 * them and load the into the frameworks/this class' element member.
	 * 
	 * @param string $className	Name of the class that was passed into the controller by the user to interface with the framework
	 * @param string $field	The same name of $className, only lowercased...necessary convention?  maybe...
	 * @access private
	 */
	private function generateObject($className, $field) {

		$this->siteObjects[$field] = new $className();
		
		self::$siteObjectsData[$field] = $this->siteObjects[$field]->getResults();
		
	}/* </ generateObject >  */	
	
	
	private function jsonrpc(){
			parent::__construct();
		  $post = LiteFrame::FetchPostVariable();
		  $api = new $post['api']();
		  self::$siteObjectsData[$post['api'] . "_" . $post['method']] = call_user_func_array(array($api, $post['method']), $post['config']);
		  $this->setObjectsForTemplate(); 
	}
	
}  /* </Site> */


?>