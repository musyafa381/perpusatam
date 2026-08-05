<?php

namespace App\Models;

use CodeIgniter\Model;

class BookItemModel extends Model
{
    protected $table            = 'book_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'book_id',
        'item_code',
        'condition',
        'condition_note',
        'copy_type',
        'status',
        'acquisition',
        'price',
        'rack_id',
        'donated_by_member_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Generate book items for a book
     */
    public function generateItemsForBook(int $bookId, int $quantity): bool
    {
        if ($quantity <= 0) return false;

        for ($i = 1; $i <= $quantity; $i++) {
            $itemCode = sprintf('BK-%04d-%02d', $bookId, $i);
            
            // Check if item_code already exists, if so increment sequence
            $existing = $this->where('item_code', $itemCode)->first();
            if ($existing) {
                $maxSeq = $this->where('book_id', $bookId)->countAllResults() + 1;
                $itemCode = sprintf('BK-%04d-%02d', $bookId, $maxSeq);
            }

            $this->insert([
                'book_id'   => $bookId,
                'item_code' => $itemCode,
                'condition' => 'baik',
                'status'    => 'tersedia',
            ]);
        }

        return true;
    }

    /**
     * Sync book items quantity when stock is edited
     */
    public function syncBookItems(int $bookId, int $newQuantity): bool
    {
        $currentItems = $this->where('book_id', $bookId)->findAll();
        $currentCount = count($currentItems);

        if ($newQuantity > $currentCount) {
            // Need to add more items
            $additionalCount = $newQuantity - $currentCount;
            for ($i = 1; $i <= $additionalCount; $i++) {
                $seqNumber = $currentCount + $i;
                $itemCode = sprintf('BK-%04d-%02d', $bookId, $seqNumber);
                
                while ($this->where('item_code', $itemCode)->first()) {
                    $seqNumber++;
                    $itemCode = sprintf('BK-%04d-%02d', $bookId, $seqNumber);
                }

                $this->insert([
                    'book_id'   => $bookId,
                    'item_code' => $itemCode,
                    'condition' => 'baik',
                    'status'    => 'tersedia',
                ]);
            }
        } elseif ($newQuantity < $currentCount) {
            // Need to remove excess available items
            $removeCount = $currentCount - $newQuantity;
            $availableItems = $this->where([
                'book_id' => $bookId,
                'status'  => 'tersedia'
            ])->orderBy('id', 'DESC')->findAll();

            foreach ($availableItems as $item) {
                if ($removeCount <= 0) break;
                $this->delete($item['id']);
                $removeCount--;
            }
        }

        return true;
    }

    /**
     * Get available items for a book
     */
    public function getAvailableItems(int $bookId): array
    {
        return $this->where([
            'book_id' => $bookId,
            'status'  => 'tersedia'
        ])->findAll();
    }
}
