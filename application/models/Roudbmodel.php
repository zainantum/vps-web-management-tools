<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Roudbmodel extends CI_Model {
  
    function __construct()
    {
      // Call the Model constructor
      parent::__construct();
      
      $this->load->database();
      date_default_timezone_set("Asia/Jakarta");
    }	
    
    function ExecQueryJSon($SqlStr){
      if ($SqlStr !== ''){
        $this->db->trans_begin();
        $this->db->query($SqlStr);
        //$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
          $errMsg = $this->db->error(); 
          // $rescode = $errMsg["code"];
          $resmsg = $errMsg["message"];
          $rescode = 3;
          $resmsg = "Query gagal dieksekusi. $resmsg";
          //$details = $result->row_array();
          $this->db->trans_rollback();
        } else {
          $rescode = 0;
          $resmsg = "Query berhasil dieksekusi.";
          $this->db->trans_commit();
        }
        return json_encode(array('result_code'=>$rescode, 'result_msg'=>$resmsg));
      } else {				
        $rescode = 5;
        $resmsg = "Ada kesalahan saat proses eksekusi.";
        return json_encode(array('result_code'=>$rescode, 'result_msg'=>$resmsg));	
      }			
    }
    
    function OpenQueryJSon($SqlStr){
      $result = true;
      $sqlStrCek =  strtolower($SqlStr);

      // $check_query = is_null($check_query)?true:$check_query;
      $conn = $this->db->conn_id;
      do {
          if ($result = mysqli_store_result($conn)) {
              mysqli_free_result($result);
          }
      } while (mysqli_more_results($conn) && mysqli_next_result($conn));
      $result = $this->db->query($SqlStr);
      
      if ($result){
        $res = $result->result();
        if (preg_match('/^call/', $sqlStrCek)) {
          $result->next_result();
          $result->free_result();
        }

        $row = $result->row();
        $details = array();
        if (isset($row)){
          $rescode = 4;
          $resmsg = "Data ditemukan.";
          //$details = $result->row_array();
          $details = array_values($res);
        } else {
          $rescode = 3;
          $resmsg = "Data tidak ditemukan.";
          $details = [];
        }
        // log_message('debug','cek masuk ke stropa sal3 [OpenQueryJSon] after '.json_encode($details));
        return json_encode(
          array(
            'result_code'=>$rescode, 
            'result_msg'=>$resmsg, 
            'data_openq'=>$details
          )
        );
      } else {
        $errMsg = $this->db->error(); 
        $rescode = $errMsg["code"];
        $resmsg = $errMsg["message"];

        return json_encode(
          array(
            'result_code'=>$rescode, 
            'result_msg'=>$resmsg
          )
        );	
      }
    }

    function execMultiQuery()
    {
      
    }
    
    function convertToArr($fieldValueData, $tableName="") {
      $data = array();
      if ($fieldValueData != ''){
        $fieldValueArr = explode(';', $fieldValueData);
        if ($tableName!==""){
          $fieldData = $this->db->field_data($tableName);
        }
        foreach ($fieldValueArr as $value) {
          $keyVal = explode('=>', $value);
          //bukan data kosong
          if ((strtolower($keyVal[1]) != 'null') && ($keyVal[1] != '')) {
            //ubah semua karakter petik sat (') maupun petik dua (")
            $val = str_replace("'", ' ', $keyVal[1]);
            $val = str_replace("\"", ' ', $val);
            if (substr($keyVal[1], 0, 1) != '+') {
            //tidak ditemukan string awal '+', tambahkan escaped
              $key = $keyVal[0];
              if ((strtolower($val) == "now()") || (strtolower($val) == "now(3)") || (strtolower($val) == "now")) {
                // SET @@session.time_zone = "+07:00";
                $tgl = $this->syslib->getServerDateTime();
                // $val = date("Y-m-d H:i:s");
                $val = $tgl;
              }
            } else {
              //cek apakah field tujuan, type field => numeric
              if (($tableName==="")||(($tableName!=="")&&($this->routinesdb->isNumericFieldType($fieldData, $keyVal[0], $fieldDataType)))) {
                $key = $keyVal[0];
                $val = str_replace("+", "", $keyVal[1]);
                if ((strtolower($fieldDataType)=="integer")||(strtolower($fieldDataType)=="smallint"))
                {
                  //jika tipe data int
                  $val = intval($val);
                } else{
                  //tipe data selain int
                  $val = floatval($val);
                }
              } else {
                $key = $keyVal[0];
                $val=$keyVal[1];
              }
            }
            $data[$key] = $val;
          } else {
            $key = $keyVal[0];
            $data[$key] = NULL;
          }
        }
      }
      return $data;
    }
    
    function insertData($tableName, $fieldValueData) {
        $data = $this->convertToArr($fieldValueData, $tableName);
        return $this->insertDataArr($tableName, $data);
      }
      
      function insertDataArr($tableName, $data){
      /*$sql = $this->db->set($data)->get_compiled_insert($tableName);
      echo $sql;*/
      $this->db->insert($tableName, $data);
        
      $errMsg = $this->db->error();				
      $rescode = $errMsg["code"];
      $resmsg = $errMsg["message"];
      
      if ($rescode == 0){
        //proses insert berhasil
        $resmsg = "Proses Insert data telah berhasil.";
      }
      return json_encode(array('result_code'=>$rescode, 'result_msg'=> $resmsg));
    }	 
    
    function updateData($tableName, $fieldValueData, $whereList) {
      $data = $this->convertToArr($fieldValueData, $tableName);
      //cek apakah ada string penanda conversi ke array ("=>")
      if( strpos($whereList, "=>") !== false ) {			 			 	
        $where = $this->convertToArr($whereList);
      } else {
        $where = $whereList;
      }
      return $this->updateDataArr($tableName, $data, $where);
    }
    
    function updateDataArr($tableName, $data, $where){	      
      if (is_array($where)){
        $this->db->update($tableName, $data, $where);
      } else {
        if ($where !== ''){
          $this->db->where($where);
        }
        $this->db->update($tableName, $data);
      }
        
      $errMsg = $this->db->error();
      $rescode = $errMsg["code"];
      $resmsg = $errMsg["message"];
      // log_message("debug","db error: ".json_encode($errMsg));
      if ($rescode == 0){
        //proses insert berhasil
        $rescode = 0;
        $resmsg = "Proses Update data telah berhasil.";
      } 
      return json_encode(array('result_code'=>$rescode, 'result_msg'=> $resmsg));
      //return json_encode(array('result_code'=>0, 'result_msg'=> 'masuk'));
    }	
    
    function deleteData($tableName, $whereList) {
      //cek apakah ada string penanda conversi ke array ("=>")
      if( strpos($whereList, "=>") !== false ) {			 			 	
       $where = $this->convertToArr($whereList);
      } else {
       $where = $whereList;
      }
      return $this->deleteDataArr($tableName, $where);
    }
    
    function deleteDataArr($tableName, $where){
      if (is_array($where)){
        $this->db->delete($tableName, $where);
      } else {
        if ($where !== ''){
          $this->db->where($where);
        }
        $this->db->delete($tableName);
      }
        
      $errMsg = $this->db->error();				
      $rescode = $errMsg["code"];
      $resmsg = $errMsg["message"];
      
      if ($rescode == 0){
        //proses insert berhasil
        $resmsg = "Proses Delete data telah berhasil.";
      }
      return json_encode(array('result_code'=>$rescode, 'result_msg'=> $resmsg));
    }	

  function getLastID(){
    /* Jangan dihapus!
      * Panggil fungsi ini setelah proses INSERT INTO untuk mengambil ID(primary key) terakhir
    */ 
    $lastID = $this->db->insert_id();
    return $lastID;
  }
}
?>
