<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SettingsController extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        helper(['form', 'url', 'library']);
    }

    public function index()
    {
        $settings = $this->settingModel->getAllLibrarySettings();

        // Convert open days array for view checkboxes
        $femaleDays = array_map('intval', explode(',', $settings['female_open_days'] ?? '6,0,1'));
        $maleDays = array_map('intval', explode(',', $settings['male_open_days'] ?? '2,3,4'));
        $generalClosedDays = array_map('intval', explode(',', $settings['general_closed_days'] ?? '5'));
        $applyGenderTypes = array_map('trim', explode(',', strtolower($settings['apply_gender_schedule_to'] ?? 'santri,siswa')));

        $availableMemberTypes = [
            'santri'  => 'Siswa / Santri',
            'petugas' => 'Petugas / Staf',
        ];

        return view('admin/settings/index', [
            'title'                => 'Pengaturan Perpustakaan',
            'settings'             => $settings,
            'femaleDays'           => $femaleDays,
            'maleDays'             => $maleDays,
            'generalClosedDays'    => $generalClosedDays,
            'applyGenderTypes'     => $applyGenderTypes,
            'availableMemberTypes' => $availableMemberTypes,
        ]);
    }

    public function update()
    {
        $post = $this->request->getPost();

        // Process checkboxes array to comma separated strings
        $femaleDaysArr = $this->request->getPost('female_open_days') ?? [];
        $maleDaysArr = $this->request->getPost('male_open_days') ?? [];
        $generalClosedArr = $this->request->getPost('general_closed_days') ?? [];
        $applyTypesArr = $this->request->getPost('apply_gender_types') ?? [];

        $postData = [
            'female_open_days'         => implode(',', $femaleDaysArr),
            'male_open_days'           => implode(',', $maleDaysArr),
            'general_closed_days'      => implode(',', $generalClosedArr),
            'apply_gender_schedule_to' => implode(',', array_map('strtolower', $applyTypesArr)),
            'max_books_per_member'     => max(1, (int)($post['max_books_per_member'] ?? 2)),
            'default_loan_duration'    => max(1, (int)($post['default_loan_duration'] ?? 7)),
            'max_loan_extensions'      => max(0, (int)($post['max_loan_extensions'] ?? 1)),
            'fine_per_day'             => max(0, (int)($post['fine_per_day'] ?? 1000)),
            'max_fine_amount'          => max(0, (int)($post['max_fine_amount'] ?? 20000)),
            'grace_period_days'        => max(0, (int)($post['grace_period_days'] ?? 0)),
            'damaged_book_fine'        => max(0, (int)($post['damaged_book_fine'] ?? 5000)),
            'special_holidays'         => trim((string)($post['special_holidays'] ?? '')),
            'library_name'             => trim((string)($post['library_name'] ?? 'Perpustakaan Assalafiyyah')),
            'library_address'          => trim((string)($post['library_address'] ?? '')),
            'library_contact'          => trim((string)($post['library_contact'] ?? '')),
            'struk_footer_note'        => trim((string)($post['struk_footer_note'] ?? '')),
        ];

        foreach ($postData as $key => $val) {
            $this->settingModel->setSetting($key, $val);
        }

        // Sync fine_per_day to legacy FinesPerDayModel
        \App\Models\FinesPerDayModel::updateAmount($postData['fine_per_day']);

        return redirect()->to(base_url('admin/settings'))->with('success', 'Pengaturan perpustakaan berhasil diperbarui!');
    }
}
