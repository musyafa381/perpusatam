<?php if (empty($books)) : ?>
  <div class="p-4 text-center text-danger bg-light rounded-3 border border-danger border-opacity-25">
    <i class="ti ti-alert-circle fs-7 d-block mb-1"></i>
    <b>Buku tidak ditemukan</b>
    <p class="mb-0 text-muted fs-2"><?= $msg ?? 'Silakan coba gunakan kata kunci judul, pengarang, atau ISBN yang lain.'; ?></p>
  </div>
<?php else : ?>
  <?php
  // Flatten books into individual available eksemplar rows
  $eksemplarList = [];
  foreach ($books as $book) {
      if ($book['deleted_at']) continue;

      $items = $book['available_items'] ?? [];

      if (!empty($items)) {
          foreach ($items as $idx => $item) {
              $eksemplarList[] = [
                  'book'         => $book,
                  'item'         => $item,
                  'eksemplar_no' => $idx + 1,
                  'is_matched'   => (!empty($matchedItemCode) && $matchedItemCode === $item['item_code'])
              ];
          }
      } else {
          // General book fallback without items
          $eksemplarList[] = [
              'book'         => $book,
              'item'         => null,
              'eksemplar_no' => 1,
              'is_matched'   => false
          ];
      }
  }
  ?>

  <div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-bold text-dark mb-0">
      <i class="ti ti-books text-primary me-2"></i> Hasil Pencarian (<?= count($eksemplarList); ?> Eksemplar Fisik Tersedia)
    </h5>
    <small class="text-muted"><i class="ti ti-info-circle me-1"></i>Klik "Pilih Buku" pada eksemplar yang ingin dipinjam</small>
  </div>

  <div class="table-responsive rounded-3 border">
    <table class="table table-hover align-middle table-custom mb-0">
      <thead>
        <tr>
          <th scope="col" class="ps-3">#</th>
          <th scope="col">Sampul</th>
          <th scope="col">Judul & Pengarang</th>
          <th scope="col">Kode Eksemplar Fisik</th>
          <th scope="col">Rak & Kategori</th>
          <th scope="col" class="text-center pe-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php foreach ($eksemplarList as $row) : ?>
          <?php
          $book = $row['book'];
          $item = $row['item'];
          $itemCode = $item['item_code'] ?? '';
          $isMatched = $row['is_matched'];
          ?>
          <tr class="<?= $isMatched ? 'table-warning' : ''; ?>">
            <th scope="row" class="ps-3 text-muted"><?= $i++; ?></th>
            <td>
              <?php
              $coverImageUrl = getBookCover($book['book_cover'] ?? '');
              ?>
              <img class="rounded-2 shadow-sm border" src="<?= $coverImageUrl; ?>" alt="<?= esc($book['title']); ?>" style="width: 48px; height: 68px; object-fit: cover;">
            </td>
            <td>
              <div class="fw-bold text-dark fs-3 mb-0"><?= esc("{$book['title']} ({$book['year']})"); ?></div>
              <small class="text-muted"><i class="ti ti-user me-1"></i>Pengarang: <?= esc($book['author']); ?></small>
              <?php if (stripos($book['category'] ?? '', 'novel') !== false) : ?>
                <div class="mt-1">
                  <span class="badge bg-warning-subtle text-dark border border-warning fs-1"><i class="ti ti-lock me-1"></i> Syarat Member (Silver+)</span>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <?php if (!empty($itemCode)): ?>
                <div class="fw-bold text-dark fs-3">
                  <i class="ti ti-barcode text-primary me-1"></i><?= esc($itemCode); ?>
                </div>
                <small class="text-muted">Eksemplar Ke-<?= $row['eksemplar_no']; ?> <?= !empty($item['condition']) ? '('.esc($item['condition']).')' : ''; ?></small>
                <?php if ($isMatched): ?>
                  <span class="badge bg-warning text-dark ms-1 fs-1">Cocok Barcode</span>
                <?php endif; ?>
              <?php else: ?>
                <span class="badge bg-light text-muted border fw-normal fs-2">Umum / Tanpa Barcode Kode</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge badge-subtle-secondary fs-2 d-block mb-1"><i class="ti ti-category me-1"></i><?= esc($book['category']); ?></span>
              <span class="badge bg-light text-dark border fs-2"><i class="ti ti-box me-1"></i><?= esc($book['rack']); ?></span>
            </td>
            <td class="text-center pe-3">

              <?php if (intval($book['stock'] ?? 0) > 0) :
                $rndm = md5($book['id'] . $itemCode . rand(0, 10000));
                $uniqueKey = $book['slug'] . ($itemCode ? '_' . $itemCode : '');
              ?>
                <script>
                  function handleSelectEksemplar<?= $rndm; ?>() {
                    const bookObj = {
                      key: "<?= $uniqueKey; ?>",
                      slug: "<?= $book['slug']; ?>",
                      title: "<?= esc("{$book['title']} ({$book['year']})"); ?>",
                      cover: "<?= esc($book['book_cover'] ?? ''); ?>",
                      coverUrl: "<?= $coverImageUrl; ?>",
                      stock: "<?= $book['stock']; ?>",
                      category: "<?= esc($book['category'] ?? ''); ?>",
                      item_code: "<?= esc($itemCode); ?>"
                    };

                    selectBook(bookObj);
                  }
                </script>
                <button type="button" class="btn btn-primary btn-sm px-3 rounded-3 fw-bold d-inline-flex align-items-center gap-1" onclick="handleSelectEksemplar<?= $rndm; ?>()">
                  <i class="ti ti-plus fs-4"></i> Pilih Buku
                </button>
              <?php else : ?>
                <button class="btn btn-secondary btn-sm px-3 rounded-3" disabled>
                  Stok Habis
                </button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>