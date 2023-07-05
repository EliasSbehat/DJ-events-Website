<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
    <title>My Requests</title>
</head>

<body>
    @include('layout.nav')
    
    <div class="text-center pt-4">
        <h4 class="mb-3 requested-text">My Requests</h4>
    </div>
    <div class="container">
        <div class="mb-4 w-25">
            <label class="form-label" for="date">Date</label>
            <select id="date" class="form-control form-control-lg">
                <option value='1'>Today</option>
                <option value='0'>Past</option>
            </select>
        </div>
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
                            <label class="form-label" for="make_dj">Message The DJ</label>
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
        <hr />
        <table id="today_table_id" class="display mt-4">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Artist</th>
                </tr>
            </thead>
            <tbody id="today_song_tbl">
            </tbody>
        </table>
        <table id="past_table_id" class="display mt-4">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Artist</th>
                    <th class="requestTR">Request</th>
                </tr>
            </thead>
            <tbody id="past_song_tbl">
            </tbody>
        </table>
    </div>
    <!-- Start wrapper-->

    <script>
        getSongs(1);
        $("#past_table_id").hide();
        $("#past_table_id_wrapper").hide();
        $("#date").change(function(){
            getSongs($("#date").val()*1);
            if ($("#date").val()*1) {
                $(".requested-text").val('Today Requested List');
                $("#past_table_id").hide();
                $("#past_table_id_wrapper").hide();
                $("#today_table_id").show();
                $("#today_table_id_wrapper").show();
                
            } else {
                $(".requested-text").val('Past Requested List');
                $("#past_table_id").show();
                $("#past_table_id_wrapper").show();
                $("#today_table_id").hide();
                $("#today_table_id_wrapper").hide();
            }
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
                    if (res=="turnoff") {
                        alert("The song is not available for request at the moment");
                    } else {
                        $('#request').modal('hide');
                        $("#make_dj").val('');
                        $("#song_id").val('');
                        alert("Thank you for your song request");
                    }
                }
            );
        });
        $(document).on("click", ".request-btn", function(){
            var id = $(this).parent().parent().attr("id");
            $('#request').modal('show');
            $("#song_id").val(id);
            var title = $(this).parent().parent().children()[0].innerText;
            var artist = $(this).parent().parent().children()[1].innerText;
            $("#song_title").val(title);
            $("#song_artist").val(artist);
        });
        function getSongs(today) {
            showLoading();
            if (today==1) {
                $.get(
                    "/songmng/getByUser", {
                        today: today
                    }, function (res) {
                        var tableData = "";
                        for (var i=0;i<res.length;i++) {
                            var tr = "<tr id='"+res[i]['id']+"'>";
                            tr += `<td>${res[i]['title']}</td>`;
                            tr += `<td>${res[i]['artist']}</td>`;
                            tr += `</tr>`;
                            tableData += tr;
                        }
                        $("#today_song_tbl").html(tableData);
                        $('#today_table_id').DataTable();
                        hideLoading();          
                    }, "json"
                );
            } else {
                $.get(
                    "/songmng/getByUser", {
                        today: today
                    }, function (res) {
                        var tableData = "";
                        for (var i=0;i<res.length;i++) {
                            var tr = "<tr id='"+res[i]['id']+"'>";
                            tr += `<td>${res[i]['title']}</td>`;
                            tr += `<td>${res[i]['artist']}</td>`;
                            tr += `<td class="d-flex requestTD"><button type="button" class="btn btn-primary request-btn">Request</button></td>`;
                            tr += `</tr>`;
                            tableData += tr;
                        }
                        $("#past_song_tbl").html(tableData);
                        $('#past_table_id').DataTable();
                        hideLoading();          
                    }, "json"
                );
            }
        }
    </script>
</body>

</html>