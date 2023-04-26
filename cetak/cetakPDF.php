<?php
// koneksi
include '../tools/connection.php';

// header
include '../blade/header.php';
?>

<div class="row">
    <div class="col-lg-1"></div>
    <div class="col-lg-10">

        <!-- kop surat -->
        <p class="text-center fw-bold m-0">PEMERINTAH KOTA DEPOK</p>
        <p class="text-center fw-bold m-0">SEKRETARIAT DAERAH</p>
        <p class="text-center m-0">Jl. Margonda Raya No. 54 Kota Depok Telepon (0711) 11111</p>
        <p class="text-center m-0">Email : humasdepok@gmail.com</p>
        <hr>

        <!-- isi surat -->
        <p class="text-center fw-bold">Laporan Kenaikan Jabatan Pegawai Negeri Sipil</p>
        <p class="text-justify">Berdasarkan hasil pengolahan data dengan menggunakan beberapa kriteria yang sudah ditentukan dan dengan mengimplementasikan metode Weighted Product (WP), maka menghasilkan tiga rangking teratas sebagai berikut : </p>

        <?php $array_vector_si = array(); ?>
        <?php $ranks = array(); ?>

        <!-- <p class="text-center fw-bold">Tabel Perubahan Kriteria</p>
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

                        <?php
                        // hasil vektor
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
        </table> -->

        <div class="row mt-3">
            <div class="col-1"></div>
            <div class="col-10">
                <!-- <p class="text-center fw-bold">Tabel Ranking</p> -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
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
                            //jika hanya menampilkan 3 nilai teratas
                            if ($ranking > 3) {
                                break;
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
            <div class="col-1"></div>
        </div>

        <p class="text-justify">Demikian surat ini kami sampaikan atas perhatian bapak / ibu / saudara, kami ucapkan terimakasih</p>

        <br><br>

        <p style=" text-align: right;">Depok, <?php echo date("d/m/Y") ?></p><br><br>
        <p style=" text-align: right;">Sekretariat Daerah Kota Depok</p>

    </div>
    <div class="col-lg-1"></div>
</div>

<script>
    window.print();
</script>