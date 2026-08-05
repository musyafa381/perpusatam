<?php

namespace App\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uid',
        'member_type',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'institution',
        'class_level',
        'card_print_status',
        'card_delivery_status',
        'manual_tier',
        'donated_books_count',
        'qr_code'
    ];

    /**
     * Dynamically count how many book items were donated by a member
     */
    public static function getDonatedBooksCount($memberId): int
    {
        if (empty($memberId)) return 0;
        $bookItemModel = new \App\Models\BookItemModel();
        return $bookItemModel->where([
            'donated_by_member_id' => $memberId,
            'deleted_at'           => null
        ])->countAllResults();
    }

    /**
     * Get member tier details based on donated books count or manual tier override
     */
    public static function getTierDetails($donatedCountOrMember = 0): array
    {
        $manualTier = 'none';

        if (is_array($donatedCountOrMember)) {
            $manualTier = $donatedCountOrMember['manual_tier'] ?? 'none';
            $donatedCount = (int) ($donatedCountOrMember['donated_books_count'] ?? 0);
            if (!empty($donatedCountOrMember['id'])) {
                $actualCount = self::getDonatedBooksCount($donatedCountOrMember['id']);
                $donatedCount = max($donatedCount, $actualCount);
            }
        } else {
            $donatedCount = (int) $donatedCountOrMember;
        }


        if (!empty($manualTier) && $manualTier !== 'none') {
            if ($manualTier === 'living_library') {
                return [
                    'name'         => 'Living Library (Paket Kelas)',
                    'code'         => 'living_library',
                    'badge'        => 'bg-primary text-white',
                    'color'        => '#0d6efd',
                    'icon'         => 'ti-building-community',
                    'max_loans'    => 50,
                    'max_days'     => 90,
                    'allow_novel'  => true,
                    'can_book'     => true,
                    'is_priority'  => true,
                    'is_manual'    => true,
                ];
            } elseif ($manualTier === 'platinum') {

                return [
                    'name'         => 'Platinum Member (Manual)',
                    'code'         => 'platinum',
                    'badge'        => 'bg-danger text-white',
                    'color'        => '#dc3545',
                    'icon'         => 'ti-crown',
                    'max_loans'    => 5,
                    'max_days'     => 14,
                    'allow_novel'  => true,
                    'can_book'     => true,
                    'is_priority'  => true,
                    'is_manual'    => true,
                ];
            } elseif ($manualTier === 'gold') {
                return [
                    'name'         => 'Gold Member (Manual)',
                    'code'         => 'gold',
                    'badge'        => 'bg-warning text-dark',
                    'color'        => '#ffc107',
                    'icon'         => 'ti-medal',
                    'max_loans'    => 3,
                    'max_days'     => 10,
                    'allow_novel'  => true,
                    'can_book'     => true,
                    'is_priority'  => false,
                    'is_manual'    => true,
                ];
            } elseif ($manualTier === 'silver') {
                return [
                    'name'         => 'Silver Member (Manual)',
                    'code'         => 'silver',
                    'badge'        => 'bg-secondary text-white',
                    'color'        => '#6c757d',
                    'icon'         => 'ti-award',
                    'max_loans'    => 1,
                    'max_days'     => 7,
                    'allow_novel'  => true,
                    'can_book'     => false,
                    'is_priority'  => false,
                    'is_manual'    => true,
                ];
            }
        }

        if ($donatedCount >= 15) {
            return [
                'name'         => 'Platinum Member',
                'code'         => 'platinum',
                'badge'        => 'bg-danger text-white',
                'color'        => '#dc3545',
                'icon'         => 'ti-crown',
                'max_loans'    => 5,
                'max_days'     => 14,
                'allow_novel'  => true,
                'can_book'     => true,
                'is_priority'  => true,
                'is_manual'    => false,
            ];
        } elseif ($donatedCount >= 7) {
            return [
                'name'         => 'Gold Member',
                'code'         => 'gold',
                'badge'        => 'bg-warning text-dark',
                'color'        => '#ffc107',
                'icon'         => 'ti-medal',
                'max_loans'    => 3,
                'max_days'     => 10,
                'allow_novel'  => true,
                'can_book'     => true,
                'is_priority'  => false,
                'is_manual'    => false,
            ];
        } elseif ($donatedCount >= 3) {
            return [
                'name'         => 'Silver Member',
                'code'         => 'silver',
                'badge'        => 'bg-secondary text-white',
                'color'        => '#6c757d',
                'icon'         => 'ti-award',
                'max_loans'    => 1,
                'max_days'     => 7,
                'allow_novel'  => true,
                'can_book'     => false,
                'is_priority'  => false,
                'is_manual'    => false,
            ];
        }

        return [
            'name'         => 'Reguler (Non-Member)',
            'code'         => 'none',
            'badge'        => 'bg-light text-dark border',
            'color'        => '#6c757d',
            'icon'         => 'ti-user',
            'max_loans'    => 1,
            'max_days'     => 7,
            'allow_novel'  => false,
            'can_book'     => false,
            'is_priority'  => false,
            'is_manual'    => false,
        ];
    }

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
