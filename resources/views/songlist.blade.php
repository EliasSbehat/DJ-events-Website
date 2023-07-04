<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
</head>

<body>
    @include('layout.nav')
    
    <div class="text-center pt-4">
        <h4 class="mb-3">Song List</h4>
    </div>
    <div class="container">
        <!-- Modal -->
        <div class="modal fade" id="request" tabindex="-1" aria-labelledby="requestLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="requestLabel">Request Song</h5>
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
                        <div class="mb-4">
                            <label class="form-label" for="singer">Who is singing?</label>
                            <input type="text" id="singer" class="form-control form-control-lg" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="make_dj">Make the DJ</label>
                            <input type="text" id="make_dj" class="form-control form-control-lg" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary submit-btn">Submit</button>
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
                    <th>Request</th>
                </tr>
            </thead>
            <tbody id="song_tbl">
            </tbody>
        </table>
    </div>
    <!-- Start wrapper-->

    <script>
        $(document).on("click", ".request-btn", function(){
            var id = $(this).parent().parent().attr("id");
            $('#request').modal('show');
            $("#song_id").val(id);
            var title = $(this).parent().parent().children()[0].innerText;
            var artist = $(this).parent().parent().children()[1].innerText;
            $("#song_title").val(title);
            $("#song_artist").val(artist);
        });
        $(".submit-btn").click(function(){
            showLoading();
            var title = $("#song_title").val();
            var artist = $("#song_artist").val();
            var singer = $("#singer").val();
            var dj = $("#make_dj").val();
            var id = $("#song_id").val();
            $.get(
                "songmng/request-song", {
                    id: id,
                    title: title,
                    artist: artist,
                    singer: singer,
                    dj: dj
                }, function (res) {
                    hideLoading();
                    $('#request').modal('hide');
                    $("#make_dj").val('');
                    $("#song_id").val('');
                }
            );
        });

        getSongs();

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
                        tr += `<td class="d-flex"><button type="button" class="btn btn-primary request-btn">Request</button></td>`;
                        tr += `</tr>`;
                        tableData += tr;
                    }
                    $("#song_tbl").html(tableData);
                    $('#table_id').DataTable();
                    hideLoading();          
                }, "json"
            );
        }
    </script>
</body>

</html>