    </main>
    <?php $uri = $this->uri->segment(1); ?>
    <nav class="bottom-nav">
        <div class="bottom-nav-inner">
            <a href="<?= base_url('dashboard') ?>" class="<?= $uri == 'dashboard' ? 'active' : '' ?>">
                <i class="ri-home-5-line"></i>Dashboard
            </a>
            <a href="<?= base_url('sesi') ?>" class="<?= $uri == 'sesi' ? 'active' : '' ?>">
                <i class="ri-file-list-3-line"></i>Sesi
            </a>
            <a href="<?= base_url('riwayat') ?>" class="<?= $uri == 'riwayat' ? 'active' : '' ?>">
                <i class="ri-history-line"></i>Riwayat
            </a>
            <a href="<?= base_url('profil') ?>" class="<?= $uri == 'profil' ? 'active' : '' ?>">
                <i class="ri-user-3-line"></i>Profil
            </a>
        </div>
    </nav>
    <script src="<?= base_url(); ?>assets/js/vendor.min.js"></script>

<!-- App js -->
<script src="<?= base_url(); ?>assets/js/app.js"></script>
<script src="<?= base_url(); ?>assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="<?= base_url(); ?>assets/js/pagination.js"></script>
<script src="<?= base_url(); ?>assets/js/lightbox.js"></script>
<script src="<?= base_url(); ?>assets/js/helper.js"></script>
<script src="<?= base_url(); ?>assets/js/js-form.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jodit@3.1.3/build/jodit.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.2/package/dist/xlsx.full.min.js"></script>
<script src="<?= base_url(); ?>assets/js/jquery.autocomplete.js"></script>
</body>
</html>
