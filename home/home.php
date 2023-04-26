<?php include '../blade/header.php' ?>

<div class="container">
    <div class="card">
        <div class="card-header bg-info">
            <!-- judul sistem -->
            <?php include '../blade/namaProgram.php'; ?>
        </div>
        <!-- nav -->
        <?php include '../blade/nav.php' ?>
        <!-- body -->
        <div class="card-body">
            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10 shadow py-3">
                    <!-- judul -->
                    <p class="text-center fw-bold">Rekomendasi Kenaikan Jabatan Pegawai Negeri Sipil </p>
                    <hr>
                    <!-- gambar -->
                    <div class="gambar bg-light bg-gradient">
                        <div class="text-center">
                            <img src="../img/pnsImage.JPG" class="rounded" alt="...">
                        </div>
                    </div>
                    <hr>
                    <!-- pengantar -->
                    <p>Metode Weighted Product (WP) menggunakan perkalian untuk menghubungkan rating atribut, dimana rating setiap atribut harus dipangkatkan terlebih dahulu dengan bobot atribut yang bersangkutan. Proses ini sama halnya dengan proses normalisasi. Pembobotan metode Weighted Product dihitung berdasarkan tingkat kepentingan.</p>

                </div>
                <div class="col-lg-1"></div>
            </div>
        </div>
    </div>
</div>

<!-- footer -->
<?php include '../blade/footer.php' ?>