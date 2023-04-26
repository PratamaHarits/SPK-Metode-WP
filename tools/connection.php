<?php
// Koneksi
$conn = mysqli_connect("localhost", "root", "", "db_wp");
// Cek
if (!$conn) {
    die("Gagal terkoneksi : " . mysqli_connect_error());
}
