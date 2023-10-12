<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to exorde log</title>
	<!-- Datatable CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link href='https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>

	<!-- jQuery Library -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

	<!-- Datatable JS -->
	<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
	<style type="text/css">

	::selection { background-color: #E13300; color: white; }
	::-moz-selection { background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
		text-decoration: none;
	}

	a:hover {
		color: #97310e;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body {
		margin: 0 15px 0 15px;
		min-height: 96px;
	}

	p {
		margin: 0 0 10px;
		padding:0;
	}

	p.footer {
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container {
		margin: 10px;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

<div id="container">
	<div class="card">
		<div class="card-header">
			Pantau VPS
		</div>
		<div class="card-body">
			<form class="form-horizontal" id="frmIsian" enctype="multipart/form-data" method="post" accept-charset="utf-8">
				<div class="row">
					<div class="col-md-12" style="margin: 10px">
					<input type="hidden" id="action" name="action" value="addIpSection">
						<div class="form-group col-md-6">
							<label for="inputState">Pilih tindakan....</label>
							<select id="inputState" class="form-control" onchange="cekIpLama(this.value)">
								<option value="addIpSection">Tambah IP Baru</option>
								<option value="editIpSection">Edit IP</option>
								<option value="deleteIpSection">Hapus IP</option>
							</select>
						</div>
						<div class="col-md-6" id="addIpSection">
							<div class="form-group">
								<label for="exampleInputEmail1">Masukkan IP Baru</label>
								<input class="form-control" id="ipBaru" name="ipBaruAdd" placeholder="Enter ip">
							</div>
						</div>
						<div id="editIpSection" style="display: none">
							<div class="col-md-6">
								<div class="form-group">
									<label for="exampleInputEmail1">IP Lama</label>
									<input class="form-control" id="ipLama" name="ipLamaEdit" placeholder="Enter ip">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="exampleInputEmail1">IP Baru</label>
									<input class="form-control" id="ipBaru" name="ipBaruEdit" placeholder="Enter ip">
								</div>
							</div>
						</div>
						<div class="col-md-6" id="deleteIpSection" style="display: none">
							<div class="form-group">
								<label for="exampleInputEmail1">Masukkan IP yg mau dihapus</label>
								<input class="form-control" id="ipLama" name="ipLamaDelete" placeholder="Enter ip">
							</div>
						</div>
						<button type="button" class="btn btn-primary" id="btnUpdate" onclick="updateData()" style="margin: 20px">Submit</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-header">
			Buat, stop, start dan hapus vps dari sini (sudah bisa digunakan)
		</div>
		<div class="card-body">
			<form class="form-horizontal" id="frmIsianVps" enctype="multipart/form-data" method="post" accept-charset="utf-8">
				<div class="row">
					<div class="col-md-12" style="margin: 10px">
					<input type="hidden" id="action2" name="action2" value="addAkunSection">
						<div class="form-group col-md-6">
							<label for="inputState">Pilih tindakan....</label>
							<select id="inputState" class="form-control" onchange="cekTindakan(this.value)">
								<option>Pilih...</option>
								<option value="addAkunSection">Tambahkan akun</option>
								<option value="addProjectSection">Tambahkan projek dan credential</option>
								<option value="actionProjectSection">Akses gc (buat dan hapus vps)</option>
							</select>
						</div>
						<div id="forAppend">

						</div>
						<div id='loader' style='display: none;'>
							<img src='http://167.86.127.69/exorde/assets/loader.gif' width='150px' height='150px'>
						</div>
						<button type="button" class="btn btn-primary" id="insertDataVps" onclick="insertDataVpsFunc()" style="margin: 20px">Submit</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div id="addAkunSection" style="display: none">
		<div class="alert alert-secondary" role="alert">
			Akun ini akan digunakan untuk proses buat, start, stop dan hapus vps di sini. Password yg diinput akan di-encrypt dan disimpan ke database secara aman.
		</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="exampleInputEmail1">Buat username</label>
					<input class="form-control" id="username" name="username" placeholder="Enter username">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="exampleInputEmail1">Buat password</label>
					<input class="form-control" id="password" name="password" type="password" placeholder="Enter password">
				</div>
			</div>
	</div>

	<div class="col-md-12" id="addProjectSection" style="display: none">
		<div class="form-group">
			<label for="exampleInputEmail1">Masukkan username</label>
			<input class="form-control" id="username" name="username" placeholder="Enter username">
		</div>
		<div class="form-group">
			<label for="exampleInputEmail1">Masukkan password</label>
			<input class="form-control" id="password" name="password" type="password" placeholder="Enter password">
		</div>
		<div class="form-group">
			<label for="exampleInputEmail1">Masukkan nama project</label>
			<input class="form-control" id="project" name="project" placeholder="Enter project">
		</div>
		<div class="form-group">
			<label for="exampleInputEmail1">Masukkan email dari project ini</label>
			<input class="form-control" id="emailproject" name="emailproject" placeholder="Enter email">
		</div>
		<div class="form-group">
			<label for="exampleFormControlFile1">Lampirkan file credential dengan format .json</label>
			<input type="file" class="form-control-file" id="fileJson" accept="application/JSON">
		</div>
	</div>

	<div class="col-md-12" id="actionProjectSection" style="display: none">
		<div class="form-group">
			<label for="exampleInputEmail1">Masukkan username</label>
			<input class="form-control" id="username" name="username" placeholder="Enter username">
		</div>
		<div class="form-group">
			<label for="exampleInputEmail1">Masukkan password</label>
			<input class="form-control" id="password" name="password" type="password" placeholder="Enter password">
		</div>
		<button type="button" class="btn btn-info" id="getList" onclick="cekTindakanProject('list')">Get project list</button>
		<div class="form-group">
			<label for="inputState">Pilih project....</label>
			<select id="cmbProject" name="cmbProject" class="form-control" onchange="aturButton(this.value)">
				<option>Pilih...</option>
			</select>
		</div>
		<div class="alert alert-secondary" role="alert">
			Jika ingin membuat vps baru, silahkan pilih project. Jika ingin menghapus vps, silahkan pilih vps yg mau dihapus.
		</div>
		<div class="form-group" id="ramVpsDiv" style="display: none">
			<label for="inputState">Pilih ram....</label>
			<select id="ramVps" name="ramVps" class="form-control">
				<option>Pilih...</option>
				<option value="2">E2 - Ram 2GB</option>
				<option value="4">E2 - Ram 4GB</option>
				<option value="8">E2 - Ram 8GB</option>
				<option value="22">T2D - Ram 2GB</option>
				<option value="44">T2D - Ram 4GB</option>
				<option value="88">T2D - Ram 8GB</option>
			</select>
		</div>
		<div class="form-group" id="zonaDiv" style="display: none">
			<label for="inputState">Pilih zona....</label>
			<select id="zona" name="zona" class="form-control">
				<option>Pilih...</option>
				<option value="us-central1-a">us-central1-a</option>
				<option value="us-east1-b">us-east1-b</option>
				<option value="us-west1-b">us-west1-b</option>
			</select>
		</div>
		<div id="loading" style="display: none">
			<div class="d-flex justify-content-center">
				<div class="spinner-border" role="status">
					<span class="sr-only">Loading...</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<button type="button" class="btn btn-primary mb-2" id="newVps" style="display: none" onclick="cekTindakanProject('newVps')">Buat vps</button>
			<button type="button" class="btn btn-success mb-2" id="startVps" style="display: none" onclick="cekTindakanProject('startVps')">Start vps</button>
			<button type="button" class="btn btn-secondary mb-2" id="stopVps" style="display: none" onclick="cekTindakanProject('stopVps')">Stop vps</button>
			<button type="button" class="btn btn-danger mb-2" id="delVps" style="display: none" onclick="cekTindakanProject('delVps')">Hapus vps</button>
		</div>

	</div>
		

	<!-- Table -->
	<table id='empTable' class='display dataTable'>

	<thead>
		<tr>
			<th>User</th>
			<th>IP</th>
			<th>Status</th>
			<th>Date</th>
		</tr>
	</thead>

	</table>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	$(document).ready(function(){
	$('#empTable').DataTable({
		destroy: true,
		"lengthMenu": [10, 25, 50, 75, 100],
		"pageLength": 10,
		"processing": false,
		"serverSide": true,
		// "retrieve": true, 
		"bAutoWidth": false,
		"stateSave": false,
		"scrollCollapse": true,
		"ajax": {
			url: 'http://167.86.127.69/exorde/api/select',
			type: "POST",
			
		},
		'columns': [
			{ data: 'user' },
			{ data: 'ip' },
			{ data: 'status' },
			{ data: 'date' },
		]
	});
	});
</script>
<script>
	function updateData(){
		$.ajax({
			type: "POST",
			url: "http://167.86.127.69/exorde/api/update",
			data: $('#frmIsian').serializeArray(),
			success: function(msg) {
				if(msg.result_code == 0){
					Swal.fire(
						'Success!',
						msg.result_msg,
						'success'
					)
				}else{
					Swal.fire(
						'Error!',
						msg.result_msg,
						'error'
					)
				}
			}
		});
	}

	function cekIpLama(data){
		let opt = ['addIpSection','editIpSection','deleteIpSection']
		$.each(opt, function(key, value){
			if(data == value){
				$("#"+value).css("display","block")
				$("#action").val(value)
			}else{
				$("#"+value).css("display","none")
			}
		});
	}

	function cekTindakan(data){
		if(data == "addAkunSection"){
			$('#forAppend').empty()
			$('#forAppend').append($('#addAkunSection').clone());
			$("#forAppend #addAkunSection").css("display","block")
			$("#action2").val('addAkunSection')
			$("#insertDataVps").prop('disabled', false)
		}else if(data == "addProjectSection"){
			$('#forAppend').empty()
			$('#forAppend').append($('#addProjectSection').clone());
			$("#forAppend #addProjectSection").css("display","block")
			$("#action2").val('addProjectSection')
			$("#insertDataVps").prop('disabled', false)
		}else if(data == "actionProjectSection"){
			$('#forAppend').empty()
			$('#forAppend').append($('#actionProjectSection').clone());
			$("#forAppend #actionProjectSection").css("display","block")
			$("#action2").val('actionProjectSection')
			$("#insertDataVps").prop('disabled', true)
		}
	}

	function aturButton(data){
		if(data.includes(";")){
			$("#ramVpsDiv").css("display","none")
			$("#zonaDiv").css("display","none")
			$("#newVps").css("display","none")
			$("#delVps").css("display","block")
			$("#startVps").css("display","block")
			$("#stopVps").css("display","block")
		}else{
			$("#ramVpsDiv").css("display","block")
			$("#zonaDiv").css("display","block")
			$("#newVps").css("display","block")
			$("#delVps").css("display","none")
			$("#startVps").css("display","none")
			$("#stopVps").css("display","none")
		}
	}

	function cekTindakanProject(data){
		$("#loader").css("display","block")
		if(data == "list"){
			$.ajax({
				type: "POST",
				url: "http://167.86.127.69/exorde/api/getList",
				data: $('#frmIsianVps').serializeArray(),
				success: function(msg) {
					if(msg.result_code == 0){
						$("#cmbProject").empty()
						$("#cmbProject").append('<option>Pilih...</option>'+msg.result_data)
						Swal.fire(
							'Success!',
							msg.result_msg,
							'success'
						)
						$("#loader").css("display","none")
					}else{
						Swal.fire(
							'Error!',
							msg.result_msg,
							'error'
						)
						$("#loader").css("display","none")
					}
				}
			});
		}else if(data == "newVps"){
			$.ajax({
				type: "POST",
				url: "http://167.86.127.69/exorde/api/newVps",
				data: $('#frmIsianVps').serializeArray(),
				success: function(msg) {
					if(msg.result_code == 0){
						Swal.fire(
							'Success!',
							msg.result_msg,
							'success'
						)
						$("#loader").css("display","none")
					}else{
						Swal.fire(
							'Error!',
							msg.result_msg,
							'error'
						)
						$("#loader").css("display","none")
					}
				}
			});
		}else if(data == "delVps"){
			$.ajax({
				type: "POST",
				url: "http://167.86.127.69/exorde/api/delVps",
				data: $('#frmIsianVps').serializeArray(),
				success: function(msg) {
					if(msg.result_code == 0){
						Swal.fire(
							'Success!',
							msg.result_msg,
							'success'
						)
						$("#loader").css("display","none")
					}else{
						Swal.fire(
							'Error!',
							msg.result_msg,
							'error'
						)
						$("#loader").css("display","none")
					}
				}
			});
		}else if(data == "startVps"){
			$.ajax({
				type: "POST",
				url: "http://167.86.127.69/exorde/api/startVps",
				data: $('#frmIsianVps').serializeArray(),
				success: function(msg) {
					if(msg.result_code == 0){
						Swal.fire(
							'Success!',
							msg.result_msg,
							'success'
						)
						$("#loader").css("display","none")
					}else{
						Swal.fire(
							'Error!',
							msg.result_msg,
							'error'
						)
						$("#loader").css("display","none")
					}
				}
			});
		}else if(data == "stopVps"){
			$.ajax({
				type: "POST",
				url: "http://167.86.127.69/exorde/api/stopVps",
				data: $('#frmIsianVps').serializeArray(),
				success: function(msg) {
					if(msg.result_code == 0){
						Swal.fire(
							'Success!',
							msg.result_msg,
							'success'
						)
						$("#loader").css("display","none")
					}else{
						$("#loader").css("display","none")
						Swal.fire(
							'Error!',
							msg.result_msg,
							'error'
						)
					}
				}
			});
		}
		// $("#loader").css("display","none")
	}

	function insertDataVpsFunc(){
		// let dataForm = $('#frmIsianVps').serializeArray()
		let formData = new FormData();
			if($('#frmIsianVps #action2').val() == "addProjectSection"){
				let fileupload = $('#frmIsianVps #addProjectSection #fileJson').prop('files')[0];
				if (fileupload!="") {
					formData.append('fileupload', fileupload);
				}else{
					Swal.fire(
							'Error!',
							'File tidak boleh kosong',
							'error'
						)
				}
			}
			formData.append('emailproject', $('#frmIsianVps #emailproject').val());
			formData.append('project', $('#frmIsianVps #project').val());
			formData.append('username', $('#frmIsianVps #username').val());
			formData.append('password', $('#frmIsianVps #password').val());
			formData.append('action2', $('#frmIsianVps #action2').val());
			$.ajax({
				type: "POST",
				url: "http://167.86.127.69/exorde/api/actionvps",
				data: formData,
				processData: false,
				contentType: false,
				cache: false,
				enctype: 'multipart/form-data',
				success: function(msg) {
					if(msg.result_code == 0){
						Swal.fire(
							'Success!',
							msg.result_msg,
							'success'
						)
					}else{
						Swal.fire(
							'Error!',
							msg.result_msg,
							'error'
						)
					}
				}
			});	
		// }else{
		// 	Swal.fire(
		// 			'Error!',
		// 			'File tidak boleh kosong',
		// 			'error'
		// 		)
		// }
		
	}
</script>
</body>
</html>
