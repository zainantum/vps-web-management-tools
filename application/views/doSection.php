<div id="vpsdo">
    <input type="hidden" id="allIp" name="allIp" value="">
    <div class="alert alert-secondary" role="alert">
    Lengkapi detail dibawah jika ingin menjalankan command lewat web ini. misal ingin menjalankan command temporary.sh
    </div>
    <div class="alert alert-secondary" role="alert">
    Username dan password vps saat membuat vps digital ocean.
    </div>
    <div class="form-group">
        <label for="inputState" class="form-label">Pilih COMMAND....</label>
        <select id="commandId" name="commandId" class="form-select" onchange="aturCommand(this.value)">
        <option>Pilih...</option>
        <option value="1">Temporary sh</option>
        <option value="2">Log docker</option>
        <option value="3">Sisa disk</option>
        <option value="4">Cek list container</option>
        <option value="5">Sisa ram</option>
        <!-- <option value="6">Cek "REP" di container</option> -->
        <option value="7">Hapus container</option>
        <option value="8">Cek swappiness</option>
        <option value="9">Set swappiness 5</option>
        <!-- <option value="10">Total kata "REPUTATION" di log</option> -->
        <option value="11">Restart worker</option>
        <option value="12">Hapus image, system dan volume docker (pake ini jika disk habis)</option>
        <option value="13">Update Script Checkdisk (jika disk 100%, auto remove image dan rerun)</option>
        <option value="14">Check worker version</option>
        <option value="15">Update watchtower</option>
        <option value="16">Cek twitter limit</option>
        <option value="17">Cek total rep yg didapat selama worker berjalan</option>
        <option value="99">Custom Command</option>
        </select>
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1" class="form-label">Masukkan username vps</label>
        <input class="form-control" id="unvps" name="unvps" placeholder="Enter username vps">
    </div>
    <div class="form-group">
        <label for="exampleInputEmail1" class="form-label">Masukkan password vps</label>
        <input class="form-control" id="pwvps" name="pwvps" type="password" placeholder="Enter password vps">
    </div>
    <div id="customSection" style="display: none">
        <div class="form-group">
            <label for="exampleInputEmail1" class="form-label">Enter custom command</label>
            <input class="form-control" id="customcommand" name="customcommand" placeholder="Enter custom command">
        </div>
    </div>
    <div id="detailInfoSection" style="display: none">
        <div class="form-group">
            <label for="exampleInputEmail1" class="form-label">Enter address for workers:</label>
            <input class="form-control" id="addressvps" name="addressvps" placeholder="Ent
            er your address">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1" class="form-label">Enter maximum worker do you want: </label>
            <input class="form-control" id="workervps" name="workervps" placeholder="Enter maximum worker do you want">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1" class="form-label">Install docker? y or n:</label>
            <input class="form-control" id="dvps" name="dvps" placeholder="Install docker? y or n:">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1" class="form-label">Create swap? y or n:</label>
            <input class="form-control" id="csvps" name="csvps" placeholder="Create swap? y or n:">
        </div>
        <div class="form-group">
        <div class="alert alert-secondary" role="alert">
            Jawaban untuk Use Twitter:<br>
            Jawab c -> worker akan dilimit max 2 cpu dan 8gb ram<br>
            Jawab d -> worker akan dilimit max 4 cpu dan 16gb ram<br>
            Jawab e -> worker akan dilimit max 2 cpu dan 16gb ram
        </div>
            <label for="exampleInputEmail1" class="form-label">Use Twitter? c (2 core) or d (4 core):</label>
            <input class="form-control" id="tvps" name="tvps" placeholder="Use Twitter? c (2 core) or d (4 core):">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1" class="form-label">Enter swap do you want with G(exp: 4G):</label>
            <input class="form-control" id="svps" name="svps" placeholder="Enter swap do you want with G(exp: 4G): isi 0 jika sudah melakukan swap:">
        </div>
    </div>
    <div class="form-group">
    <input type="checkbox" id="item-2" value="1" onclick="ambilIpDb()"> Semua IP <br />
        <label for="exampleInputEmail1" class="form-label">Daftar IP</label>
        <input class="form-control" id="ipCommand" name="ipCommand" placeholder="Enter IP. exp: 1 vps = 34.83.148.228 or multi vps = 34.83.148.227;34.83.148.228;34.83.148.229">
    </div>
    <button type="button" class="btn btn-success mb-2" id="runCommandVps" onclick="cekTindakanProject('runCommand')">Run Command</button>
    <div class=fakeMenu>
        <div class="fakeButtons fakeClose"></div>
        <div class="fakeButtons fakeMinimize"></div>
        <div class="fakeButtons fakeZoom"></div>
    </div>
    <div class="fakeScreen" id="fakeScreen">
    </div>
</div>