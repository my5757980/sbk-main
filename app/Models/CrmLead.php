<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * A CRM customer with no email in the CRM: their invoices are listed by the
 * CRM lead (the CRM's own customer record) instead of by email. They have no
 * website login - that needs an email - but their agent sees them and their
 * whole record. Kept in step with the CRM by CrmCustomerSync; when the CRM
 * gets an email for them they become an ordinary website account instead.
 */
class CrmLead extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_invoice_at' => 'datetime',
        'total' => 'float',
        'paid' => 'float',
        'balance' => 'float',
    ];

    private static ?bool $ready = null;

    /** False until the migration has run on this server. */
    public static function ready(): bool
    {
        return self::$ready ??= Schema::hasTable('crm_leads');
    }

    /** Everyone's for "See all customers"; otherwise only the admin's own (by email, like CustomerAgent). */
    public function scopeVisibleTo($query, ?Admin $admin)
    {
        if ($admin && $admin->canSeeAllCustomers()) {
            return $query;
        }

        if (!$admin) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('agent_email', CustomerAgent::normalize($admin->email));
    }
}
