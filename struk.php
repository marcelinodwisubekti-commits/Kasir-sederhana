<?php
session_start();

$products = [
    1 => ['name' => 'Kopi Kapal Api', 'price' => 3000],
    2 => ['name' => 'Indomie Goreng', 'price' => 3500],
    3 => ['name' => 'Teh Botol Sosro', 'price' => 4500],
    4 => ['name' => 'Roti Tawar Sari Roti', 'price' => 15000],
    5 => ['name' => 'Susu Ultra Milk 250ml', 'price' => 7000],
];

if (empty($_SESSION['cart']) || !isset($_POST['bayar'])) {
    header("Location: index.php");
    exit;
}

$bayar = (float)$_POST['bayar'];
$total = 0;
$items = [];

foreach ($_SESSION['cart'] as $id => $qty) {
    if (isset($products[$id])) {
        $subtotal = $products[$id]['price'] * $qty;
        $total += $subtotal;
        $items[] = [
            'name' => $products[$id]['name'],
            'price' => $products[$id]['price'],
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}

$kembalian = $bayar - $total;

// Setelah struk diproses, kosongkan keranjang
$_SESSION['cart'] = [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background-color: #f0f0f0;
            padding: 20px;
        }
        /* Desain ala Kertas Struk Kasir Thermal */
        .receipt {
            background: #fff;
            padding: 20px;
            width: 300px;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            border-top: 5px solid #333;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h3 { margin: 0; }
        .line {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }
        .item-table {
            width: 100%;
            font-size: 13px;
        }
        .item-table td {
            padding: 2px 0;
        }
        .text-right { text-align: right; }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
        }
        .actions {
            margin-top: 15px;
        }
        @media print {
            .actions { display: none; }
            body { background: none; }
            .receipt { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>

<div class="receipt" id="receipt">
    <div class="header">
        <h3>TOKO SERBA ADA</h3>
        <small>Ds.Pejok , Kec.Kepohbaru, Bojonegoro</small><br>
        <small>Telp: 0812-3456-7890</small>
    </div>
    
    <div class="line"></div>
    <small>Tgl: <?= date('d/m/Y H:i:s') ?></small><br>
    <small>Kasir: Admin</small>
    <div class="line"></div>

    <table class="item-table">
        <?php foreach ($items as $item): ?>
            <tr>
                <td colspan="2"><strong><?= htmlspecialchars($item['name']) ?></strong></td>
            </tr>
            <tr>
                <td><?= $item['qty'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <div class="line"></div>

    <table class="item-table">
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="text-right"><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="text-right">Rp <?= number_format($bayar, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right">Rp <?= number_format($kembalian, 0, ',', '.') ?></td>
        </tr>
    </table>

    <div class="line"></div>
    <div class="footer">
        <p>-- Terima Kasih --<br>Selamat Belanja Kembali</p>
    </div>
</div>

<div class="actions">
    <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Cetak Struk (PDF/Printer)</button>
    <a href="index.php" style="margin-left: 10px;">Kembali ke Kasir</a>
</div>

</body>
</html>
