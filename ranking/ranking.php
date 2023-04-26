<?php
// koneksi
include '../tools/connection.php';

// header
include '../blade/header.php';

?>

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

            <?php $array_vector_si = array(); ?>
            <?php $ranks = array(); ?>

            <div class="row">
                <div class="col-lg-1"></div>
                <div class="col-lg-10 shadow py-3">
                    <!-- judul -->
                    <p class="text-center fw-bold">Hasil Akhir dan Perangkingan</p>
                    <hr>

                    <!-- button trigger cetak PDF -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-1">
                        <button type="button" class="btn btn-outline-primary" onclick="window.open('../cetak/cetakPDF.php', '_blank')">
                            Cetak PDF
                        </button>
                    </div>

                    <!-- tabel perubahaan bobot -->
                    <div class="row mt-3">
                        <div class="col-1"></div>
                        <div class="col-10">
                            <p class="text-center fw-bold">Tabel Perubahan Kriteria</p>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="table-info">
                                        <th>No</th>
                                        <th>Nama Kriteria</th>
                                        <th>Kode Kriteria</th>
                                        <th>Kategori Kriteria</th>
                                        <th>Bobot Awal</th>
                                        <th>Hasil Perbaikan Bobot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query_kriteria = $conn->query("SELECT * FROM ta_kriteria");
                                    $no = 1;
                                    while ($kriteria = $query_kriteria->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $kriteria['kriteria_nama'] ?></td>
                                            <td><?= $kriteria['kriteria_kode'] ?></td>
                                            <td><?= $kriteria['kriteria_kategori'] ?></td>
                                            <td><?= $kriteria['kriteria_bobot'] ?></td>
                                            <?php
                                            $sql_sum = $conn->query("SELECT SUM(kriteria_bobot) as total_kriteria_bobot FROM ta_kriteria");
                                            while ($kriteriaBobot_total = $sql_sum->fetch_assoc()) { ?>
                                                <td><?= $kriteria['kriteria_bobot'] / $kriteriaBobot_total['total_kriteria_bobot'] ?></td>
                                            <?php } ?>
                                        <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-1"></div>
                    </div>

                    <!-- tabel nilai vektor Si  -->
                    <div class="row mt-3">
                        <!-- <div class="col-1"></div> -->
                        <div class="col">
                            <p class="text-center fw-bold">Tabel Nilai Vector Si</p>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="table-info">
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Nama Alternatif</th>
                                        <?php
                                        $query_kriteria = $conn->query("SELECT * FROM ta_kriteria");
                                        $kriteriaRows = mysqli_num_rows($query_kriteria);
                                        ?>
                                        <th colspan="<?= $kriteriaRows; ?>">Nama Kriteria</th>
                                        <th rowspan="2">Nilai Vektor Si</th>

                                    </tr>
                                    <tr class="table-info">
                                        <?php
                                        $query_alternatif = $conn->query("SELECT * FROM ta_kriteria");
                                        while ($kriteria = $query_alternatif->fetch_assoc()) { ?>
                                            <td><?= $kriteria['kriteria_nama']; ?></td>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query_alternatif = $conn->query("SELECT * FROM ta_alternatif ORDER BY alternatif_kode");
                                    $no = 1;
                                    $nilai_vector_si = 0;
                                    while ($alternatif = $query_alternatif->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $alternatif['alternatif_nama'] ?></td>
                                            <?php
                                            $total_nilai_vektor = 1;
                                            ?>

                                            <?php
                                            $alternatifKode = $alternatif['alternatif_kode'];
                                            $query_faktor = $conn->query("SELECT * FROM tb_faktor WHERE alternatif_kode='$alternatifKode' ORDER BY kriteria_kode");
                                            while ($data_faktor = $query_faktor->fetch_assoc()) { ?>
                                                <?php
                                                $kriteriaKode = $data_faktor['kriteria_kode'];
                                                $query_kriteria_faktor = $conn->query("SELECT * FROM ta_kriteria WHERE kriteria_kode='$kriteriaKode' ORDER BY kriteria_kode");
                                                while ($kriteria = $query_kriteria_faktor->fetch_assoc()) {
                                                ?>
                                                    <?php if ($kriteria['kriteria_kategori'] == "benefit") { ?>
                                                        <?php
                                                        $sql = $conn->query("SELECT SUM(kriteria_bobot) as total_kriteria_bobot FROM ta_kriteria");
                                                        while ($kriteriaBobot_total = $sql->fetch_assoc()) { ?>
                                                            <?php $nilai_vektor = $data_faktor['nilai_faktor'] ** ($kriteria['kriteria_bobot'] / $kriteriaBobot_total['total_kriteria_bobot']) ?>

                                                            <td><?= number_format($nilai_vektor, 2); ?></td>

                                                            <?php $total_nilai_vektor = $total_nilai_vektor * $nilai_vektor; ?>

                                                        <?php } ?>

                                                    <?php } elseif ($kriteria['kriteria_kategori'] == "cost") { ?>
                                                        <?php
                                                        $sql = $conn->query("SELECT SUM(kriteria_bobot) as total_kriteria_bobot FROM ta_kriteria");
                                                        while ($kriteriaBobot_total = $sql->fetch_assoc()) { ?>
                                                            <?php $nilai_vektor = $data_faktor['nilai_faktor'] ** (-1 * ($kriteria['kriteria_bobot'] / $kriteriaBobot_total['total_kriteria_bobot'])) ?>

                                                            <td><?= number_format($nilai_vektor, 2); ?></td>

                                                            <?php $total_nilai_vektor = $total_nilai_vektor * $nilai_vektor; ?>

                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>

                                            <td><?= number_format($total_nilai_vektor, 2); ?></td>

                                            <?php

                                            // mencari total nilai vektor
                                            $nilai_vector_si += $total_nilai_vektor;
                                            // masukan total nilai vektor ke array
                                            $vector_si['jumlah_semua_vector'] = $nilai_vector_si;
                                            array_push($array_vector_si, $vector_si);
                                            ?>

                                        </tr>
                                    <?php } ?>
                                    <?php
                                    // ambil nilai array terakhir dan masukan kedalam array
                                    $array_vector_total = array();
                                    array_push($array_vector_total, end($array_vector_si[count($array_vector_si) - 1]));
                                    // hasil total nilai vektor dimasukan kedalam variabel
                                    $jumlah_vektor_total = end($array_vector_total);
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- <div class="col-1"></div> -->
                    </div>

                    <!-- tabel nilai vektor Vi   -->
                    <div class="row mt-3">
                        <div class="col-1"></div>
                        <div class="col-10">
                            <p class="text-center fw-bold">Tabel Nilai Vector Vi</p>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="table-info">
                                        <th>No</th>
                                        <th>Nama Alternatif</th>
                                        <th>Nilai Vektor Si</th>
                                        <th>Total Nilai Vektor Si</th>
                                        <th>Nilai Vektor Vi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query_alternatif = $conn->query("SELECT * FROM ta_alternatif ORDER BY alternatif_kode");
                                    $no = 1;
                                    while ($alternatif = $query_alternatif->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $alternatif['alternatif_nama'] ?></td>
                                            <?php $total_nilai_vektor = 1; ?>

                                            <?php
                                            $alternatifKode = $alternatif['alternatif_kode'];
                                            $query_faktor = $conn->query("SELECT * FROM tb_faktor WHERE alternatif_kode='$alternatifKode' ORDER BY kriteria_kode");
                                            while ($data_faktor = $query_faktor->fetch_assoc()) { ?>
                                                <?php
                                                $kriteriaKode = $data_faktor['kriteria_kode'];
                                                $query_kriteria_faktor = $conn->query("SELECT * FROM ta_kriteria WHERE kriteria_kode='$kriteriaKode' ORDER BY kriteria_kode");
                                                while ($kriteria = $query_kriteria_faktor->fetch_assoc()) {
                                                ?>
                                                    <?php if ($kriteria['kriteria_kategori'] == "benefit") { ?>
                                                        <?php
                                                        $sql = $conn->query("SELECT SUM(kriteria_bobot) as total_kriteria_bobot FROM ta_kriteria");
                                                        while ($kriteriaBobot_total = $sql->fetch_assoc()) { ?>
                                                            <?php $nilai_vektor = $data_faktor['nilai_faktor'] ** ($kriteria['kriteria_bobot'] / $kriteriaBobot_total['total_kriteria_bobot']) ?>

                                                            <?php number_format($nilai_vektor, 2); ?>

                                                            <?php $total_nilai_vektor = $total_nilai_vektor * $nilai_vektor; ?>

                                                        <?php } ?>

                                                    <?php } elseif ($kriteria['kriteria_kategori'] == "cost") { ?>
                                                        <?php
                                                        $sql = $conn->query("SELECT SUM(kriteria_bobot) as total_kriteria_bobot FROM ta_kriteria");
                                                        while ($kriteriaBobot_total = $sql->fetch_assoc()) { ?>
                                                            <?php $nilai_vektor = $data_faktor['nilai_faktor'] ** (-1 * ($kriteria['kriteria_bobot'] / $kriteriaBobot_total['total_kriteria_bobot'])) ?>

                                                            <?php number_format($nilai_vektor, 2); ?>

                                                            <?php $total_nilai_vektor = $total_nilai_vektor * $nilai_vektor; ?>

                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>

                                            <td><?= number_format($total_nilai_vektor, 2); ?></td>
                                            <td><?= number_format(round($jumlah_vektor_total), 2); ?></td>

                                            <!-- hasil vektor -->

                                            <?php
                                            $alternatifKode = $alternatif['alternatif_kode'];
                                            $query_faktor = $conn->query("SELECT * FROM tb_faktor WHERE alternatif_kode='$alternatifKode' ORDER BY kriteria_kode");
                                            while ($data_faktor = $query_faktor->fetch_assoc()) { ?>
                                                <?php
                                                $kriteriaKode = $data_faktor['kriteria_kode'];
                                                $query_kriteria_faktor = $conn->query("SELECT * FROM ta_kriteria WHERE kriteria_kode='$kriteriaKode' ORDER BY kriteria_kode");
                                                while ($kriteria = $query_kriteria_faktor->fetch_assoc()) {
                                                ?>
                                                    <?php if ($kriteria['kriteria_kategori'] == "benefit") { ?>
                                                        <?php
                                                        $sql = $conn->query("SELECT SUM(kriteria_bobot) as total_kriteria_bobot FROM ta_kriteria");
                                                        while ($kriteriaBobot_total = $sql->fetch_assoc()) { ?>
                                                            <?php $nilai_vektor = $data_faktor['nilai_faktor'] ** ($kriteria['kriteria_bobot'] / $kriteriaBobot_total['total_kriteria_bobot']) ?>

                                                            <?php number_format($nilai_vektor, 2); ?>

                                                            <?php
                                                            $total_nilai_vektor_alternatif = $total_nilai_vektor * $nilai_vektor;
                                                            $nilai_wp = $total_nilai_vektor_alternatif / round($jumlah_vektor_total);
                                                            ?>

                                                        <?php } ?>

                                                    <?php } elseif ($kriteria['kriteria_kategori'] == "cost") { ?>
                                                        <?php
                                                        $sql = $conn->query("SELECT SUM(kriteria_bobot) as total_kriteria_bobot FROM ta_kriteria");
                                                        while ($kriteriaBobot_total = $sql->fetch_assoc()) { ?>
                                                            <?php $nilai_vektor = $data_faktor['nilai_faktor'] ** (-1 * ($kriteria['kriteria_bobot'] / $kriteriaBobot_total['total_kriteria_bobot'])) ?>

                                                            <?php number_format($nilai_vektor, 2); ?>

                                                            <?php
                                                            $total_nilai_vektor_alternatif = $total_nilai_vektor * $nilai_vektor;
                                                            $nilai_wp = $total_nilai_vektor_alternatif / round($jumlah_vektor_total);
                                                            ?>

                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                            <td><?= number_format($nilai_wp, 2); ?></td>

                                            <?php
                                            //masukan  nilai hasil-sum, nama-alternatif, kode-alternatif ke dalam variabel $ranks(baris 24)
                                            $rank['nilaiWP'] = $nilai_wp;
                                            $rank['alternatifNama'] = $alternatif['alternatif_nama'];
                                            $rank['alternatifKode'] = $alternatif['alternatif_kode'];
                                            array_push($ranks, $rank);
                                            ?>

                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-1"></div>
                    </div>

                    <!-- tabel ranking -->
                    <div class="row mt-3">
                        <div class="col-1"></div>
                        <div class="col-10">
                            <p class="text-center fw-bold">Tabel Ranking</p>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr class="table-warning">
                                        <th>Ranking</th>
                                        <th>Kode Alternatif</th>
                                        <th>Nama Alternatif</th>
                                        <th>Nilai WP</th>
                                        <th>Keputusan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ranking = 1;
                                    rsort($ranks);
                                    foreach ($ranks as $r) {
                                    ?>
                                        <tr>
                                            <td><?= $ranking++; ?></td>
                                            <td><?= $r['alternatifKode']; ?></td>
                                            <td><?= $r['alternatifNama']; ?></td>
                                            <td><?= number_format($r['nilaiWP'], 2); ?></td>
                                            <td><?= ($ranking <= 4) ? 'Direkomendasikan' : 'Tidak Direkomendasikan'; ?></td>
                                        </tr>
                                    <?php
                                        // //jika hanya menampilkan 3 nilai teratas
                                        // if ($ranking > 3) {
                                        //     break;
                                        // }
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-1"></div>
                    </div>

                </div>
                <div class="col-1"></div>
            </div>
        </div>
    </div>
</div>

<!-- footer -->
<?php include '../blade/footer.php' ?>