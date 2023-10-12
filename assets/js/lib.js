$(document).ready(function(){
	
    dbgM()
    reloadTableProject()
    $('#listTable').DataTable({
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
			url: '/exorde/api/selectList',
			type: "POST",
			
		},
		'columns': [
			{ data: 'ip' },
			{ data: 'tele' },
			{ data: 'project' },
			{ data: 'status' },
            { data: 'platform' },
            { data: 'action' },
		]
	});

    $('#empTable tbody').on('click', 'tr', function () {
        if($("#deleteIpSection #ipLama").length > 0){
            var value = $(this).find("td").eq(1).text();
            if($("#deleteIpSection #ipLama").val().includes(value)){
                Swal.fire({
                    position: 'center',
                    icon: 'info',
                    title: 'IP '+value+' udah ada di list lo. gak usah ditambahin lagi ',
                    showConfirmButton: false,
                    timer: 1500
                  })
            }else{
                Swal.fire({
                    title: 'Tambahkan IP '+value+' ke daftar IP yg akan dihapus?',
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: 'Tambahkan',
                    denyButtonText: `Gak usah deh`,
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        if($("#deleteIpSection #ipLama").val() == ""){
                            $("#deleteIpSection #ipLama").val(value)
                        }else if($("#deleteIpSection #ipLama").val().includes(value) == false){
                            $("#deleteIpSection #ipLama").val($("#deleteIpSection #ipLama").val()+";"+value)
                        }
                        // Swal.fire('IP '+value+' berhasil ditambahkan!', '', 'success')
                    } else if (result.isDenied) {
                        // Swal.fire('Oke gajadi ditambahkan deh', '', 'info')
                    }
                })
            }
            

            // console.log(value);
        }
    });
    
});

function dbgM(){
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
			url: '/exorde/api/select',
			type: "POST",
			
		},
		'columns': [
			{ data: 'user' },
			{ data: 'ip' },
			{ data: 'status' },
			{ data: 'date' },
            { data: 'project' },
		]
	});
}

function reloadTableProject(){
    $('#listProject').DataTable({
		destroy: true,
		"lengthMenu": [10, 25, 50, 75, 100],
		"pageLength": 10,
		"serverSide": true,
		// "retrieve": true, 
        'language':{ 
            "loadingRecords": "&nbsp;",
            "processing": "Loading..."
         },
		"bAutoWidth": false,
		"stateSave": false,
		"scrollCollapse": true,
		"ajax": {
			url: '/exorde/api/getProjectList',
			type: "POST",
			
		},
		'columns': [
			{ data: 'project' },
            { data: 'email' },
            { data: 'status' },
            { data: 'action' },
		]
	});
}

function postData(frmIsian, urlPost, callbackSuccess, callbackError){
    let dataPost = $('#'+frmIsian).serializeArray()
    $.ajax({
        type: "POST",
        url: urlPost,
        data: dataPost,
        success: function(msg) {
            if(msg.result_code == 0){
                if(msg.result_msg != "" && urlPost.includes("Command") && $("#frmIsianVps #ipCommand").val() != "" && $("#frmIsianVps #ipCommand").val() != null){
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: msg.result_msg,
                        showConfirmButton: false,
                        timer: 1500
                      })
                }else{
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: msg.result_msg,
                        showConfirmButton: false,
                        timer: 1500
                      })
                }
                
                if(urlPost.includes("getListIp")){
                    $("#ipVpsDiv").css("display","block")
                    $("#cmbProjectIp").empty()
                    if(msg.result_data == ""){
                        $("#cmbProjectIp").append('<option value="0">Pilih...</option><option value="buat">Buat vps</option>')
                    }else{
                        $("#cmbProjectIp").append('<option value="0">Pilih...</option><option value="buat">Buat vps</option><option value="delAll">Hapus semua vps</option><option value="command">Command</option>'+msg.result_data)
                    }
                }else if(urlPost.includes("getList")){
                    $("#cmbProject").empty()
                    $("#cmbProject").append('<option value="0">Pilih...</option>'+msg.result_data)
                }else if(urlPost.includes("Command")){
                    $("#modalLoading").modal("hide")
                    if($("#frmIsianVps #ipCommand").val() != "" && $("#frmIsianVps #ipCommand").val() != null){
                        $("#fakeScreen").html(msg.result_data)
                        $("#frmIsianVps #ipCommand").val('')
                    }else{
                        $("#fakeScreen").append(msg.result_data)
                    }
                    $("#item-1").prop("checked",false)
                }else if(urlPost.includes("loadMasterO")){
                    $("#forAppend").html(msg.result_data)
                }else if(urlPost.includes("loadDetailO")){
                    $("#forAppendDetail").html(msg.result_data)
                }
                if(callbackSuccess != undefined){
                    callbackSuccess()
                }
            }else{
                if(urlPost.includes("Command")){
                    $("#modalLoading").modal("hide")
                    $("#fakeScreen").html('')
                    $("#frmIsianVps #ipCommand").val('')
                    $("#item-1").prop("checked",false)
                }
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: msg.result_msg,
                    showConfirmButton: false,
                    timer: 1500
                  })
                if(callbackError != undefined){
                    callbackError()
                }
            }
        }
    });
}

function aturCommand(exec){
    if(exec == "1"){
        $("#detailInfoSection").css("display","block")
        $("#customSection").css("display","none")
    }else if(exec == "99"){
        $("#customSection").css("display","block")
        $("#detailInfoSection").css("display","none")
    }else{
        $("#detailInfoSection").css("display","none")
        $("#customSection").css("display","none")
    }
}

function updateData(){
    postData("frmIsian","/exorde/api/update",dbgM())
}

function doLogin(){
    postData("frmIsianLogin","/exorde/doLogin",function(){
            $(':button').prop('disabled', false);
            window.location.replace("/exorde/akses");
        }, function(){
            $(':button').prop('disabled', false);
        })
}

function doRegister(){
    postData("frmIsianRegister","/exorde/doRegister",function(){
            $(':button').prop('disabled', false);
            window.location.replace("/exorde/login");
        }, function(){
            $(':button').prop('disabled', false);
        })
}

function addAllVpsToWatchList(){
    postData("frmIsianVps","/exorde/api/addList",function(){
            // $("#loader").css("display","none")
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        }, function(){
            // $("#loader").css("display","none")
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        })
}

function deleteAllIp(){
    $("#modalLoading").modal("show")
    $(':button').prop('disabled', true);
    postData("frmIsianVps","/exorde/api/delAllVpsProject",function(){
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
            cekTindakanProject('listIp')
        }, function(){
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        })
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

$('body').on('change', '#cmbMaster', function() {
    postData("frmIsianVps","/exorde/api/loadMasterOption",function(){
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        }, function(){
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        })
})

// function aturButton(data){
//     let project = ["ramVpsDiv","zonaDiv","newVps","jmlVps","diskDiv"]
//     let vps = ["delVps", "startVps","stopVps","ipVps"]
//     let btn = ["getListIp", "addList"]
//     if(data.includes(";")){
//         $.each(project, function(key, value){
//             $("#"+value).css("display","none")    
//         });
//         $.each(vps, function(key, value){
//             if(value == "ipVps"){
//                 let ip = $("#cmbProjectIp option:selected").text().split("~")
//                 $("#ipVps").html(ip[1]);
//             }
//             $("#"+value).css("display","block")    
//         });
//     }else if(data == "buat"){
//         $.each(project, function(key, value){
//             $("#"+value).css("display","block")    
//         });
//         $.each(vps, function(key, value){
//             $("#"+value).css("display","none")    
//         });
//         $.each(btn, function(key, value){
//             $("#"+value).prop('disabled', false); 
//         });
//     }else if(data == "buat"){
//         $.each(project, function(key, value){
//             $("#"+value).css("display","block")    
//         });
//         $.each(vps, function(key, value){
//             $("#"+value).css("display","none")    
//         });
//         $.each(btn, function(key, value){
//             $("#"+value).prop('disabled', false); 
//         });
//     }
// }

function aturButtonAll(){
    postData("frmIsianVps","/exorde/api/loadDetailOption",function(){
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        }, function(){
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        })
}

// function aturButtonAll(data){
//     let project = ["ramVpsDiv","zonaDiv","newVps","jmlVps","diskDiv"]
//     let vps = ["delVps", "startVps","stopVps","ipVps","commandSection","ipListMultiA"]
//     let btn = ["getListIp", "addList"]
//     if(data.includes(";")){
//         $.each(project, function(key, value){
//             $("#"+value).css("display","none")    
//         });
//         $.each(vps, function(key, value){
//             if(value == "ipVps"){
//                 let ip = $("#cmbProjectIp option:selected").text().split("~")
//                 $("#ipVps").html(ip[1]);
//             }
//             $("#"+value).css("display","block")    
//         });
//         $("#commandSection").css("display","none")
//         $("#delAllIp").css("display","none")
//     }else if(data == "buat"){
//         $.each(project, function(key, value){
//             $("#"+value).css("display","block")    
//         });
//         $.each(vps, function(key, value){
//             $("#"+value).css("display","none")    
//         });
//         $("#delAllIp").css("display","none")
//     }else if(data == "delAll"){
//         $.each(project, function(key, value){
//             $("#"+value).css("display","none")    
//         });
//         $.each(vps, function(key, value){
//             $("#"+value).css("display","none")    
//         });
//         $("#delAllIp").css("display","block")
//     }else if(data == "command"){
//         $.each(project, function(key, value){
//             $("#"+value).css("display","none")    
//         });
//         $.each(vps, function(key, value){
//             if(value != "commandSection"){
//                 $("#"+value).css("display","none")
//             }    
//         });
//         $("#delAllIp").css("display","none")
//         $("#commandSection").css("display","block")
//     }
// }

function ambilIp(){
    var checked = $("#item-1").is(":checked");
    if(checked){
        const opts = document.querySelectorAll("#cmbProjectIp option"); 
        const vals = [...opts].map(el => el.text); 
        let dataIp = '';
        $.each(vals, function(key,data){
            let da = data.split("~");
            if(data.includes("~")){
                if(dataIp == ''){
                    dataIp = da[1]
                }else{
                    dataIp += ";"+da[1]
                }
            }
        });
        $("#ipCommand").val(dataIp)
    }else{
        $("#ipCommand").val('')
    }
}

function ambilIpDb(){
    var checked = $("#item-2").is(":checked");
    if(checked){
        Swal.fire({
            title: 'answer',
            html: 'tw: ambil semua ip tw <br> nontw: semua ip yg bukan tw <br> all: semua ip',
            input: 'text',
            inputAttributes: {
              autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Ambil ip',
            showLoaderOnConfirm: true,
            preConfirm: (name) => {
                let dataV = $('#frmIsianVps').serializeArray()
                dataV = dataV.concat({name: 'user',value: name})
                $.ajax({
                    type: "POST",
                    url: '/exorde/api/getIpDb',
                    data: dataV,
                    success: function(msg) {
                        if(msg.result_code == 0){
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: msg.result_msg,
                                showConfirmButton: false,
                                timer: 1500
                              })
                              $("#ipCommand").val(msg.result_data)
                        }else{
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                title: msg.result_msg,
                                showConfirmButton: false,
                                timer: 1500
                              })
                        }
                    }
                })
            }
          })
        
    }else{
        $("#ipCommand").val('')
    }
}

function deleteProject(id){
    Swal.fire({
        title: 'Konfirmasi ulang nama project yg akan dihapus<br>',
        html: '<strong>'+id+'</strong>',
        input: 'text',
        inputAttributes: {
          autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Hapus Project',
        showLoaderOnConfirm: true,
        preConfirm: (name) => {
            let dataV = []
            dataV = dataV.concat({name: 'projectName',value: name})
            $.ajax({
                type: "POST",
                url: '/exorde/api/deleteProject',
                data: dataV,
                success: function(msg) {
                    if(msg.result_code == 0){
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: msg.result_msg,
                            showConfirmButton: false,
                            timer: 3500
                          })
                        reloadTableProject()
                    }else{
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: msg.result_msg,
                            showConfirmButton: false,
                            timer: 1500
                          })
                    }
                }
            })
        }
      })
}

function terminal(ip, multi=false){
    let cmd = $("#commandId").val()
    let command = ''
    if(cmd == 1){
        command = 'bash temporary.sh'
    }else if(cmd == 2){
        command = 'docker logs exorde1'
    }else if(cmd == 3){
        command = 'df -h'
    }else if(cmd == 4){
        command = 'docker ps -a'
    }else if(cmd == 5){
        command = 'free -h'
    }else if(cmd == 6){
        command = 'docker logs exorde1 2>&1 | grep REP'
    }else if(cmd == 7){
        command = 'docker stop exorde1 && docker rm exorde1'
    }else if(cmd == 8){
        command = 'cat /proc/sys/vm/swappiness'
    }else if(cmd == 9){
        command = 'sysctl vm.swappiness=10'
    }else if(cmd == 10){
        command = 'docker logs exorde1 2>&1 | grep REP | wc -l'
    }else if(cmd == 11){
        command = 'docker restart exorde1'
    }
    $("#fakeScreen").empty()
    if(multi){
        $("#fakeScreen").append("<p class='line2'>"+ip+" "+command+"</p><p class='line4'>><span class='cursor4'>_</span></p>")
    }else{
        if(ip.includes(";")){
            let data = ip.split(";")
            for(let i = 0; i < data.length; i++){
                $("#fakeScreen").append("<p class='line2'>"+data[i]+" "+command+"</p><p class='line4'>><span class='cursor4'>_</span></p>")
            }
        }else{
            $("#fakeScreen").html("<p class='line2'>"+ip+" "+command+"</p><p class='line4'>><span class='cursor4'>_</span></p>")
        }
        
    }
    
}

function cekTindakanProject(data){
    // $("#loader").css("display","block")
    $("#modalLoading").modal("show")
    $(':button').prop('disabled', true);
    if(data == "list"){
        postData("frmIsianVps","/exorde/api/getList",function(){
            // $("#loader").css("display","none")
            $("#modalLoading").modal("hide")
            // $("#addList").prop('disabled', false);
            $(':button').prop('disabled', false);
            const opts = document.querySelectorAll("#cmbProject option"); 
            const vals = [...opts].map(el => el.text); 
            let dataIp = '';
            $.each(vals, function(key,data){
                let da = data.split("~");
                if(data.includes("~")){
                    if(dataIp == ''){
                        dataIp = da[1]
                    }else{
                        dataIp += ","+da[1]
                    }
                }
            })
            $("#allIp").val(dataIp)
        }, function(){
            // $("#loader").css("display","none")
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        })
    }else if(data == "listIp"){
        if($("#cmbProject").val().includes("-")){
            let project = ["ramVpsDiv","zonaDiv","newVps","jmlVps","diskDiv","delVps", "startVps","stopVps","ipVps","delAllIp"]
            $.each(project, function(key, value){
                $("#"+value).css("display","none")
            })
            $("#allIpVps").empty()
            $("#allIpVps").css("display","none")
            $("#cmbProjectIp").empty()
            postData("frmIsianVps","/exorde/api/getListIp",function(){
                // $("#loader").css("display","none")
                $("#modalLoading").modal("hide")
                $(':button').prop('disabled', false);
                const opts = document.querySelectorAll("#cmbProjectIp option"); 
                const vals = [...opts].map(el => el.text); 
                const vals1 = [...opts].map(el => el.value); 
                let dataIp = '';
                let dataLengkap = ''
                let dataExample = ''
                $.each(vals, function(key,data){
                    let da = data.split("~");
                    if(data.includes("~")){
                        if(dataIp == ''){
                            dataIp = da[1]
                        }else{
                            dataIp += ";"+da[1]
                        }
                    }
                })
                $.each(vals1, function(key,data){
                    if(data.includes(";")){
                        if(dataLengkap == ''){
                            dataLengkap = "Vps ke "+(key-3)+ ": "+data;
                            if((key-3) % 2 !== 0){
                                dataExample = data
                            }
                        }else{
                            dataLengkap += "<br>"+"Vps ke "+(key-3)+ ": "+data;
                            if((key-3) % 2 !== 0){
                                dataExample += "#"+data   
                            }
                        }
                    }
                })
                $("#allIp").val(dataIp)
                if(dataIp != ''){
                    $("#addList").css("display","block")
                    $("#allIpVps").html("Daftar IP di project ini: "+dataIp+"<br><br> Gunakan list di bawah untuk proses multi stop dan start. Hashtag '#' digunakan sebagai pembatas antar IP. Jika hanya akan men-stop atau start 1 vps tidak perlu mengisi list, bisa langsung klik stop.<br><br>"+dataLengkap+"<br><br>Contoh list: "+dataExample);
                    $("#allIpVps").css("display","block")
                }else{
                    $("#addList").css("display","none")
                    $("#allIpVps").html('')
                    $("#allIpVps").css("display","none")
                }
                $("#commandSection").css("display","none")
                // let ip = $("#cmbProjectIp option:selected").text().split("~")
            }, function(){
                // $("#loader").css("display","none")
                $("#modalLoading").modal("hide")
                $(':button').prop('disabled', false);
            })
        }else{
            Swal.fire(
                'Error!',
                'Pilih project dulu',
                'error'
            )
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        }
    }else if(data == "newVps"){
        if($("#cmbProject").val() != 0 && $("#ramVps").val().includes("Pilih") == false && $("#zona").val().includes("Pilih") == false){
            let loop = $("#jml").val().length > 0? $("#jml").val():1
            for(let i = 0; i < loop; i++){
                postData("frmIsianVps","/exorde/api/newVps",function(){
                    // $("#loader").css("display","none")
                    $("#modalLoading").modal("hide")
                    $(':button').prop('disabled', false);
                    if(i == loop-1){
                        cekTindakanProject('listIp')
                    }
                }, function(){
                    // $("#loader").css("display","none")
                    $("#modalLoading").modal("hide")
                    $(':button').prop('disabled', false);
                })
            }
            
        }else{
            Swal.fire(
                'Error!',
                'Ram dan zona tidak boleh kosong',
                'error'
            )
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        }
    }else if(data == "delVps"){
        postData("frmIsianVps","/exorde/api/delVps",function(){
            cekTindakanProject("list")
            // $("#loader").css("display","none")
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        }, function(){
            // $("#loader").css("display","none")
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
        })
    }else if(data == "startVps"){
        if($("#frmIsianVps #ipListMulti").val() != "" && $("#frmIsianVps #ipListMulti").val().includes("#")){
            let data = $("#frmIsianVps #ipListMulti").val().split("#")
                for(let i = 0; i < data.length; i++){
                    $("#frmIsianVps #ipListMulti").val(data[i])
                    postData("frmIsianVps","/exorde/api/startVps",function(){
                        // $("#loader").css("display","none")
                        $("#modalLoading").modal("hide")
                        $(':button').prop('disabled', false);
                    }, function(){
                        // $("#loader").css("display","none")
                        $("#modalLoading").modal("hide")
                        $(':button').prop('disabled', false);
                    })
                }
        }else{
            postData("frmIsianVps","/exorde/api/startVps",function(){
                // $("#loader").css("display","none")
                $("#modalLoading").modal("hide")
                $(':button').prop('disabled', false);
            }, function(){
                // $("#loader").css("display","none")
                $("#modalLoading").modal("hide")
                $(':button').prop('disabled', false);
            })
        }
    }else if(data == "stopVps"){
        if($("#frmIsianVps #ipListMulti").val() != "" && $("#frmIsianVps #ipListMulti").val().includes("#")){
            let data = $("#frmIsianVps #ipListMulti").val().split("#")
                for(let i = 0; i < data.length; i++){
                    $("#frmIsianVps #ipListMulti").val(data[i])
                    postData("frmIsianVps","/exorde/api/stopVps",function(){
                        // $("#loader").css("display","none")
                        $("#modalLoading").modal("hide")
                        $(':button').prop('disabled', false);
                    }, function(){
                        // $("#loader").css("display","none")
                        $("#modalLoading").modal("hide")
                        $(':button').prop('disabled', false);
                    })
                }
        }else{
            postData("frmIsianVps","/exorde/api/stopVps",function(){
                // $("#loader").css("display","none")
                $("#modalLoading").modal("hide")
                $(':button').prop('disabled', false);
            }, function(){
                // $("#loader").css("display","none")
                $("#modalLoading").modal("hide")
                $(':button').prop('disabled', false);
            })
        }
    }else if(data == "runCommand"){
        if($("#frmIsianVps #ipCommand").val() != ""){
            $("#modalLoading").modal("hide")
            // $("#fakeScreen").html("<p class='line2'>"+$("#frmIsianVps #ipCommand").val()+"</p>")
            terminal($("#frmIsianVps #ipCommand").val())
            setTimeout(function(){
                postData("frmIsianVps","/exorde/api/runCommand",function(){
                    // $("#loader").css("display","none")
                    $("#modalLoading").modal("hide")
                    $(':button').prop('disabled', false);
                    $("#item-2").prop("checked", false)
                }, function(){
                    // $("#loader").css("display","none")
                    $("#modalLoading").modal("hide")
                    $(':button').prop('disabled', false);
                    $("#item-2").prop("checked", false)
                })
            }, 2000)
        }else{
            $("#modalLoading").modal("hide")
            $(':button').prop('disabled', false);
            Swal.fire(
                'Error!',
                'Ip tidak boleh kosong',
                'error'
            )
        }
    }
}

function insertDataVpsFunc(){
    // let dataForm = $('#frmIsianVps').serializeArray()
    let formData = new FormData();
        if($('#frmIsianVps #cmbMaster').val() == "addProjectSection"){
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
        }else if($('#frmIsianVps #cmbMaster').val() == "addPemSection"){
            let fileupload = $('#frmIsianVps #addPemSection #fileJson').prop('files')[0];
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
        if($('#frmIsianVps #usernameTg').length > 0){
            formData.append('usernameTg', $('#frmIsianVps #usernameTg').val());
        }
        if($('#frmIsianVps #usernamevps').length > 0){
            formData.append('usernamevps', $('#frmIsianVps #usernamevps').val());
        }
        formData.append('password', $('#frmIsianVps #password').val());
        formData.append('action2', $('#frmIsianVps #cmbMaster').val());
        $.ajax({
            type: "POST",
            url: "/exorde/api/actionvps",
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            enctype: 'multipart/form-data',
            success: function(msg) {
                if(msg.result_code == 0){
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: msg.result_msg,
                        showConfirmButton: false,
                        timer: 1500
                      })
                }else{
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: msg.result_msg,
                        showConfirmButton: false,
                        timer: 1500
                      })
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
