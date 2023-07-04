<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
</head>

<body>
    @include('layout.nav')
    
    <div class="text-center pt-4">
        <h4 class="mb-3 requested-text">Today Requested List</h4>
    </div>
    <div class="container">
        <div class="mb-4 w-25">
            <label class="form-label" for="date">Date</label>
            <select id="date" class="form-control form-control-lg">
                <option value='1'>Today</option>
                <option value='0'>Past</option>
            </select>
        </div>
        <hr />
        <table id="table_id" class="display mt-4">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Artist</th>
                </tr>
            </thead>
            <tbody id="song_tbl">
            </tbody>
        </table>
    </div>
    <!-- Start wrapper-->

    <script>
        getSongs(1);
        $("#date").change(function(){
            getSongs($("#date").val()*1);
            if ($("#date").val()*1) {
                $(".requested-text").val('Today Requested List');
            } else {
                $(".requested-text").val('Past Requested List');
            }
        });
        function getSongs(today) {
            showLoading();
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
                    $("#song_tbl").html(tableData);
                    $('#table_id').DataTable();
                    hideLoading();          
                }, "json"
            );
        }
    </script>
</body>

</html>