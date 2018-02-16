<?php
/**
 * @file Contains \Drupal\ftp_connect\FtpConnection
 */
 
namespace Drupal\ftp_connect;



/**
 * The main service to connect and grab files
 *
 */
class FtpConnection {


	protected $ftp_hostname;
	protected $ftp_username;
	protected $ftp_password;
	protected $ftp_secured;
	protected $ftp_port;


    // Store settings array for use
    public function setSettings($settings) {

        $this->ftp_hostname = $settings['hostname'];
        $this->ftp_username = $settings['username'];
        $this->ftp_password = $settings['password'];
        $this->ftp_secured = $settings['secured'];
        $this->ftp_port = $settings['port'];

    }


	// Connect, pull file and store in local file system
	public function getFile( $source_file, $destination_file ){

		$protocol = ( $this->ftp_secured ) ? 'ftps' : 'ftp';
		$source_url = $protocol.'://'.$this->ftp_hostname.":".$this->ftp_port.$source_file;
		$source_credentials =  $this->ftp_username.":".$this->ftp_password;

		//var_dump($source_url);

		$curl = curl_init();
		$file = fopen($destination_file, 'w');
		curl_setopt($curl, CURLOPT_URL, $source_url);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FILE, $file);
		curl_setopt($curl, CURLOPT_USERPWD, $source_credentials);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		if($this->ftp_secured)curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); /* Disabled if not ftp */
		if($this->ftp_secured)curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); /* Disabled if not ftp */
		if($this->ftp_secured)curl_setopt($curl, CURLOPT_FTP_SSL, CURLOPT_FTPSSLAUTH);  /* Disabled if not ftp */
		if($this->ftp_secured)curl_setopt($curl, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);  /* Disabled if not ftp */

		$result = curl_exec ($curl);
		$error_no = curl_errno($curl);
		$success = ($error_no == 0 || $error_no == 18) ? true : false; // if error is 0 or 18 (partial) then say success

		curl_close ($curl);
		fclose($file);

		return array(
		  "success" => $success,
		  "result" => $result,
		  "error_no" => $error_no,
		  "message" => curl_strerror($error_no)
		);

	}



	// Get last modified time of source file
	public function getFileTime( $source_file ){

		$protocol = ( $this->ftp_secured ) ? 'ftps' : 'ftp';
		$source_url = $protocol.'://'.$this->ftp_hostname.":".$this->ftp_port.$source_file;
		$source_credentials =  $this->ftp_username.":".$this->ftp_password;

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL,$source_url);
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_USERPWD, $source_credentials);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_FILETIME, true);

		if($this->ftp_secured)curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); /* Disabled if not ftp */
		if($this->ftp_secured)curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); /* Disabled if not ftp */
		if($this->ftp_secured)curl_setopt($curl, CURLOPT_FTP_SSL, CURLOPT_FTPSSLAUTH);  /* Disabled if not ftp */
		if($this->ftp_secured)curl_setopt($curl, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);  /* Disabled if not ftp */

		$result = curl_exec($curl);
		$info = curl_getinfo($curl);

		if ($info['filetime'] != -1) {
			$time = $info['filetime'];
		}else{
			$time = 'Time currently unavailable';
		}

		curl_close ($curl);

		return $time;


	}
   

    

}
