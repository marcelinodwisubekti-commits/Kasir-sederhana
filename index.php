<?php
session_start();

// 1. Daftar Barang (Dapat diganti dengan database jika diperlukan)
$products = [
    1 => ['name' => 'Kopi Kapal Api', 'price' => 3000],
    2 => ['name' => 'Indomie Goreng', 'price' => 3500],
    3 => ['name' => 'Teh Botol Sosro', 'price' => 4500],
    4 => ['name' => 'Roti Tawar Sari Roti', 'price' => 15000],
    5 => ['name' => 'Susu Ultra Milk 250ml', 'price' => 7000],
];

// Inisialisasi Keranjang
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 2. Logika Tambah ke Keranjang
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $qty = (int)$_POST['qty'];

    if ($product_id && $qty > 0) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $qty;
        } else {
            $_SESSION['cart'][$product_id] = $qty;
        }
    }
    header("Location: index.php");
    exit;
}

// 3. Logika Hapus Item dari Keranjang
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$remove_id]);
    header("Location: index.php");
    exit;
}

// 4. Logika Reset Keranjang
if (isset($_GET['reset'])) {
    $_SESSION['cart'] = [];
    header("Location: index.php");
    exit;
}

// Hitung Total Belanja
$total = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    if (isset($products[$id])) {
        $total += $products[$id]['price'] * $qty;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kasir Sederhana</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f6f9; }
        .container { display: flex; gap: 20px; max-width: 1000px; margin: auto; }
        .box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); flex: 1; }
        h2 { margin-top: 0; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-danger { background: #dc3545; }
        .btn-secondary { background: #6c757d; }
        .btn-blue { background: #007bff; }
        input[type="number"] { width: 60px; padding: 5px; }
    </style>
</head>
<body>

<h1 style="text-align: center;">Aplikasi Kasir Sederhana</h1>

<div class="container">
    <!-- Kolom 1: Daftar Barang -->
    <div class="box">
        <h2>Daftar Barang</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $id => $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td>Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td>
                        <form method="POST" style="display: flex; gap: 5px;">
                            <input type="hidden" name="product_id" value="<?= $id ?>">
                            <input type="number" name="qty" value="1" min="1">
                            <button type="submit" name="add_to_cart" class="btn">+ Keranjang</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Kolom 2: Keranjang Belanja & Pembayaran -->
    <div class="box">
        <h2>Keranjang Belanja</h2>
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Keranjang masih kosong.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $id => $qty): ?>
                        <?php if (isset($products[$id])): 
                            $subtotal = $products[$id]['price'] * $qty;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($products[$id]['name']) ?></td>
                            <td><?= $qty ?></td>
                            <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                            <td><a href="index.php?remove=<?= $id ?>" class="btn btn-danger" style="padding: 2px 6px;">X</a></td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h3>Total: Rp <?= number_format($total, 0, ',', '.') ?></h3>

            <!-- Form Proses Bayar -->
            <form action="struk.php" method="POST">
                <div style="margin-bottom: 10px;">
                    <label>Uang Dibayar (Rp):</label><br>
                    <input type="number" name="bayar" min="<?= $total ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                </div>
                <button type="submit" class="btn btn-blue" style="width: 100%; padding: 10px;">Proses & Cetak Struk</button>
            </form>
            <br>
            <a href="index.php?reset=1" class="btn btn-secondary" onclick="return confirm('Kosongkan keranjang?')">Reset Keranjang</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
