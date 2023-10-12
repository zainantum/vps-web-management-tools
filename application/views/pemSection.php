<div id="addPemSection">
    <div class="alert alert-secondary" role="alert">
    File .pem diperoleh dari putty gen. File ini nantinya akan digunakan untuk mengakses vps lewat website. Pastikan semua username vps kalian sama, karena nanti file .pem akan disimpan dengan username vps kalian    
    </div>
    <div class="form-group">
    <label for="exampleInputEmail1" class="form-label">Masukkan username vps</label>
    <input class="form-control" id="usernamevps" name="usernamevps" placeholder="Enter username vps">
    </div>
    <div class="form-group">
    <label for="exampleFormControlFile1" class="form-label">Lampirkan file .pem dari puttygen</label>
    <input type="file" class="form-control-file" id="fileJson" accept=".pem">
    </div>
    <button type="button" class="btn btn-primary" id="insertDataVps" onclick="insertDataVpsFunc()" style="margin: 20px">Submit</button>
</div>