<div id="actionProjectSection">
    <input type="hidden" id="allIp" name="allIp" value="">
    <button type="button" class="btn btn-info mb-3" id="getList" onclick="cekTindakanProject('list')">Get project list</button>
    <!-- <div class="alert alert-secondary" role="alert">
    Jika ingin menambahkan semua ip, silahkan klik <b>Get All ip list</b> dulu baru klik <b>Add all IP to watchlist</b>
    </div> -->
    <div class="form-group">
    <label for="inputState" class="form-label">Pilih project....</label>
    <select id="cmbProject" name="cmbProject" class="form-select" onchange="cekTindakanProject('listIp')">
        <option>Pilih...</option>
    </select>
    </div>
    <!-- <button type="button" class="btn btn-info mb-3" id="getListIp" onclick="cekTindakanProject('listIp')" disabled>Get all vps in project</button> -->
    <div class="alert alert-secondary" role="alert" id="allIpVps" style="display: none">
        
    </div>
    <button type="button" class="btn btn-dark mb-3" id="addList" onclick="addAllVpsToWatchList()" style="display: none">Add all IP to watchlist</button>
    <div class="form-group" id="ipVpsDiv" style="display: none">
    <label for="inputState" class="form-label">Pilih tindakan....</label>
    <select id="cmbProjectIp" name="cmbProjectIp" class="form-select" onchange="aturButtonAll(this.value)">

    </select>
    </div>
    <div class="alert alert-secondary" role="alert" id="ipVps" style="display: none">
        
    </div>
    <div id="forAppendDetail">

    </div>
    
    
    
    
</div>