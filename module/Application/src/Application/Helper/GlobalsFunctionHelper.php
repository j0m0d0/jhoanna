<?php

namespace Application\Helper;

use Zend\View\Helper\AbstractHelper;

//Componentes necesarios para enviar el correo
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mail\AddressList;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\Sendmail as SendmailTransport;

//Componente para
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Zend\Validator\File\MimeType;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\UploadFile;

//get Ip
use Zend\Http\PhpEnvironment\RemoteAddress;

use Zend\View\Helper\Url;
use Zend\View\Helper\ServerUrl;

//Componentes de autenticación
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Session\Container;

class GlobalsFunctionHelper extends AbstractHelper
{
	protected $count = 0;

    public function __invoke()
    {
        $this->count++;
        $output = sprintf("I have seen 'The Jerk' %d time(s).", $this->count);
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }

    private $auth;

    public function getOnSession($val = "")
    {
       $auth = $this->auth;
        $identi=$auth->getStorage()->read();
        if ($identi!=null){
           /*foreach ($identi as $key => $value) {
              if($key==$val){
                return $value;
              }
           }*/
           return $identi;
        }
        return $identi;
    }


    public function __construct() {
        //Cargamos el servicio de autenticación en el constructor
        $this->auth = new AuthenticationService();
    }
     
    public function sendMail($to="",$from,$cc = "",$message,$subject,$attach)
    {
        $html_code ="<div>
                        <div style='padding-bottom: 25px;'>
                            <a href='http://geotest.com.mx/' target='blank'><img src='http://geotest.com.mx/wp-content/uploads/2015/09/geomarca-header1.png' style='width:300px;'></a>
                        </div>
                        <fieldset style='border-radius: 20px; width:auto; padding:30px;'>
                            <div>
                                <strong>Estimado(a):</strong>
                                <p style='width:auto;'>
                                    ".utf8_decode($message)."
                                </p>
                                <p>Por su atenci&oacute;n gracias!.</p>
                            </div>
                        </fieldset>
                    </div>";

        $html = new MimePart($html_code);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->setParts(array($html));
        
        $mail = new Message();
        $mail->setEncoding("UTF-8");
        $mail->setBody($body);
        $mail->setFrom('erp@geotest.com.mx', 'Geotest MailSender');

        if ( $to != "" ) {
            $mail->addTo( $to['correo'] , $to['nombre'] );   
        }
        
        if ($cc != "") {
            foreach ($cc as $key => $value) {
                if ( $value['correo'] != "" ) {
                    $mail->addCc($value['correo'], $value['nombre']." ".$value['apaterno']);
                }
            }
        }

        //$mail->addBcc("rterrones@ehecatl.com.mx","Rodolfo Terrones Ruiz");
        $mail->addBcc("erp@geotest.com.mx","Copia de Seguridad ERP");
        $mail->setSubject($subject);

        $config = array('throwRcptExceptions' => false);
        //$transport = new Zend_Mail_Transport_Smtp('smtphost', $config));
        /*
            User : erp@geotest.com.mx 
            Pass : GEO$_erp%2016
            Port : 25
            Smtp : geotest.com.mx
            Host : 192.163.214.110
        */
        $transport = new SmtpTransport();
        $options   = new SmtpOptions(array(
            'name'              => 'mail.geotest.com.mx',
            'host'              => 'mail.geotest.com.mx',
            'port'              => 465,
            'connection_class'  => 'login',
            'connection_config' => array(
                'username' => 'erp@geotest.com.mx',
                'password' => 'ERP-gg&si@16%',
            ),
            'port'              => 25, // Notice port change for TLS is 587
        ));
        /*
        $ical = <<<ICALENDAR_DATA
                    BEGIN:VCALENDAR
                    PRODID:-//Seu sistema//Sua organizacao//EN
                    VERSION:2.0
                    CALSCALE:GREGORIAN
                    METHOD:REQUEST
                    BEGIN:VEVENT
                    DTSTART:{$dtStart}
                    DTEND:{$dtEnd}
                    DTSTAMP:{$timestamp}
                    UID:{$uid}
                    SUMMARY:Sucesso Total
                    DESCRIPTION:Forrózão hoje. Vamos ralá nossos bucho!
                    CREATED:{$dtCreated}
                    LAST-MODIFIED:{$dtCreated}
                    LOCATION:Forró pé sujo
                    SEQUENCE:0
                    STATUS:CONFIRMED
                    TRANSP:OPAQUE
                    ORGANIZER:MAILTO:adlermedrado@gmail.com
                    BEGIN:VALARM
                    ACTION:DISPLAY
                    DESCRIPTION:Lembrete do evento
                    TRIGGER:-P0DT0H10M0S
                    END:VALARM
                    END:VEVENT
                    END:VCALENDAR
                    ICALENDAR_DATA;
        */
        $transport->setOptions($options);
        $sent = true;
        try{
            $transport->send($mail);
         }catch (\Zend\Mail\Transport\Exception\ExceptionInterface $e) {
            $sent = false;
         }
         return $sent;
    }

	public function uploadFile($File,$ruta_save,$type = null)
    {
        //move uploaded file
        if($File['name'] != null ){
            if($File["type"] == $type || $type == null){
                $size = new Size(array('max'=>'40MB'));
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators(array($size),$File['name']);
                if (!$adapter->isValid()){
                    $dataError = $adapter->getMessages();
                    $error = array();
                    foreach($dataError as $key=>$row){ $error[] = $row; }
                    return $error[0];
                } else {
                    $destination = dirname(__DIR__) . '/../../../../public' . $ruta_save;
                    //echo $destination;
                    if(!file_exists($destination)){
                        mkdir($destination,0777,true);
                    }
                    //if is image create dir thumbnail
                    if($type != null && $type == "image/png" || $type == "image/jpeg"){
                        $destination_thumb = dirname(__DIR__) . '/../../../../public' . $ruta_save."_thumb";
                        if(!file_exists($destination_thumb)){
                            mkdir ($destination_thumb,0777,true);
                        } 
                    }

                    $ext = pathinfo($File['name'], PATHINFO_EXTENSION);
                    $newName = md5(rand(). $File['name']) . '.' . $ext;
                    $adapter = new \Zend\File\Transfer\Adapter\Http();
                    $adapter->addFilter('File\Rename', array(
                         'target' => $destination . '/' . $newName,
                    ));
                    if ($adapter->receive($File['name'])) {
                        $file = $adapter->getFilter('File\Rename')->getFile();
                        $target = $file[0]['target'];
                        //if is image copy to thumbnail
                        if($type != null && $type == "image/png" || $type == "image/jpeg"){
                            $this->make_thumb($destination.'/'. $newName, $destination_thumb.'/'. $newName,400);
                        }
                        return $ruta_save .'/'. $newName;
                    }
                }
            }else{
                return "Error no tengo tipo.";
            }
        }else{
            return "Error no me llego ningun archivo.";
        }

    }

    public function make_thumb($src, $dest, $desired_width) {

        /* read the source image */
        $source_image = imagecreatefromjpeg($src);
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($desired_width / $width));

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

        /* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $dest);
    }

    public function closetags($html) {
        preg_match_all('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);
        if (count($closedtags) == $len_opened) {
            return $html;
        }
        $openedtags = array_reverse($openedtags);
        for ($i=0; $i < $len_opened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $html .= '</'.$openedtags[$i].'>';
            } else {
                unset($closedtags[array_search($openedtags[$i], $closedtags)]);
            }
        }
        return $html;
    } 

    
}