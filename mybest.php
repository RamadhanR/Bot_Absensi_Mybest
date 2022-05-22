<?php
error_reporting(0);
date_default_timezone_set('asia/jakarta');
/* --------[ COLOR ]-------- */
$bred   = "\033[1;31m";         # Red
$black  = "\033[1;30m";         # Black
$red    = "\033[1;31m";         # Red
$green  = "\033[1;32m";         # Green
$yellow = "\033[1;33m";         # Yellow
$blue   = "\033[1;34m";         # Blue
$purple = "\033[1;35m";         # Purple
$cyan   = "\033[1;36m";         # Cyan
$white  = "\033[1;37m";         # White
$hgreen = "\033[0;92m";         # High Green
$normal = "\033[0m";            # Non Color
/* --------[ COLOR ]-------- */

$nim = $smtpne = preg_split(
    '/\n|\r\n?/',
    trim(file_get_contents("nim.txt"))
);
$count = count($nim);
for ($i = 0; $i < $count; $i++) {
    $ch = curl_init("http://elearning.bsi.ac.id/login");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    // curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
    // curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


    $response = curl_exec($ch);

    $_GetToken1 = explode('<meta name="csrf-token" content="', $response);
    $_GetToken2 = explode('">', $_GetToken1[1]);

    # code...
    $datamybest = explode('|', $nim[$i]);
    $data = array(
        '_token'        => $_GetToken2[0],
        'username'      => $datamybest[0],
        'password'      => $datamybest[1],
        'verifikasi'    => 1,
    );

    curl_setopt($ch, CURLOPT_URL, "http://elearning.bsi.ac.id/login");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $response = curl_exec($ch);
    $carinama = explode('id="eMail" placeholder="', $response);
    $nama = explode('" readonly', $carinama[1]);
    if (empty($nama[0])) {
        $i = $i - 1;
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        echo $yellow . "Automatic Fixing\n" . $normal;
    } else {
        echo $yellow . "[ Nama ]==> " . $nama[0] . "$normal \n";
        // file_put_contents('mybest.html', $response);
        $sch = array(
            'Host: elearning.bsi.ac.id',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate',
            'Connection: keep-alive',
            'Referer: http://elearning.bsi.ac.id/user/dashboard',
            'Cookie: ',
        );

        curl_setopt($ch, CURLOPT_URL, "http://elearning.bsi.ac.id/sch");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0");
        // curl_setopt($ch, CURLOPT_COOKIE, "cookie.txt");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $sch);
        $sch = curl_exec($ch);
        // file_put_contents('mybest.html', $sch);

        $thisjadwal = preg_split('<<div class="col-lg-4 col-md-4 col-sm-12">>', $sch);
        for ($colek = 1; $colek < count($thisjadwal); $colek++) {
            # code...
            $namamatkul1 = explode('<h6 class="pricing-title">', $thisjadwal[$colek]);
            $makul22 = explode('</h6>', $namamatkul1[1]);
            $makul22[0] = str_ireplace('&amp;', '&', $makul22[0]);
            $kd_cek = explode('<a href="http://elearning.bsi.ac.id/absen-mhs/', $thisjadwal[$colek]);
            $kd_mat = explode('" class="btn btn-primary btn-lg">Masuk Kelas</a>', $kd_cek[1]);

            curl_setopt($ch, CURLOPT_URL, "http://elearning.bsi.ac.id/absen-mhs/" . $kd_mat[0]);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0");
            // curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
            $makul = curl_exec($ch);
            // file_put_contents('mybest.html', $makul);
            if (preg_match('/Belum Mulai/', $makul)) {
                echo $red . "[ $colek ]==> " . $makul22[0] . " ==> Makul Belum Dimulai\n$normal";
            } elseif (preg_match('/Pengajaran Sesuai/', $sch)) {
                echo $green . "[ $colek ]==> " . $makul22[0] . " ==> Makul Ini Sudah Absen\n$normal";
            } elseif (preg_match('/Absen Masuk/', $makul)) {
                $caritoken = explode('<input type="hidden" name="_token" value="', $makul);
                $token = explode('">', $caritoken[1]);
                // echo $token[0] . "\n";

                $caripertemuan = explode('<input type="hidden" name="pertemuan" value="', $makul);
                $pertemuan = explode('">', $caripertemuan[1]);
                // echo $pertemuan[0] . "\n";

                $cariid = explode('<input type="hidden" name="id" value="', $makul);
                $id = explode('">', $cariid[1]);
                // print_r($id[0] . "\n");

                $postfield = "_token=" . $token[0] . "&pertemuan=" . $pertemuan[0] . "&id=" . $id[0] . "";

                curl_setopt($ch, CURLOPT_URL, "http://elearning.bsi.ac.id/mhs-absen");
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0");
                // curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_ENCODING, "gzip");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield);
                $absen = curl_exec($ch);
                if (preg_match('/Pengajaran Sesuai/', $absen)) {
                    echo $green . "[ $colek ]==> " . $makul22[0] . " ==> Absen Sukses\n$normal";
                } else {
                    echo $red . "[ $colek ]==> " . $makul22[0] . " ==> Absen Gagal\n$normal";
                }
            } else {
                echo $green . "[ $colek ]==> " . $makul22[0] . " ==> Sudah Absen\n$normal";
            }
            $gb_link = "http://elearning.bsi.ac.id/absen-mhs/" . $kd_mat[0];
            $gb_link = str_ireplace('absen-mhs', 'rekap-side', $gb_link);
            curl_setopt($ch, CURLOPT_URL, $gb_link);
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:100.0) Gecko/20100101 Firefox/100.0");
            curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
            $json = curl_exec($ch);
            $inidata = json_decode($json);
            $get = count($inidata->data);
            for ($a = 0; $a < $get; $a++) {
                # code...
                $absencek = $inidata->data[$a]->status_hadir;
                $absencek = str_ireplace('<a href="javascript:void(0)" class="btn btn-primary">', '', $absencek);
                $absencek = str_ireplace('<a href="javascript:void(0)" class="btn btn-danger">', '', $absencek);
                $absencek = str_ireplace('</a>', '', $absencek);
                $at = $a + 1;
                if (preg_match('/Tidak Hadir/', $absencek)) {
                    print_r($yellow . "[ Pertemuan$red $at $normal$yellow]==> " . $red . $absencek . "\n" . $normal);
                } elseif (preg_match('/Hadir/', $absencek)) {
                    print_r($yellow . "[ Pertemuan$red $at $normal$yellow]==> " .  $green . $absencek . "\n" . $normal);
                }
            }
            echo $cyan . "+-----------------[ Cek Absensi Keseluruhan ]+-----------------\n" . $normal;
        }
        // cek kehadiran
        exit;
    }
}
