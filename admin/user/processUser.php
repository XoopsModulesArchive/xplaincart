<?php
//require_once dirname(__DIR__, 2) . '/library/config.php';
//require_once dirname(__DIR__) . '/library/functions.php';
require_once dirname(__DIR__, 2) . '/library/define.php';
require_once dirname(__DIR__, 2) . '/library/init_class.php';	//include database_class inside
require_once dirname(__DIR__) . '/library/admin_func_class.php';
class process_user_class
{
    public function process_user()
    {
        $init = new init_class();

        $admin_func = new admin_func_class();

        $admin_func->checkUser();

        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'add':
                $this->addUser();
                break;
            case 'modify':
                $this->modifyUser();
                break;
            case 'delete':
                $this->deleteUser();
                break;
            default:
                // if action is not defined or unknown
                // move to main user page
                header('Location: index.php');
        }
    }

    //function

    public function addUser()
    {
        $db = new database_class();

        $userName = $_POST['txtUserName'];

        $password = $_POST['txtPassword'];

        /*
        // the password must be at least 6 characters long and is
        // a mix of alphabet & numbers
        if(strlen($password) < 6 || !preg_match('/[a-z]/i', $password) ||
        !preg_match('/[0-9]/', $password)) {
          //bad password
        }
        */

        // check if the username is taken

        $sql = 'SELECT user_name
		        FROM ' . PREFIX . "user
				WHERE user_name = '$userName'";

        $result = $db->dbQuery($sql);

        if (1 == $db->dbNumRows($result)) {
            header('Location: index.php?view=add&error=' . urlencode('Username already taken. Choose another one'));
        } else {
            $sql = 'INSERT INTO ' . PREFIX . "user (user_name, user_password, user_regdate)
			          VALUES ('$userName', PASSWORD('$password'), NOW())";

            $db->dbQuery($sql);

            header('Location: index.php');
        }
    }

    //function

    /*
        Modify a user
    */

    public function modifyUser()
    {
        $db = new database_class();

        $userId = (int)$_POST['hidUserId'];

        $password = $_POST['txtPassword'];

        $sql = 'UPDATE ' . PREFIX . "user 
		          SET user_password = PASSWORD('$password')
				  WHERE user_id = $userId";

        $db->dbQuery($sql);

        header('Location: index.php');
    }

    //function

    /*
        Remove a user
    */

    public function deleteUser()
    {
        $db = new database_class();

        if (isset($_GET['userId']) && (int)$_GET['userId'] > 0) {
            $userId = (int)$_GET['userId'];
        } else {
            header('Location: index.php');
        }

        $sql = 'DELETE FROM ' . PREFIX . "user 
		        WHERE user_id = $userId";

        $db->dbQuery($sql);

        header('Location: index.php');
    }
}
$process_user = new process_user_class();
$process_user->process_user();
