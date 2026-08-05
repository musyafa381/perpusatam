<?php

use App\Models\SettingModel;

if (!function_exists('get_library_setting')) {
    function get_library_setting(string $key, $default = null)
    {
        $settingModel = new SettingModel();
        return $settingModel->getSetting($key, $default);
    }
}

if (!function_exists('calculate_loan_fine')) {
    /**
     * Calculate loan fine based on loan due date, return date, member gender & type, and settings
     */
    function calculate_loan_fine(array $loan, ?string $returnDate = null): array
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->getAllLibrarySettings();

        $finePerDay = (int)($settings['fine_per_day'] ?? 1000);
        $maxFine = (int)($settings['max_fine_amount'] ?? 20000);
        $graceDays = (int)($settings['grace_period_days'] ?? 0);

        $femaleDays = array_map('intval', explode(',', $settings['female_open_days'] ?? '6,0,1'));
        $maleDays = array_map('intval', explode(',', $settings['male_open_days'] ?? '2,3,4'));
        $generalClosedDays = array_map('intval', explode(',', $settings['general_closed_days'] ?? '5'));
        $applyTypes = array_map('trim', explode(',', strtolower($settings['apply_gender_schedule_to'] ?? 'santri,siswa')));

        $specialHolidays = [];
        if (!empty($settings['special_holidays'])) {
            $specialHolidays = array_map('trim', explode(',', $settings['special_holidays']));
        }

        $dueDate = new DateTime($loan['due_date']);
        $actualReturnDate = $returnDate ? new DateTime($returnDate) : new DateTime(date('Y-m-d'));

        if ($actualReturnDate <= $dueDate) {
            return [
                'late_days'   => 0,
                'charge_days' => 0,
                'fine_amount' => 0,
            ];
        }

        // Determine if member is subject to gender schedule
        $memberType = strtolower($loan['member_type'] ?? $loan['type'] ?? 'santri');
        $isGenderRestricted = false;
        foreach ($applyTypes as $type) {
            if ($type !== '' && str_contains($memberType, $type)) {
                $isGenderRestricted = true;
                break;
            }
        }

        $gender = strtoupper($loan['gender'] ?? 'L'); // 'L' or 'P'

        $lateDays = 0;
        $chargeDays = 0;

        $current = clone $dueDate;
        $current->modify('+1 day'); // Start counting late from the day after due date

        while ($current <= $actualReturnDate) {
            $dateStr = $current->format('Y-m-d');
            $dayOfWeek = (int)$current->format('w'); // 0 (Sun) to 6 (Sat)

            $isClosed = false;

            // Check special holidays
            if (in_array($dateStr, $specialHolidays)) {
                $isClosed = true;
            }
            // Check general closed days (e.g. Friday)
            elseif (in_array($dayOfWeek, $generalClosedDays)) {
                $isClosed = true;
            }
            // Check gender schedule if member is restricted
            elseif ($isGenderRestricted) {
                if ($gender === 'P' && !in_array($dayOfWeek, $femaleDays)) {
                    $isClosed = true;
                } elseif ($gender === 'L' && !in_array($dayOfWeek, $maleDays)) {
                    $isClosed = true;
                }
            }

            $lateDays++;
            if (!$isClosed) {
                $chargeDays++;
            }

            $current->modify('+1 day');
        }

        // Apply grace period
        $chargeable = max(0, $chargeDays - $graceDays);
        $totalFine = $chargeable * $finePerDay;

        if ($maxFine > 0 && $totalFine > $maxFine) {
            $totalFine = $maxFine;
        }

        return [
            'late_days'   => $lateDays,
            'charge_days' => $chargeDays,
            'fine_amount' => $totalFine,
        ];
    }
}

if (!function_exists('calculate_suggested_due_date')) {
    /**
     * Calculate suggested due date based on duration and member gender schedule
     */
    function calculate_suggested_due_date(array $member, ?string $startDate = null): string
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->getAllLibrarySettings();

        $duration = (int)($settings['default_loan_duration'] ?? 7);
        $femaleDays = array_map('intval', explode(',', $settings['female_open_days'] ?? '6,0,1'));
        $maleDays = array_map('intval', explode(',', $settings['male_open_days'] ?? '2,3,4'));
        $generalClosedDays = array_map('intval', explode(',', $settings['general_closed_days'] ?? '5'));
        $applyTypes = array_map('trim', explode(',', strtolower($settings['apply_gender_schedule_to'] ?? 'santri,siswa')));

        $start = $startDate ? new DateTime($startDate) : new DateTime(date('Y-m-d'));
        $due = clone $start;
        $due->modify("+{$duration} days");

        $memberType = strtolower($member['type'] ?? $member['member_type'] ?? 'santri');
        $isGenderRestricted = false;
        foreach ($applyTypes as $type) {
            if ($type !== '' && str_contains($memberType, $type)) {
                $isGenderRestricted = true;
                break;
            }
        }

        $gender = strtoupper($member['gender'] ?? 'L');

        // Adjust due date forward if it lands on a closed day for this member
        $maxAttempts = 14;
        while ($maxAttempts-- > 0) {
            $dayOfWeek = (int)$due->format('w');
            $isClosed = false;

            if (in_array($dayOfWeek, $generalClosedDays)) {
                $isClosed = true;
            } elseif ($isGenderRestricted) {
                if ($gender === 'P' && !in_array($dayOfWeek, $femaleDays)) {
                    $isClosed = true;
                } elseif ($gender === 'L' && !in_array($dayOfWeek, $maleDays)) {
                    $isClosed = true;
                }
            }

            if (!$isClosed) {
                break;
            }
            $due->modify('+1 day');
        }

        return $due->format('Y-m-d');
    }
}
