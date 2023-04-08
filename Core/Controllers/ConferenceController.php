<?php

namespace Core\Controllers;

use Exception;
use Core\Views\View;
use Core\Models\Member;

class ConferenceController extends Controller
{
    /**
     * Renders the index view.
     * @return void
     */
    public function IndexAction()
    {
        View::render();
    }

    /**
     * Register action method
     *
     * @return void
     */
    public function RegisterAction()
    {
        $result = Member::create($_POST);
        $encrypted = $this->safeEncrypt(strval($result), $_ENV['secret_key']);
        echo json_encode(['id'=>$encrypted]);
    }

    
    /**
     * UpdateAction method for updating member information.
     * Checks if a file was uploaded, generates a unique file name and moves the file to the uploads directory.
     * Updates the member record in the database.
     * @return void
     */
    public function UpdateAction()
    {
        $hasFile = false;
        // check if file was uploaded
        if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            if(!is_dir('uploads/')) {
                mkdir('uploads/');
            }
            $filename = $_FILES['file']['name'];
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $new_filename = uniqid().'.'.$ext;
            $hasFile = move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/'.$new_filename); 
        }
        // decrypt and remove 'id' and 'file' keys from $_POST array
        $id = $this->safeDecrypt($_POST['id'], $_ENV['secret_key']);
        unset($_POST['id']);
        unset($_POST['file']);

        // add photo file name to $_POST array if a file was uploaded
        if($hasFile){
            $_POST['photo'] = $new_filename;
        }
        // update member record in database
        Member::update($_POST, $id);
        echo json_encode(['ok'=>true]);  
    }

    /**
     * ImgAction action for serving images.
     * 
     * Reads the image file from the server and sends it to the client with the correct MIME type.
     * If the requested file does not exist, a default image is served instead.
     */
    public function ImgAction()
    {
        $filename = substr($_SERVER['REQUEST_URI'], 5);
        $file_path = '../public/uploads/' . $filename;

        if (!file_exists($file_path)) {
            $file_path = '../public/uploads/default.png';
        }
        header('Content-Type: ' . mime_content_type($file_path));
        readfile($file_path);
    }

    /**
     * DetailsAction method to get details of a member by id
     * @return void
     */
    public function DetailsAction()
    {
        $id = $this->getId();
        $result = Member::getDetails($id);
        echo json_encode($result);
    }

    /**
     * MembersAction for getting all members' data
     * @return void
     */
    public function MembersAction()
    {
        $result= Member::getMembers();
        echo json_encode($result);
    }

    /**
     * MembersCountAction - Returns the count of all members in the database.
     * This method retrieves the count of all members in the database by calling the Member::count() method and
     * outputs the result as a JSON-encoded string.
     * @return void
     */
    public function MembersCountAction()
    {
        $result = Member::count();
        echo json_encode(['membersCount' => $result]);
    }

    /**
     * Displays personal information for a member with the specified ID.
     * @return void
     */
    public function PersonalAction()
    {
        $id = $this->getId();
        $result = Member::getPersonal($id);
        echo json_encode($result);
    }
    
    /**
     * Returns decrypted ID from URL
     * @return int Decrypted ID
     */
    private function getId(){
        $url = $_SERVER['REQUEST_URI'];
        $url = explode("/", $url);
        $subArr = array_slice($url, 4);
        $encrypted = implode('/',$subArr);
        $decrypted = $this->safeDecrypt($encrypted, $_ENV['secret_key']);
        return intval($decrypted);
    }

    /**
     * Encrypt a message
     * 
     * @param string $message - message to encrypt
     * @param string $key - encryption key
     * @return string
     * @throws RangeException
     */
    function safeEncrypt(string $message, string $key): string
    {
        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RangeException('Key is not the correct size (must be 32 bytes).');
        }
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        
        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $message,
                $nonce,
                $key
            )
        );
        sodium_memzero($message);
        sodium_memzero($key);
        return $cipher;
    }

    /**
     * Decrypt a message
     * 
     * @param string $encrypted - message encrypted with safeEncrypt()
     * @param string $key - encryption key
     * @return string
     * @throws Exception
     */
    function safeDecrypt(string $encrypted, string $key): string
    {   
        $decoded = base64_decode($encrypted);
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
        
        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );
        if (!is_string($plain)) {
            throw new Exception('Invalid MAC');
        }
        sodium_memzero($ciphertext);
        sodium_memzero($key);
        return $plain;
    }
}

