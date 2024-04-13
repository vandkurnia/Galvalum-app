<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Galvalum - @yield('title', 'App')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ secure_asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ secure_asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet" />

    <!-- Custom styles for this page -->
    <link href="{{ secure_asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <!-- Icon -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link href="{{ secure_asset('library/select2/dist/css/select2.min.css') }}" rel="stylesheet" />


    @yield('header-custom')
</head>

<body id="page-top">


    <!-- Bootstrap core JavaScript-->
    <script src="{{ secure_asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ secure_asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ secure_asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ secure_asset('assets/js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ secure_asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ secure_asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ secure_asset('assets/js/demo/datatables-demo.js') }}"></script>



    <!-- Page Wrapper -->
    <div id="wrapper">
        <script src="{{ secure_asset('library/select2/dist/js/select2.min.js') }}"></script>


        <!-- Sidebar -->
        @include('template.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">


                @include('template.navbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                @yield('content')

            </div>

            @yield('javascript-custom')
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Website 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>


</body>
