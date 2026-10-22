<?php

namespace App\Services;

use App\Models\User;
use App\Models\VehicleTrackingEvent;
use Illuminate\Support\Facades\Schema;

class VehicleTracking
{
    public static function ready(): bool
    {
        return Schema::hasTable('vehicle_tracking_events');
    }

    /** Stable CRM identifiers, never the order of cars in the CRM response. */
    public static function vehicles(array $invoice): array
    {
        $vehicles = $invoice['vehicles'] ?? [];
        if (!$vehicles) {
            $vehicles = $invoice['units'] ?? [];
        }
        if (!$vehicles) {
            $vehicles = [$invoice];
        }
        $result = [];
        foreach ($vehicles as $vehicle) {
            $chassis = trim((string) ($vehicle['chassisNo'] ?? $vehicle['chassis'] ?? ''));
            $id = (string) ($vehicle['id'] ?? '');
            $label = trim((string) ($vehicle['unit'] ?? '')).
                ' '.trim(($vehicle['make'] ?? '').' '.($vehicle['model'] ?? ''));
            $label = trim($label) ?: ($invoice['unit'] ?? 'Vehicle');
            $identity = $id !== '' ? 'id:'.$id : ($chassis !== '' ? 'chassis:'.strtoupper($chassis) : null);
            $key = $identity === null ? null : hash('sha256', $identity);
            $result[] = ['key' => $key, 'label' => $label ?: 'Vehicle', 'chassis' => $chassis];
        }
        $counts = array_count_values(array_filter(array_column($result, 'key')));
        foreach ($result as &$vehicle) {
            if ($vehicle['key'] && $counts[$vehicle['key']] > 1) {
                $vehicle['key'] = null;
            }
        }
        return $result;
    }

    public static function forUser(User $user, ?array $record): array
    {
        if (!$record || !self::ready()) {
            return [];
        }
        $events = VehicleTrackingEvent::where('user_id', $user->id)
            ->where('crm_email', strtolower(trim($user->crm_email)))
            ->whereIn('invoice_id', array_column($record['invoices'] ?? [], 'id'))
            ->orderByDesc('id')->get()->groupBy('invoice_id');
        $result = [];
        foreach ($record['invoices'] ?? [] as $invoice) {
            $cars = self::vehicles($invoice);
            foreach ($cars as &$car) {
                $car['events'] = ($events->get($invoice['id']) ?? collect())
                    ->where('vehicle_key', $car['key'])->values();
            }
            unset($car);
            $result[$invoice['id']] = $cars;
        }
        return $result;
    }
}
