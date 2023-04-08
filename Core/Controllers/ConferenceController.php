<?php

namespace Core\Controllers;

use Exception;
use Core\Views\View;
use Core\Models\Member;

/**
 * Главный контроллер приложения
 * 
 * 
 */
class ConferenceController
{
    /**
     * Действие, которое возвращает
     * index.html
     * 
     */
    public function IndexAction()
    {
        View::render();
    }
    /**
     * Действие, отвечающее за регистрацию
     * участника
     * 
     */
    public function RegisterAction()
    {
        try{
            // $result = Member::register($_POST);
            $result = Member::create($_POST);
            if($result != NULL){
                $encrypted = $this->safeEncrypt(strval($result), $_ENV['secret_key']);
                echo json_encode(['id'=>$encrypted]);                 
            }else{
                throw new Exception('Unexpected error during registration');
            }
        }catch(Exception $ex){
            echo json_encode(['error' => $ex->getMessage()]);
        }
    }

    
    /**
     * Действие, отвечающее за обновление
     * участника
     * 
     */
    public function UpdateAction()
    {
        try{
            $hasFile = false;
            // проверяем, что файл был отправлен
            if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                // создаем директорию, если ее не существует
                if(!is_dir('uploads/')) {
                    mkdir('uploads/');
                }
                // получаем имя файла и расширение
                $filename = $_FILES['file']['name'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                // генерируем уникальное имя файла
                $new_filename = uniqid().'.'.$ext;

                // перемещаем файл в директорию uploads
                $hasFile = move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/'.$new_filename); 
            }
            $id = $this->safeDecrypt($_POST['id'], $_ENV['secret_key']);
            unset($_POST['id']);
            unset($_POST['file']);
            if($hasFile){
                $_POST['photo'] = $new_filename;
            }
            // $result = Member::update($_POST, $id, $hasFile ? $new_filename: NULL);
            $result = Member::update($_POST, $id);
            if($result){
                echo json_encode(['ok'=>true]);                 
            }else{
                throw new Exception('Unexpected error during update');
            }
        }catch(Exception $ex){
            echo json_encode(['error' => $ex->getMessage()]);
        }
    }

    /**
     * Действие, отвечающее за возврат картинки
     * участника
     * 
     */
    public function ImgAction()
    {
        $filename = substr($_SERVER['REQUEST_URI'], 5);
        $file_path = '../uploads/' . $filename;

        if (!file_exists($file_path)) {
            $file_path = '../uploads/default.png';
        }
        header('Content-Type: ' . mime_content_type($file_path));
        readfile($file_path);
    }

    /**
     * Действие, отвечающее за возврат
     * дополнительной ифнормации про участника
     * 
     */
    public function DetailsAction()
    {
        $id = $this->getId();
        try{
            $result = Member::getDetails($id);
            echo json_encode($result);
        }catch(Exception $ex){
            echo json_encode(['error' => $ex->getMessage()]);
        }
    }

    /**
     * Действие, которое возвращает
     * список участников
     * 
     */
    public function MembersAction()
    {
        try{
            $result= Member::getMembers();
            // $result= Member::getAll();
            echo json_encode($result);
        }catch(Exception $ex){
            echo json_encode(['error' => $ex->getMessage()]);
        }
    }
    /**
     * Действие, которое возвращает
     * количество участников
     * 
     */
    public function MembersCountAction()
    {
        try{
            // $result = Member::getMembersCount();
            $result = Member::count();
            echo json_encode($result);
        }catch(Exception $ex){
            echo json_encode(['error' => $ex->getMessage()]);
        }
    }

    /**
     * Действие, которое возвращает
     * персональную информацию участника
     * 
     */
    public function PersonalAction()
    {
        $id = $this->getId();
        try{
            $result = Member::getPersonal($id);
            echo json_encode($result);
        }catch(Exception $ex){
            echo json_encode($ex->getMessage());
        }
    }
    /**
     * Достает из REQUEST_URI
     * id участника
     * 
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

