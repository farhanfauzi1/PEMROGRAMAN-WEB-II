<html>
<head>
    <title>Contoh Penggunaan UDF</title>
</head>

<body>

<!-- Menentukan Form Input -->
<form method="post">

    Masukkan Bilangan Pertama : <br>
    <input type="text" name="A" size="10">
    <br>

    Masukkan Bilangan Kedua : <br>
    <input type="text" name="B" size="10">
    <br>

    <input type="submit" value="hitung">

</form>

<?php

// Mengecek apakah form sudah dikirim
if (isset($_POST["A"]) && isset($_POST["B"])) {

    $A = $_POST["A"];
    $B = $_POST["B"];

    // Function Penjumlahan
    function jumlah($A, $B)
    {
        $jumlahbil = $A + $B;
        return $jumlahbil;
    }

    // Function Pengurangan
    function kurang($A, $B)
    {
        $kurangbil = $A - $B;
        return $kurangbil;
    }

    // Function Perkalian
    function kali($A, $B)
    {
        $kalibil = $A * $B;
        return $kalibil;
    }

    // Function Pembagian
    function bagi($A, $B)
    {
        $bagibil = $A / $B;
        return $bagibil;
    }

    echo "<br>";

    echo "Bilangan Pertama : ";
    echo $A;
    echo "<br>";

    echo "Bilangan Kedua : ";
    echo $B;
    echo "<br><br>";

    echo "Hasil Penjumlahan 2 buah bilangan";
    echo "<br>";

    $jumlahbil = jumlah($A, $B);

    printf(
        "Penjumlahan antara : %d + %d = %d",
        $A,
        $B,
        $jumlahbil
    );

    echo "<br><br>";

    echo "Hasil Pengurangan 2 buah bilangan";
    echo "<br>";

    $kurangbil = kurang($A, $B);

    printf(
        "Pengurangan antara : %d - %d = %d",
        $A,
        $B,
        $kurangbil
    );

    echo "<br><br>";

    echo "Hasil Perkalian 2 buah bilangan";
    echo "<br>";

    $kalibil = kali($A, $B);

    printf(
        "Perkalian antara : %d * %d = %d",
        $A,
        $B,
        $kalibil
    );

    echo "<br><br>";

    echo "Hasil Pembagian 2 buah bilangan";
    echo "<br>";

    if ($B != 0) {

        $bagibil = bagi($A, $B);

        printf(
            "Pembagian antara : %d / %d = %.2f",
            $A,
            $B,
            $bagibil
        );

    } else {

        echo "Pembagian dengan 0 tidak dapat dilakukan.";

    }

    echo "<br><br>";
}

?>

</body>
</html>
