<!DOCTYPE html>
<html>
<head>
    <title>Pemberitahuan Pemindahan</title>
</head>
<body>
    <h2>Pemberitahuan Pemindahan Posisi</h2>
    
    @if($transferType == "Penetapan")
        <p>Selamat! Status kepegawaian Anda telah diubah menjadi Karyawan Tetap.</p>
    @elseif($transferType == "Resign")
        <p>Status kepegawaian Anda telah diubah menjadi Inactive.</p>
    @else
        <p>Anda telah dipindahkan dari posisi {{ $oldPosition }} di departemen {{ $oldDepartment }}
        menjadi {{ $newPosition }} di departemen {{ $newDepartment }}.</p>
    @endif

    <p>Jika ada pertanyaan, silakan hubungi HR Department.</p>
    
    <br>
    <p>Terima kasih,</p>
    <p>HR Department</p>
</body>
</html>