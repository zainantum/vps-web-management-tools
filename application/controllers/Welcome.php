<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
    {
        // $this->load does not exist until after you call this
        parent::__construct(); // Construct CI's core so that you can use it
        $this->load->database();
		$this->load->helper('form');
		$this->load->helper("url");
		$this->ci =& get_instance(); 
		
    }

	function index()
	{
		if(maintenance && $this->getUsername() != "imron"){
			redirect('http://167.86.127.69/exorde/maintenance', 'refresh');
		}else{
			$this->load->view('table');
		}
	}

	function login(){
		$this->load->view('login');
	}

	function register(){
		if(maintenance){
			redirect('http://167.86.127.69/exorde/maintenance', 'refresh');
		}else{
			$this->load->view('register');
		}
	}

	function cekSession(){
		return $this->ci->session->userdata('isLogin');
	}

	function getUsername(){
		return $this->ci->session->userdata('username');
	}

	function checkComponent($list=[]){
		$dataTemp = [];
		foreach($list as $key => $value){
			if($_POST[$key] == "" || $_POST[$key] == null || array_key_exists($key, $_POST) === false){
				array_push($dataTemp, $value);
			}
		}
		if(count($dataTemp) > 0){
			$rescode = 3;
			$resmsg = implode(", ", $dataTemp)." tidak boleh kosong";
		}else{
			$rescode = 0;
			$resmsg = "semua terisi";
		}
		return json_encode(array('result_code' => $rescode, 'result_message' => $resmsg));
	}

	function doLogin(){
		$username = $_POST["username"];
		$password = $this->GenUserPassMD5($_POST["password"]);
		$rescode = 0;
		$resmsg = "Berhasil";
		$resdata = '';
		$cek = json_decode($this->checkComponent(["username"=>"username","password"=>"pass"]), true);
		if($cek["result_code"] != 0){
			$rescode = 3;
			$resmsg = $cek["result_message"];
		}else{
			$sql = "SELECT password FROM tb_m_user where nama_user = '$username'";
			$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
			if ($proses["result_code"] == 4){
				if($password == $proses["data_openq"][0]["password"]){
					$dataSession = array(
						'isLogin' => TRUE,
						'username' => $username
					);
					//log_message("debug","dataSession: ".json_encode($dataSession));
					$this->ci->session->set_userdata($dataSession);
					$resmsg = "Berhasil login";
				}else{
					$rescode = 3;
					$resmsg = "Password salah";
				}
			}else{
				$rescode = 3;
				$resmsg = "Akun tidak ditemukan. Daftar dulu dong pun";
			}
		}
		$data = json_encode(
			array(
				'result_code' => $rescode, 
				'result_msg' => $resmsg,
				'result_data' => $resdata,
			)
		);

		$this->ci->output
				->set_content_type('application/json')
				->set_output($data);
	}

	function doRegister(){
		$username = $_POST["username"];
		$password = $this->GenUserPassMD5($_POST["password"]);
		$cek = json_decode($this->checkComponent(["username"=>"username","password"=>"passowrd"]), true);
		if($cek["result_code"] != 0){
			$rescode = 3;
			$resmsg = $cek["result_message"];
		}else{
			$sql = "SELECT count(*) as ada FROM tb_m_user where nama_user = '$username'";
			$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
			$tele = $_POST["usernameTg"];
			if ($proses["result_code"] == 4 && $proses["data_openq"][0]["ada"] > 0) {
				$rescode = 3;
				$resmsg = "User udah terdaftar. tinggal login aja!";
			}else{
				$sqlInsert = "insert into tb_m_user (nama_user, password, telegram) values ('$username', '$password', '$tele') ";
				$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlInsert), true);
				if ($execInsert["result_code"] == 0) {
					$rescode = 0;
					$resmsg = "Berhasil menambahkan user";
				}else{
					$rescode = 3;
					$resmsg = "Gagal menambahkan user";
				}
			}
		}
		$data = json_encode(
			array(
				'result_code' => $rescode, 
				'result_msg' => $resmsg,
			)
		);

		$this->ci->output
				->set_content_type('application/json')
				->set_output($data);
	}

	function doLogout(){
		if($this->cekSession()){
			$this->ci->session->unset_userdata(array('username','isLogin'));
        	$this->ci->session->sess_destroy();
			$this->load->view('login');
		}else{
			$this->load->view('login');
		}
	}

	function loadMasterOption(){
		if($this->cekSession()){
			$opt = $_POST["cmbMaster"];
			$rescode = 0;
			$resmsg = "";
			$resdata = '';
			if($opt == "addProjectSection"){
				$resdata = $this->load->view('projectSection',[], true);
			}else if($opt == "vpsdo"){
				$resdata = $this->load->view('doSection',[], true);
			}else if($opt == "addPemSection"){
				$resdata = $this->load->view('pemSection',[], true);
			}else if($opt == "actionProjectSection"){
				$resdata = $this->load->view('actionSection',[], true);
			}else{
				$rescode = 3;
				$resmsg = "Template tidak ditemukan";
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
					'result_data' => $resdata
				)
			);
			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view('login');
		}
	}

	function loadDetailOption(){
		if($this->cekSession()){
			$opt = $_POST["cmbProjectIp"];
			$rescode = 0;
			$resmsg = "";
			$resdata = '';
			if($opt == "buat"){
				$resdata = $this->load->view('newVps',[], true);
			}else if($opt == "vpsdo"){
				$resdata = $this->load->view('doSection',[], true);
			}else if($opt == "delAll"){
				$resdata = '<button type="button" class="btn btn-danger mb-3" id="delAllIp" onclick="deleteAllIp()" >Delete all IP</button>';
			}else if($opt == "command"){
				$resdata = $this->load->view('commandGcSection',[], true);
			}else if(strpos($opt,';') !== false){
				$resdata = $this->load->view('defaultCommandGcSection',[], true);
			}else{
				$rescode = 3;
				$resmsg = "Template tidak ditemukan";
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
					'result_data' => $resdata
				)
			);
			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view('login');
		}
	}

	function maintenance(){
		$this->load->view('maintenance');
	}

	function akses()
	{
		if(maintenance && $this->getUsername() != "imron"){
			redirect('http://167.86.127.69/exorde/maintenance', 'refresh');
		}else{
			if($this->cekSession()){
				$this->load->view('form');
			}else{
				$this->load->view('login');
			}
		}
	}

	function profile()
	{
		if(maintenance && $this->getUsername() != "imron"){
			redirect('http://167.86.127.69/exorde/maintenance', 'refresh');
		}else{
			if($this->cekSession()){
				$this->load->view('profile');
			}else{
				$this->load->view('login');
			}
		}
	}

	function pantau()
	{
		$this->load->view('pantau');
	}

	function deleteOldData(){
		$sqlInsert = "DELETE FROM tb_log_ip WHERE tgl_jam_buat < (NOW() - INTERVAL 60 MINUTE)";
		$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlInsert), true);
		if ($execInsert["result_code"] == 0) {
			$rescode = 0;
			$resmsg = "Berhasil menghapus data lama";
		}else{
			$rescode = 3;
			$resmsg = "Gagal menghapus data lama";
		}
		$this->output
        	->set_content_type('application/json')
        ->set_output(json_encode(["result_code"=>$rescode,"result_msg"=>$resmsg]));
	}

	function GenUserPassMD5($usrpassword){
		$SecKey = "arbytergroup";
  		return md5(md5($SecKey).md5($usrpassword.$SecKey));
	}

	function checkIpExists($ip){
		$exists = false;
		$sql = "SELECT count(*) as hasil FROM tb_m_ip where ip = '$ip'";
		$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
		if ($proses["result_code"] == 4 && $proses["data_openq"][0]["hasil"]>0){
			$exists = true;
		}
		return $exists;
	}

	function checkProjectExists($project){
		$exists = false;
		$sql = "SELECT count(*) as hasil FROM tb_m_project where nama_project = '$project'";
		$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
		if ($proses["result_code"] == 4 && $proses["data_openq"][0]["hasil"]>0){
			$exists = true;
		}
		return $exists;
	}

	function update(){
		$resmsg = '';
		$rescode = 0;
		$action = $_POST["action"];
		if($action == "addIpSection"){
			$baru = $_POST["ipBaruAdd"];
			if($this->checkIpExists($baru)){
				$rescode = 3;
				$resmsg = "IP $baru sudah terdaftar";
			}else{
				if(strpos($baru, ";") !== false){
					$baru = explode(";",$baru);
					$ip = $baru[0];
					$tele = $baru[1];
					$project = $baru[2];
					$platform = $baru[3];
					if($ip == "" && $tele == ""){
						$rescode = 3;
						$resmsg = "IP atau username telegram tidak boleh kosong";
					}else{
						$sqlInsert = "insert into tb_m_ip (ip, tele, project, platform) values ('$ip', '$tele', '$project', '$platform') ";
						$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlInsert), true);
						if ($execInsert["result_code"] == 0) {
							$rescode = 0;
							$resmsg = "Berhasil menambahkan ip baru";
						}else{
							$rescode = 3;
							$resmsg = "Gagal menambahkan ip baru";
						}
					}
				}else{
					$rescode = 3;
					$resmsg = "Gagal menambahkan data. Format tidak sesuai!!";
				}
			}
		}else if($action == "editIpSection"){
			$baru = $_POST["ipBaruEdit"];
			$lama = $_POST["ipLamaEdit"];
			if($this->checkIpExists($lama)){
				if($baru == ""){
					$rescode = 3;
					$resmsg = "IP baru tidak boleh kosong";
				}else{
					$sqlUpdate = "update tb_m_ip set ip = '$baru' where ip = '$lama'";
					$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlUpdate), true);
					if ($execInsert["result_code"] == 0) {
						$rescode = 0;
						$resmsg = "Berhasil memperbarui ip";
					}else{
						$rescode = 3;
						$resmsg = "Gagal memperbarui ip";
					}
				}
			}else{
				$rescode = 3;
				$resmsg = "IP $baru tidak ditemukan";
			}
		}else if($action == "deleteIpSection"){
			$lama = $_POST["ipLamaDelete"];
			if($lama != ""){
				if(strpos($lama,";") !== false){
					foreach(explode(";",$lama) as $key => $value){
						if($this->checkIpExists($value)){
							$sqlDelete = "delete from tb_m_ip where ip = '$value'";
							$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlDelete), true);
							if ($execInsert["result_code"] == 0) {
								$rescode = 0;
								$resmsg = "Berhasil menghapus ip";
							}else{
								$rescode = 3;
								$resmsg = "Gagal menghapus ip";
							}
						}else{
							$rescode = 3;
							$resmsg .= "IP $value tidak ditemukan";
						}
					}
				}else{
					if($this->checkIpExists($lama)){
						$sqlDelete = "delete from tb_m_ip where ip = '$lama'";
						$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlDelete), true);
						if ($execInsert["result_code"] == 0) {
							$rescode = 0;
							$resmsg = "Berhasil menghapus ip";
						}else{
							$rescode = 3;
							$resmsg = "Gagal menghapus ip";
						}
					}else{
						$rescode = 3;
						$resmsg = "IP $lama tidak ditemukan";
					}
				}
			}else{
				$rescode = 3;
				$resmsg = "IP tidak boleh kosong";
			}
		}
		$this->output
        	->set_content_type('application/json')
        ->set_output(json_encode(["result_code"=>$rescode,"result_msg"=>$resmsg]));
		
	}

	public function actionvps(){
		if($this->cekSession()){
			$username = $this->getUsername();
			$password = $this->GenUserPassMD5($_POST["password"]);
			$project = $_POST["project"];
			$email = $_POST["emailproject"];
			$rescode = 0;
			$resmsg = "Berhasil";
			
			if($_POST["action2"] == "addProjectSection"){
				$cek = json_decode($this->checkComponent(["project"=>"project","emailproject"=>"email project"]), true);
				if($cek["result_code"] != 0){
					$rescode = 3;
					$resmsg = $cek["result_message"];
				}else{
					$json = $_FILES['fileupload']["name"];
					$path = "assets/key/";
					$tmp_name = $_FILES['fileupload']['tmp_name'];
					if(move_uploaded_file($tmp_name,$path.$project.".json")){
						$sqlInsert = "insert into tb_m_project (nama_user, nama_project, email) values ('$username', '$project', '$email') ";
						$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlInsert), true);
						if ($execInsert["result_code"] == 0) {
							$rescode = 0;
							$resmsg = "Berhasil menambahkan project";
						}else{
							$rescode = 3;
							$resmsg = "Gagal menambahkan project";
						}
					}else{
						$rescode = 3;
						$resmsg = "Gagal mengupload file";
					}
				}
			}else if($_POST["action2"] == "addPemSection"){
				$cek = json_decode($this->checkComponent(["project"=>"project","usernamevps"=>"username vps"]), true);
				if($cek["result_code"] != 0){
					$rescode = 3;
					$resmsg = $cek["result_message"];
				}else{
					$json = $_FILES['fileupload']["name"];
					$path = "assets/pem/";
					$tmp_name = $_FILES['fileupload']['tmp_name'];
					if(move_uploaded_file($tmp_name,$path.$_POST["usernamevps"].".pem")){
						$rescode = 0;
						$resmsg = "Upload file berhasil";
					}else{
						$rescode = 3;
						$resmsg = "Gagal mengupload file";
					}
				}
			}else{
				$rescode = 3;
				$resmsg = "Menu tidak ditemukan";
			}

			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view('login');
		}
	}

	function addList(){
		if($this->cekSession()){
			$username = $this->getUsername();
			$cmbProject = $_POST["cmbProject"];
			$list = str_replace(" ","",$_POST["allIp"]);
			$rescode = 0;
			$resmsg = "Berhasil";
			$resdata = '';
			$sql = "SELECT telegram FROM tb_m_user where nama_user = '$username'";
			$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
			if ($proses["result_code"] == 4){
				$tele = $proses["data_openq"][0]["telegram"];
				if($tele != null && $tele != ""){
					foreach(explode(";", $list) as $key => $value){
						$sqlInsert = "insert into tb_m_ip (ip, tele, project, platform) values ('$value', '$tele', '$cmbProject', '-') ";
						$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlInsert), true);
						if ($execInsert["result_code"] == 0) {
							$rescode = 0;
							$resmsg = "Berhasil menambahkan ip baru";
						}else{
							$rescode = 3;
							$resmsg = "Gagal menambahkan ip baru";
						}
						// $str=file_get_contents(FCPATH.'listIp.txt');
						// if(strpos($str, $value) === false){
						// 	$baru = "$value;$tele;$cmbProject";
						// 	file_put_contents(FCPATH.'listIp.txt', $baru."\n", FILE_APPEND);
						// }
					}
				}else{
					$rescode = 3;
					$resmsg = "Telegram username belum ada di database. Silahkan setting dulu";	
				}
			}else{
				$rescode = 3;
				$resmsg = "Telegram username belum ada di database. Silahkan setting dulu";
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
					'result_data' => $resdata,
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view("login");
		}
	}

	function getList(){
		if($this->cekSession()){
			$username = $this->getUsername();
			$rescode = 0;
			$resmsg = "Berhasil";
			$resdata = '';
			$sql = "SELECT distinct nama_project, email FROM tb_m_project where nama_user = '$username'";
			$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
			if ($proses["result_code"] == 4 && count($proses["data_openq"]) > 0) {
				foreach($proses["data_openq"] as $key => $value){
					if($resdata == ""){
						$resdata = "<option class = 'bld' value='".$value["nama_project"]."'>".$value["nama_project"]." - ".$value["email"]."</option>";
					}else{
						$resdata .= "<option class = 'bld' value='".$value["nama_project"]."'>".$value["nama_project"]." - ".$value["email"]."</option>";
					}
					// $resdata .= $this->ci->gcloud->list_all_instances($value["nama_project"]);
				}
			}else{
				$rescode = 3;
				$resmsg = "Projek tidak ditemukan. Silahkan tambahkan projek terlebih dahulu";
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
					'result_data' => $resdata,
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view("login");
		}
	}

	function getListIp(){
		if($this->cekSession()){
			$rescode = 0;
			$resmsg = "Berhasil";
			$resdata = '';
			$project = $_POST["cmbProject"];
			try {
				$resdata = $this->ci->gcloud->list_all_instances($project);
			}
			catch(Exception $e) {
				$check = json_decode($e->getMessage(), true);
				$rescode = 3;
				$resmsg = "Gagal mendapatkan List IP";
				if($check["code"] == 16){
					$resmsg = "Akun anda terkena Suspicious Activity!!!";
				}
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
					'result_data' => $resdata,
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view("login");
		}
	}

	function delAllVpsProject(){
		if($this->cekSession()){
			$project = $_POST["cmbProject"];
			$rescode = 0;
			$resmsg = "Berhasil";
			$get = json_decode($this->ci->gcloud->delete_all_instances($project), true);
			if($get["result_code"] != 0){
				$rescode = 3;
				$resmsg = $get["result_msg"];
			}else{
				$resmsg = $get["result_msg"];
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view("login");
		}
	}

	function newVps(){
		if($this->cekSession()){
			$username = $this->getUsername();
			$project = $_POST["cmbProject"];
			$ram = $_POST["ramVps"];
			$zona = $_POST["zona"];
			$disk = $_POST["disk"];
			$rescode = 0;
			$resmsg = "Berhasil";
			$resdata = '';
			$cek = json_decode($this->checkComponent(["ramVps"=>"ram","zona"=>"zona","disk"=>"disk"]), true);
			if($cek["result_code"] != 0 || strpos(strtolower($ram), "pilih") !== false || strpos(strtolower($zona), "pilih") !== false){
				$rescode = 3;
				$resmsg = $cek["result_message"];
			}else{
				if($disk == ""){
					$disk = 50;
				}
				$in = $username.rand(1111,9999);
				$get = json_decode($this->ci->gcloud->create_instance($project, $zona, $in, $ram, $disk), true);
				if($get["result_code"] != 0){
					$rescode = 3;
					$resmsg = $get["result_msg"];
				}else{
					$getip = $this->ci->gcloud->getIp($project, $zona, $in);
					$resmsg = $get["result_msg"]."\nIP: $getip";
				}
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view("login");
		}
	}

	function delVps(){
		if($this->cekSession()){
			$project = $_POST["cmbProjectIp"];
			$rescode = 0;
			$resmsg = "Berhasil";
			if($project == ""){
				$rescode = 3;
				$resmsg = "vps tidak boleh kosong";
			}else{
				$project = explode(";",$project);
				$get = json_decode($this->ci->gcloud->delete_instance($project[0],str_replace("zones/","",$project[1]), $project[2]), true);
				if($get["result_code"] != 0){
					$rescode = 3;
					$resmsg = $get["result_msg"];
				}else{
					$resmsg = $get["result_msg"];
				}
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view("login");
		}
	}

	function runCommand(){
		if($this->cekSession()){
			$unvps = $_POST["unvps"];
			$pwvps = $_POST["pwvps"];
			$addressvps = $_POST["addressvps"];
			$workervps = $_POST["workervps"];
			$csvps = $_POST["csvps"];
			$tvps = $_POST["tvps"];
			$dvps = $_POST["dvps"];
			$svps = $_POST["svps"];
			$ipCommand = str_replace([" ",";"],["",","],$_POST["ipCommand"]);
			$commandId = $_POST["commandId"];
			$rescode = 0;
			$resmsg = "Berhasil";
			$resdata = "";
			$platform = $_POST["cmbMaster"] == "vpsdo"? "do":"gcp";
			$cek = json_decode($this->checkComponent(["commandId"=>"command","unvps"=>"username vps","pwvps"=>"password vps","ipCommand"=>"ip vps"]), true);
			if($cek["result_code"] != 0){
				$rescode = 3;
				$resmsg = $cek["result_message"];
			}else{
				if($commandId == 1){
					$cek = json_decode($this->checkComponent(["addressvps"=>"address","workervps"=>"jumlah worker","csvps"=>"create swap","tvps"=>"use twitter","dvps"=>"install docker","svps"=>"swap"]), true);
					if($cek["result_code"] != 0){
						$rescode = 3;
						$resmsg = $cek["result_message"];
					}
				}
				if($commandId == 99){
					$cek = json_decode($this->checkComponent(["customcommand"=>"Custom Command"]), true);
					if($cek["result_code"] != 0){
						$rescode = 3;
						$resmsg = $cek["result_message"];
					}
				}
				if($rescode == 0 && $_POST["cmbMaster"] != "vpsdo"){
					if (file_exists("assets/pem/$unvps.pem")) {
						$rescode = 0;
					}else{
						$rescode = 3;
						$resmsg = "File .pem tidak ditemukan. silahkan upload file .pem terlebih dahulu";
					}
				}
				if($rescode == 0){
					if ($commandId == 1){
						$commandId = "sudo rm -rf temporaryParam* && sudo wget https://raw.githubusercontent.com/zainantum/exorde-auto/main/temporaryParam.sh && sudo chmod +x * && sudo bash temporaryParam.sh $addressvps $workervps $dvps $csvps $tvps $svps && sudo sysctl vm.swappiness=5";
					}else if($commandId == 2){
						$commandId = "sudo zdump /etc/localtime && sudo docker logs -t --tail=2 exorde1 2>&1";
					}else if($commandId == 3){
						$commandId = "sudo df -h";
					}else if($commandId == 4){
						$commandId = "sudo docker ps -a";
					}else if($commandId == 5){
						$commandId = "sudo free -h";
					}else if($commandId == 6){
						$commandId = "sudo docker logs exorde1 -t --tail=5000 2>&1 | grep 'REP'";
					}else if($commandId == 7){
						$commandId = "sudo docker stop $(docker ps -qf 'name=^exorde') && sudo docker rm $(docker ps -a -qf 'name=^exorde')";
					}else if($commandId == 8){
						$commandId = "sudo cat /proc/sys/vm/swappiness";
					}else if($commandId == 9){
						$commandId = "sudo sysctl vm.swappiness=5";
					}else if($commandId == 10){
						$commandId = "sudo docker ps -a && sudo docker logs exorde1 2>&1 | grep REPUTATION | wc -l";
					}else if($commandId == 11){
						$commandId = "sudo docker restart exorde1";
					}else if($commandId == 12){
						$commandId = "yes | sudo docker system prune -a && yes | sudo docker image prune && yes | sudo docker rmi $(docker images -a -q) && yes | sudo docker volume prune";
					}else if($commandId == 13){
						$commandId = "sudo rm -rf checkDisk* && sudo wget https://raw.githubusercontent.com/zainantum/exorde-auto/main/checkDisk.sh && sudo chmod +x *";
					}else if($commandId == 14){
						$commandId = "sudo docker image inspect exordelabs/exorde-client --format '{{.RepoDigests}}'";
					}else if($commandId == 15){
						$commandId = "sudo docker stop watchtower && sudo docker rm watchtower && sudo docker run -d --restart unless-stopped --name watchtower -v /var/run/docker.sock:/var/run/docker.sock containrrr/watchtower exorde1 -i 1800 --cleanup";
					}else if($commandId == 16){
						$commandId = "docker logs exorde1 --since=1h 2>&1 | grep 'Twitter Selenium' | wc -l && docker logs exorde1 2>&1 | grep 'Username provided' | tail -1";
					}else if($commandId == 17){
						$commandId = "rm -rf totRep* && wget https://raw.githubusercontent.com/zainantum/exorde-auto/main/totRep.sh && chmod +x totRep* && bash totRep.sh && docker ps -f name=exorde1 --format '{{.Status}}' && free -h | grep 'Mem' && docker logs exorde1 2>&1 | grep 'cookies.pkl' | tail -1";
					}else if($commandId == 99){
						$commandId = $_POST["customcommand"];
					}
					$output = shell_exec('python3 /var/www/html/exorde/trial.py --command "'.$commandId.'" --username "'.$unvps.'" --password "'.$pwvps.'" --ip "'.$ipCommand.'" --platform "'.$platform.'"');
					//log_message("debug",'python3 /var/www/html/exorde/trial.py --command "'.$commandId.'" --username "'.$unvps.'" --password "'.$pwvps.'" --ip "'.$ipCommand.'" --platform "'.$platform.'"');
					////log_message("debug","check hasil run command ".$output);
					$resdata = str_replace("#br#","\n",$output);
					// $resdata = '<p class="line1">'.$ipCommand.'</p>';
					// foreach(explode(",",$output) as $key => $value){
					// 	$resdata .= '<p class="line2">'.$value.'</p>';
					// }
					
				}
				
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
					'result_data' => $resdata,
				)
			);

			//log_message("debug","check result run command ".json_encode($data));

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view("login");
		}
	}

	function startVps(){
		if($this->cekSession()){
			$project = $_POST["cmbProjectIp"];
			if($_POST["ipListMulti"] != ""){
				$project = $_POST["ipListMulti"];
			}
			$rescode = 0;
			$resmsg = "Berhasil";
			$project = explode(";",$project);
			$get = json_decode($this->ci->gcloud->start_instance($project[0],str_replace("zones/","",$project[1]), $project[2]), true);
			if($get["result_code"] != 0){
				$rescode = 3;
				$resmsg = $get["result_msg"];
			}else{
				$resmsg = $get["result_msg"];
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view("login");
		}
	}

	function stopVps(){
		if($this->cekSession()){
			$project = $_POST["cmbProjectIp"];
			if($_POST["ipListMulti"] != ""){
				$project = $_POST["ipListMulti"];
			}
			$rescode = 0;
			$resmsg = "Berhasil";
			$project = explode(";",$project);
			$get = json_decode($this->ci->gcloud->stop_instance($project[0],str_replace("zones/","",$project[1]), $project[2]), true);
			if($get["result_code"] != 0){
				$rescode = 3;
				$resmsg = $get["result_msg"];
			}else{
				$resmsg = $get["result_msg"];
			}
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			$this->load->view("login");
		}
	}

	public function select(){
		$draw = $_POST['draw'];
		$row = $_POST['start'];
		$rowperpage = $_POST['length']; // Rows display per page
		$rowperpage += $row;
		#//log_message("debug","check data response ".json_encode($_POST));
		#$columnIndex = $_POST['order'][0]['column']; // Column index
		$columnIndex = 0;
		$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
		#$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
		$searchValue = $_POST['search']['value']; // Search value
		$addFilter = "";
		## Search 
		$searchQuery = "";
		if($searchValue != ''){
			$searchQuery = " where (ip like '%".$searchValue."%' or user like '%".$searchValue."%' or project like '%".$searchValue."%') ";
			if($addFilter != ""){
				$searchQuery .= $searchQuery." and ".$addFilter;
			}
		}
		$resAll = json_decode($this->ci->roudbmodel->openQueryJson("select DISTINCT ip, MAX(tgl_jam_buat) as tgl_jam_buat, user, status, project from tb_log_ip GROUP BY ip, USER, status, project"), TRUE);
		$totalRecords = count($resAll["data_openq"]);
		$resAllFilter = json_decode($this->ci->roudbmodel->openQueryJson("select DISTINCT ip, MAX(tgl_jam_buat) as tgl_jam_buat, user, status, project from tb_log_ip $searchQuery GROUP BY ip, USER, status, project"), TRUE);
		$totalRecordwithFilter = count($resAllFilter["data_openq"]);

		$empQuery = "select DISTINCT ip, MAX(tgl_jam_buat) as tgl_jam_buat, user, status, project from tb_log_ip".$searchQuery." GROUP BY ip, USER, status, project ORDER BY tgl_jam_buat DESC limit ".$row.",".$rowperpage;
		$res = json_decode($this->ci->roudbmodel->openQueryJson($empQuery), TRUE);
		$data = array();
		// //log_message("debug","check data response1a ".json_encode($res));
		foreach($res["data_openq"] as $key => $value){
			$tempdata = array();
			$tempdata["user"] = $value["user"];
			$tempdata["ip"] = $value["ip"];
			$tempdata["status"] = $value["status"];
			$tempdata["date"] = date("Y-m-d H:i:s", strtotime($value["tgl_jam_buat"]." +5 hours"));
			$tempdata["project"] = $value["project"];
			$data[] = $tempdata;
		}
		
		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"data" => $data
		);
		echo json_encode($response);
	}

	public function selectList(){
		$username = $this->getUsername();
		$draw = $_POST['draw'];
		$row = $_POST['start'];
		$rowperpage = $_POST['length'];
		$rowperpage += $row;
		$columnIndex = 0;
		$columnName = $_POST['columns'][$columnIndex]['data'];
		$searchValue = $_POST['search']['value'];
		$addFilter = "";
		$searchQuery = "";
		$sql = "SELECT telegram FROM tb_m_user where nama_user = '$username'";
		$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
		$tele = '';
		if ($proses["result_code"] == 4){
			$tele = $proses["data_openq"][0]["telegram"];
			$tele = "where tele = '$tele'";
		}
		if($searchValue != ''){
			$searchQuery = "(ip like '%".$searchValue."%' or tele like '%$searchValue%' or project like '%$searchValue%' or platform like '%$searchValue%')";
		}

		if($tele != ''){
			if($searchQuery != ''){
				$searchQuery = "$tele and $searchQuery";
			}else{
				$searchQuery = $tele;
			}
		}else if($tele == '' && $searchQuery != ''){
			$searchQuery = "where $searchQuery";
		}

		$resAll = json_decode($this->ci->roudbmodel->openQueryJson("select * from tb_m_ip"), TRUE);
		$totalRecords = count($resAll["data_openq"]);
		$resAllFilter = json_decode($this->ci->roudbmodel->openQueryJson("select * from tb_m_ip $searchQuery"), TRUE);
		$totalRecordwithFilter = count($resAllFilter["data_openq"]);

		$empQuery = "select * from tb_m_ip ".$searchQuery." ORDER BY no_urut ASC limit ".$row.",".$rowperpage;
		//log_message("debug","query with filter $empQuery");
		$res = json_decode($this->ci->roudbmodel->openQueryJson($empQuery), TRUE);
		$data = array();
		foreach($res["data_openq"] as $key => $value){
			$sql = "SELECT distinct count(*) as hasil FROM tb_log_ip where ip = '".$value['ip']."'";
			$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
			$stat = true;
			if ($proses["result_code"] == 4 && $proses["data_openq"][0]["hasil"] > 0) {
				$stat = false;
			}
			$tempdata["ip"] = $value['ip'];
			$tempdata["tele"] = $value['tele'];
			$tempdata["project"] = $value['project'];
			$tempdata["status"] = $stat? 'Aktif':'Non Aktif';
			$tempdata["platform"] = $value['platform'];
			$tempdata["action"] = '<div class="dropdown">
			<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
				<i class="bx bx-dots-vertical-rounded"></i>
			</button>
			<div class="dropdown-menu">
				<a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> Delete</a>
			</div>
			</div>';
			$data[] = $tempdata;
		}

		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"data" => $data
		);
		echo json_encode($response);
	}

	function getProjectList(){
		$username = $this->getUsername();
		$draw = $_POST['draw'];
		$row = $_POST['start'];
		$rowperpage = $_POST['length'];
		$rowperpage += $row;
		$columnIndex = 0;
		$columnName = $_POST['columns'][$columnIndex]['data'];
		$searchValue = $_POST['search']['value'];
		$addFilter = "";
		$searchQuery = "";
		if($searchValue != ''){
			$searchQuery = "and (nama_project like '%".$searchValue."%' or email like '%$searchValue%')";
		}

		$resAll = json_decode($this->ci->roudbmodel->openQueryJson("select * from tb_m_project"), TRUE);
		$totalRecords = count($resAll["data_openq"]);
		$resAllFilter = json_decode($this->ci->roudbmodel->openQueryJson("select * from tb_m_project where nama_user = '$username' $searchQuery"), TRUE);
		$totalRecordwithFilter = count($resAllFilter["data_openq"]);

		$empQuery = "select * from tb_m_project where nama_user = '$username' ".$searchQuery." limit ".$row.",".$rowperpage;
		// //log_message("debug","query with filter $empQuery");
		$res = json_decode($this->ci->roudbmodel->openQueryJson($empQuery), TRUE);
		$data = array();
		foreach($res["data_openq"] as $key => $value){
			$stat = false;
			try {
				$resdata = $this->ci->gcloud->list_all_instances($value['nama_project']);
			}
			catch(Exception $e) {
				$stat = true;
			}
			$tempdata["project"] = $value['nama_project'];
			$tempdata["email"] = $value['email'];
			$tempdata["status"] = $stat? 'Suspicious Activity':'Aman';
			$tempdata["action"] = '<div class="dropdown">
			<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
				<i class="bx bx-dots-vertical-rounded"></i>
			</button>
			<div class="dropdown-menu">
				<a class="dropdown-item" href="#" id="'.$value['nama_project'].'" onclick=deleteProject(this.id)><i class="bx bx-trash me-1"></i> Delete</a>
			</div>
			</div>';
			$data[] = $tempdata;
		}

		$response = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"data" => $data
		);
		echo json_encode($response);
	}
	
	function getIpDb(){
		if($this->cekSession()){
			$username = $this->getUsername();
			$rescode = 0;
			$resdata = '';
			$resmsg = '';
			$nama = $_POST["user"];
			$project = $_POST["cmbMaster"] == "vpsdo"? "do":"gcp";
			$where = '';
			$list = '';
			$sql = "SELECT telegram FROM tb_m_user where nama_user = '$username'";
			$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
			$tele = "";
			$or = "";
			if ($proses["result_code"] == 4){
				$tele = $proses["data_openq"][0]["telegram"];
			}
			if($nama == "nontw"){
				$sql = "SELECT GROUP_CONCAT(ip SEPARATOR ' ; ') as ip FROM tb_m_ip WHERE tele LIKE '%$tele%' AND project NOT LIKE '%tw%'";
			}else{
				if($project == "do"){
					$where = "and platform like '%$project%'";
				}else{
					$where = "and platform not like '%do%'";
				}
				if($nama == "tw"){
					$or = "and project like '%tw%'";
				}
				$sql = "SELECT ip FROM tb_m_ip where (tele like '%$tele%' $or ) $where";
			}
			//log_message("debug","coba cek dulu lah woii $sql");
			$proses = json_decode($this->ci->roudbmodel->openQueryJson($sql), TRUE);
			//log_message("debug","coba cek dulu lah woii 1 ".json_encode($proses));
			if ($proses["result_code"] == 4 && count($proses["data_openq"])>0){
				foreach($proses["data_openq"] as $key => $value){
					if($list == ""){
						$list = $value["ip"];
					}else{
						$list .= ";".$value["ip"];
					}
				}
				$resmsg = "Berhasil mengambil ip";
			}else{
				$rescode = 3;
				$resmsg = "Data tidak ditemukan. silahkan input ip terlebih dahulu";
			}
			$resdata = $list;
			$data = json_encode(
				array(
					'result_code' => $rescode, 
					'result_msg' => $resmsg,
					'result_data' => $resdata
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}else{
			redirect('http://167.86.127.69/exorde/login', 'refresh');
			$data = json_encode(
				array(
					'result_code' => 3, 
					'result_msg' => "Login dulu",
					'result_data' => false
				)
			);

			$this->ci->output
					->set_content_type('application/json')
					->set_output($data);
		}
	}

	function deleteProject(){
		if($this->cekSession()){
			$resmsg = '';
			$rescode = 0;
			$lama = $_POST["projectName"];
			if($lama != ""){
				if($this->checkProjectExists($lama)){
					$sqlDelete = "delete from tb_m_project where nama_project = '$lama'";
					$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlDelete), true);
					if ($execInsert["result_code"] == 0) {
						$file = "assets/key/$lama.json";
						if (file_exists($file)) {
							unlink($file);
						}
						$sqlDelete = "delete from tb_m_ip where project = '$lama'";
						$execInsert = json_decode($this->ci->roudbmodel->ExecQueryJSon($sqlDelete), true);
						$rescode = 0;
						$resmsg = "Berhasil menghapus project";
					}else{
						$rescode = 3;
						$resmsg = "Gagal menghapus project";
					}
				}else{
					$rescode = 3;
					$resmsg = "Project $lama tidak ditemukan di database";
				}
			}else{
				$rescode = 3;
				$resmsg = "Project name tidak boleh kosong";
			}
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(["result_code"=>$rescode,"result_msg"=>$resmsg]));
		}else{
			$this->load->view("login");
		}
		
	}
}
