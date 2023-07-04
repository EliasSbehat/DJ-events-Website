<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
</head>

<body>
    @include('layout.nav')
    
    <div class="text-center pt-4">
        <h4 class="mb-3">Song Manage</h4>
    </div>
    <div class="container">
        <a class="btn btn-primary import-btn" role="button">Import</a>
        <a class="btn btn-primary add-btn" role="button" data-mdb-toggle="modal" data-mdb-target="#add_song">Add</a>
        <!-- Modal -->
        <div class="modal fade" id="add_song" tabindex="-1" aria-labelledby="add_songLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="add_songLabel">Add Song</h5>
                        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label" for="song_title">Title</label>
                            <input type="text" id="song_title" class="form-control form-control-lg" />
                            <input type="hidden" id="song_id" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="song_artist">Artist</label>
                            <input type="text" id="song_artist" class="form-control form-control-lg" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary save-btn">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
        <input type="file" class="d-none file_import" accept=".csv" />
        <hr />
        <table id="table_id" class="display mt-4">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Artist</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="song_tbl">
            </tbody>
        </table>
    </div>
    <!-- Start wrapper-->

    <script>
        $(".import-btn").click(function(){
            $(".file_import").click();
        });
        getSongs();
        // Get the file input element
        var fileInput = $(".file_import");

        // Add an event listener for when the user selects a file
        fileInput.on("change", function() {
            var file = $(".file_import")[0].files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
            var contents = e.target.result;

            // Call the appropriate parsing function based on the file type
            if (file.name.endsWith('.csv')) {
                parseCSV(contents);
            } else {
                alert("Unsupported file type");
            }
            };

            reader.readAsText(file);
        });
        function parseCSV(contents) {
            showLoading();
            var rows = contents.split('\n');
            var data = [];

            for (var i = 0; i < rows.length; i++) {
                var cells = rows[i].split(',');
                if (cells.length > 1) {
                    data.push(cells);
                }
            }
            var datas = [];
            for (var j = 0; j < data.length; j++) {
                var title = data[j][1].replaceAll('\"', "");
                var artist = data[j][0].replaceAll('\"', "");
                var obj = {
                    title: title,
                    artist: artist
                }
                datas.push(obj);
            }
            $.post(
                "/songmng/add", {
                    data: JSON.stringify(datas),
                }, function (res) {
                    getSongs();
                }
            );
        }
        $(".save-btn").click(function(){
            var title = $("#song_title").val();
            var artist = $("#song_artist").val();
            var id = $("#song_id").val();
            $.get(
                "songmng/add-song", {
                    id: id,
                    title: title,
                    artist: artist
                }, function (res) {
                    $('#add_song').modal('hide');
                    getSongs();
                }
            );
        });
        $(".add-btn").click(function(){
            format();
        });
        $(document).on("click", ".edit-btn", function(){
            var id = $(this).parent().parent().attr("id");
            $('#add_song').modal('show');
            $("#song_id").val(id);
            $("#add_songLabel").val("Edit Song");
            var title = $(this).parent().parent().children()[0].innerText;
            var artist = $(this).parent().parent().children()[1].innerText;
            $("#song_title").val(title);
            $("#song_artist").val(artist);
        });
        $(document).on("click", ".delete-btn", function(){
            var id = $(this).parent().parent().attr("id");
            $.get(
                "/songmng/delete-song",
                {
                    id, id
                }, function() {
                    getSongs();
                }
            );
        });
        function getSongs() {
            showLoading();
            $.get(
                "/songmng/get", {
                }, function (res) {
                    var tableData = "";
                    for (var i=0;i<res.length;i++) {
                        var tr = "<tr id='"+res[i]['id']+"'>";
                        tr += `<td>${res[i]['title']}</td>`;
                        tr += `<td>${res[i]['artist']}</td>`;
                        tr += `<td class="d-flex"><button type="button" class="btn btn-secondary delete-btn"><i class="fas fa-trash-can"></i></button>
                                    <button type="button" class="btn btn-primary edit-btn"><i class="fas fa-pen-to-square"></i></button></td>`;
                        tr += `</tr>`;
                        tableData += tr;
                    }
                    $("#song_tbl").html(tableData);
                    $('#table_id').DataTable();
                    hideLoading();          
                }, "json"
            );
            format();
        }
        function format() {
            $("#song_id").val("");
            $("#add_songLabel").val("Add Song");
            $("#song_title").val("");
            $("#song_artist").val("");
        }
    </script>
</body>

</html>