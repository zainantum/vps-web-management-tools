<div class="form-group">
        <div class="form-group" id="ipListMultiA" >
            <label for="exampleInputEmail1" class="form-label">Masukkan list jika ingin menggunakan multi stop dan start vps</label>
            <input class="form-control" id="ipListMulti" name="ipListMulti" placeholder="Enter list, example: nifty-depth-394204;zones/us-central1-a;imron6540#nifty-depth-394204;zones/us-central1-a;imron7556">
        </div>

        <button type="button" class="btn btn-success mb-2" id="startVps"  onclick="cekTindakanProject('startVps')">Start vps</button>
        <button type="button" class="btn btn-secondary mb-2" id="stopVps"  onclick="cekTindakanProject('stopVps')">Stop vps</button>
        <button type="button" class="btn btn-danger mb-2" id="delVps"  onclick="cekTindakanProject('delVps')">Hapus vps</button>
    </div>