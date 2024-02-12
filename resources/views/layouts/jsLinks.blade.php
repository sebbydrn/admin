<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('public/assets/AdminLTE-3.0.0/dist/js/adminlte.min.js')}}"></script>
<!-- Leaflet -->
<script src="{{asset('public/assets/leaflet/leaflet.js')}}"></script>
<script src="{{asset('public/assets/leaflet/leaflet.draw.js')}}"></script>
<!-- Load Esri Leaflet from CDN.  it has no .css stylesheet of its own, only .js -->
<script src="https://unpkg.com/esri-leaflet@2.2.3/dist/esri-leaflet.js" integrity="sha512-YZ6b5bXRVwipfqul5krehD9qlbJzc6KOGXYsDjU9HHXW2gK57xmWl2gU6nAegiErAqFXhygKIsWPKbjLPXVb2g==" crossorigin=""></script>
<!-- Datatables -->
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<!-- Select2 -->
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/select2/js/select2.full.min.js')}}"></script>
<!-- InputMask -->
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/inputmask/min/jquery.inputmask.bundle.min.js')}}"></script>
<!-- ZingChart -->
<script src="{{asset('public/assets/zingchart/zingchart.min.js')}}"></script>
<!-- Sweetalert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- HoldOn -->
<script src="{{asset('public/assets/Holdon/HoldOn.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- EasyAutoComplete -->
<script src="{{asset('public/assets/EasyAutocomplete-1.3.5/jquery.easy-autocomplete.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- Datepicker -->
<script src="{{asset('public/assets/gijgo-combined-1.9.13/js/gijgo.min.js')}}"></script>
<!-- Daterangepicker -->
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- ZingChart -->
<script src="{{asset('public/assets/zingchart/zingchart.min.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('public/assets/AdminLTE-3.0.0/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>

<!-- main.js -->
<script src="{{asset('public/js/main.js')}}"></script>
<!-- map.js -->
<script src="{{asset('public/js/map.js')}}"></script>
<!-- lock.js -->
<script src="{{asset('public/js/lock.js')}}"></script>
<!-- CSRF Token -->
<script type="text/javascript">
    let _token = "<?php echo csrf_token() ?>";
    let base_route = "<?php echo url('/') ?>";
</script>
