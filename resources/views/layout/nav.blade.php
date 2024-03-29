<header>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <!-- Container wrapper -->
        <div class="container-fluid">
            <!-- Toggle button -->
            <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Collapsible wrapper -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Navbar brand -->
                <a class="navbar-brand mt-2 mt-lg-0" href="#">
                    <img
                        src="/assets/imgs/discos (logo).png"
                        height="50"
                        alt="MDB Logo"
                        loading="lazy"
                    />
                </a>
                <!-- Left links -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/songlist">Request Songs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/requested">My Requests</a>
                    </li>
                    @if (session('role')==1)
                    <li class="nav-item song-mng">
                        <a class="nav-link" href="/songmng">Song Manager</a>
                    </li>
                    @endif
                    @if (session('role')==1)
                    <li class="nav-item song-mng">
                        <a class="nav-link" href="/received">Received Requests</a>
                    </li>
                    @endif
                </ul>
                <!-- Left links -->
            </div>
            <!-- Collapsible wrapper -->

            <!-- Right elements -->
            <div class="d-flex align-items-center">
            <!-- Icon -->
                <!-- <a class="text-reset me-3" href="#">
                    <i class="fas fa-shopping-cart"></i>
                </a> -->

                <!-- Avatar -->
                <div class="dropdown">
                    <a
                        class="dropdown-toggle d-flex align-items-center hidden-arrow"
                        href="#"
                        id="navbarDropdownMenuAvatar"
                        role="button"
                        data-mdb-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <img
                            src="/assets/imgs/avatar.png"
                            class="rounded-circle"
                            height="25"
                            alt="Black and White Portrait of a Man"
                            loading="lazy"
                        />
                    </a>
                    <ul
                        class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="navbarDropdownMenuAvatar"
                    >
                        <!-- <li>
                            <a class="dropdown-item" href="#">My profile</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">Settings</a>
                        </li> -->
                        <li>
                            <a class="dropdown-item logout-btn" href="/logout">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Right elements -->
        </div>
        <!-- Container wrapper -->
    </nav>
    <!-- Navbar -->
</header>
<div class="loading">
    <div class='uil-ring-css' style='transform:scale(0.79);'>
        <div></div>
    </div>
</div>
<script>
    $.get(
        "/check", {
            token: sessionStorage.getItem("x-t")
        }, function (res) {
            if (res=='failed') {
                location.href = "/";
            }
        }
    )
    var loadingOverlay = document.querySelector('.loading');
    function showLoading(){
        document.activeElement.blur();
        loadingOverlay.classList.remove('hidden');
    }
    function hideLoading(){
        document.activeElement.blur();
        loadingOverlay.classList.add('hidden');
    }
    hideLoading();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>