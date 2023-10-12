<div class="form-group" id="ramVpsDiv">
    <label for="inputState" class="form-label">Pilih ram....</label>
    <select id="ramVps" name="ramVps" class="form-select">
        <option>Pilih...</option>
        <option value="e2-standard-2">E2 - CPU 2 Core - Ram 8GB - 50$</option>
        <option value="e2-standard-4">E2 - CPU 4 Core - Ram 16GB - 98$</option>
        <option value="e2-standard-8">E2 - CPU 8 Core - Ram 31B - 196$</option>
        <option value="t2d-standard-2">T2D - CPU 2 Core - Ram 8GB - 62$</option>
        <option value="t2d-standard-4">T2D - CPU 4 Core - Ram 16GB - 124$</option>
        <option value="t2d-standard-8">T2D - CPU 8 Core - Ram 32GB - 240$</option>
        <option value="e2-highmem-2">E2 - CPU 2 Core - Ram 16GB - 67$</option>
        <option value="e2-custom-4-10240">E2 - CPU 4 Core - Ram 10GB - 86$</option>
        <option value="e2-custom-2-10240">E2 - CPU 2 Core - Ram 10GB</option>
        <option value="n2-custom-2-16384">N2 (intel) - CPU 2 Core - Ram 16GB - 81$ (disc)</option>
        <option value="n2d-custom-2-16384">N2D (amd) - CPU 2 Core - Ram 16GB - 70$ (disc)</option>
        <option value="n2d-custom-4-16384">N2D (amd) - CPU 4 Core - Ram 16GB - 104$ (disc)</option>
        <option value="n2d-standard-2">N2D (amd) - CPU 2 Core - Ram 8GB - 50$ (disc)</option>
    </select>
    </div>
    <div class="form-group" id="zonaDiv">
        <label for="inputState" class="form-label">Pilih zona....</label>
        <select id="zona" name="zona" class="form-select">
            <option>Pilih...</option>
            <option value="us-central1-a">us-central1-a - Lowa</option>
            <option value="us-east1-b">us-east1-b - South Carolina</option>
            <option value="us-west1-b">us-west1-b - Oregon</option>
        </select>
    </div>
    <div class="form-group" id="diskDiv" >
        <div class="alert alert-secondary" role="alert">
            Isi disk size tanpa GB. Default disk 125 GB. 10GB = 1$. silahkan kalkulasi sendiri
        </div>
        <label for="exampleInputEmail1" class="form-label">Masukkan Disk Size</label>
        <input class="form-control" id="disk" name="disk" type="number" placeholder="Enter disk. exp: 50">
    </div>
    <div class="form-group" id="jmlVps" >
        <div class="alert alert-secondary" role="alert">
            Isi jumlah vps yg mau dibuat. 1 zone hanya bisa create maksimal 4 vps, total disk semua vps 250gb dan 12 core cpu. silahkan disesuaikan. jika tidak diisi maka hanya akan dibuatkan 1 vps
        </div>
        <label for="exampleInputEmail1" class="form-label">Masukkan jumlah vps</label>
        <input class="form-control" id="jml" name="jml" type="number" placeholder="Enter jumlah. exp: 2">
    </div>

    <button type="button" class="btn btn-primary mb-2" id="newVps" onclick="cekTindakanProject('newVps')">Buat vps</button>