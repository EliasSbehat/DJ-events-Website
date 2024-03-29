<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.head')
    <title>Received Requests</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.3.0/ZeroClipboard.min.js"></script>
</head>

<body>
    @include('layout.nav')
    
    <div class="text-center pt-4">
        <h4 class="mb-3">Received Requests</h4>
    </div>
    <div class="container">
        <table id="table_id" class="display mt-4">
            <thead>
                <tr>
                    <th>Date / Time</th>
                    <th>Registered Name : Who Is Singing</th>
                    <th>Song Artist / Title</th>
                    <th>Message the DJ</th>
                    <th>Read</th>
                </tr>
            </thead>
            <tbody id="song_tbl">
            </tbody>
        </table>
    </div>
    <!-- Start wrapper-->

    <script>
        getSongs();
        function getSongs() {
            // showLoading();
            var table = $('#table_id').DataTable({
                // "processing": true,
                "serverSide": true,
                "ajax": {
                    "type": "GET",
                    "url": "/songmng/getRequestedSongs",
                    "dataType": "json",
                    "contentType": 'application/json; charset=utf-8',
                },
                order: [[0, 'desc']],
                "lengthMenu": [
                    15, 50, 100
                ],
                "columns": [
                    { data: 'date', name: 'date' },
                    { data: 'first_column', name: 'first_column' },
                    { data: 'second_column', name: 'second_column' },
                    { data: 'dj', name: 'dj' },
                    { data: 'action', name: 'action' },
                ]
            });
            setInterval(function(){
                table.ajax.reload(null, false);
            }, 5000);
        }
        function copyToClipboard(text) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();
        }
        $(document).on("click", "#singer_column", function(){
            // $(this).attr('title', 'Copied!').tooltip('_fixTitle').tooltip('show');
            copyToClipboard($(this)[0].innerText);
        });
        $(document).on("click", "#artist_column", function(){
            // $(this).attr('title', 'Copied!').tooltip('_fixTitle').tooltip('show');
            copyToClipboard($(this)[0].innerText);
        });
        $(document).on("click", ".read_check", function(){
            console.log($(this)[0].checked);
            var id = $(this).attr("id");
            var checked = $(this)[0].checked;
            $.get(
                "/songmng/set-read",
                {
                    id,
                    checked
                }, function() {
                    
                }
            );
            console.log(id);
        });
    </script>
</body>

</html>