<div id="addProjectSection">
    <div class="form-group">
    <label for="exampleInputEmail1" class="form-label">Masukkan nama project</label>
    <input class="form-control" id="project" name="project" placeholder="Enter project">
    </div>
    <div class="form-group">
    <label for="exampleInputEmail1" class="form-label">Masukkan email dari project ini</label>
    <input class="form-control" id="emailproject" name="emailproject" placeholder="Enter email">
    </div>
    <div class="form-group">
    <label for="exampleFormControlFile1" class="form-label">Lampirkan file credential dengan format .json</label>
    <input type="file" class="form-control-file" id="fileJson" accept="application/JSON">
    </div>
    <button type="button" class="btn btn-primary" id="insertDataVps" onclick="insertDataVpsFunc()" style="margin: 20px">Submit</button>
</div>