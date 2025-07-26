<?php
$title = "Scan QR Kamera";
ob_start();
?>

<h2>🎥 Scan QR dari Kamera</h2>

<div id="reader" style="width: 100%; max-width: 400px;"></div>
<div id="result" style="margin-top: 20px; font-size: 18px;"></div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // Menonaktifkan scanner setelah berhasil scan
        html5QrcodeScanner.clear().then(_ => {
            // Arahkan ke halaman index.php?scan=...
            document.getElementById("result").innerHTML = `📡 Mengarahkan ke absensi...`;
            window.location.href = "index.php?scan=" + encodeURIComponent(decodedText);
        }).catch(error => {
            console.error("Gagal membersihkan scanner.", error);
        });
    }

    function onScanFailure(error) {
        // Tidak perlu apa-apa, ini hanya untuk error kecil seperti tidak ada QR
    }

    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", {
            fps: 10,
            qrbox: 250
        });
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

<style>
    #reader {
        margin: auto;
        padding: 10px;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 8px;
    }
    #result {
        text-align: center;
        margin-top: 20px;
        color: green;
    }
</style>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
