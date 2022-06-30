<?php 

Class Database
{
	
	/*
	*
	* This is the database class
	*/
	private static $con;
	private $DB_TYPE 	= "mysql";
	private $DB_HOST 	= "localhost";
	public $DB_NAME 	= "teams01";
	private $DB_USER 	= "root";
	private $DB_PASS 	= "";

	public function __construct()
	{
		try{
 
			//$string = $this->DB_TYPE . ":host=". $this->DB_HOST .";dbname=". $this->DB_NAME;
			$string = $this->DB_TYPE . ":host=". $this->DB_HOST ;
			self::$con = new PDO($string,$this->DB_USER,$this->DB_PASS);
			self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		}catch (PDOException $e){

			die($e->getMessage());
		}
	}

	public static function create_id()
    {

        $length = rand(3,20);
        $number = "";
        for ($i=0; $i < $length; $i++) { 
            # code...
            $new_rand = rand(0,9);

            $number = $number . $new_rand;
        }

        return $number;
    }

	public static function getInstance()
	{
		if(self::$con){

			//return self::$con;
		}

		return $instance = new self();
 	}

	/*
	* read from database
	*/
	public function run($query,$data = array())
	{

		try{
			$stm = self::$con->prepare($query);
			$result = $stm->execute($data);

			if($result){
				$data = $stm->fetchAll(PDO::FETCH_ASSOC);
				if(is_array($data) && count($data) > 0)
				{
					$stm->closeCursor();
					return $data;
				}
			}
			
			$stm->closeCursor();

		}catch (PDOException $e){

			return($e->getMessage());
		}
 	}
 
}