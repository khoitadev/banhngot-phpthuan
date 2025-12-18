<?php
include($_SERVER['DOCUMENT_ROOT'] . "/lib/database.php");
include($_SERVER['DOCUMENT_ROOT'] . "/helpers/format.php");
?>

<?php
class user_register
{
    private $db;
    private $fm;

    public function __construct()
    {
        $this->db = new Database();
        $this->fm = new Format();
    }

    public function insert_user($data)
    {
        $Fullname = mysqli_real_escape_string($this->db->link, $data['Fullname']);
        $Email = mysqli_real_escape_string($this->db->link, $data['Email']);
        $Password = mysqli_real_escape_string($this->db->link, $data['Password']);

        $Hash_Pass = md5($Password);
        // customers table has several NOT NULL fields (Image, PhoneNumber, Address, Date_Login, Date_Logout)
        // Provide safe defaults on registration.
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $now = date('Y-m-d H:i:s');
        $Image = '';
        $PhoneNumber = '';
        $Address = '';

        if (empty($Fullname) || empty($Email) || empty($Password)) {
            $alert = "<span class = 'error'>Fields must not be empty</span>";
            return $alert;
        } else {
            $check_Email = "SELECT * FROM customers WHERE Email='$Email' LIMIT 1";
            $result_check = $this->db->select($check_Email);
            if ($result_check) {
                $alert = "<span class = 'error'>Email already existed</span>";
                return $alert;
            } else {
                $query = "INSERT INTO customers(Fullname, Image, PhoneNumber, Address, Status, Email, Password, Date_Login, Date_Logout)
                          VALUES('$Fullname', '$Image', '$PhoneNumber', '$Address', 1, '$Email', '$Hash_Pass', '$now', '$now')";
                $result = $this->db->insert($query);
                if ($result) {
                    $alert = "<span class = 'success'>Register succesfully</span>";
                    return $alert;
                } else {
                    $alert = "<span class = 'error'>Register failed</span>";
                    return $alert;
                }
            }
        }
    }
}

?>