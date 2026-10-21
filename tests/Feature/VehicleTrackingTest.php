<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use App\Models\VehicleTrackingEvent;
use App\Services\Crm\CrmClient;
use App\Services\Crm\CrmRecord;
use App\Services\VehicleTracking;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class VehicleTrackingTest extends TestCase
{
    use DatabaseTransactions;

    private User $customer;
    private Admin $admin;
    private array $record;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = new User();
        $this->customer->forceFill([
            'first_name' => 'Tracking', 'last_name' => 'Test', 'phone' => '000',
            'email' => Str::uuid().'@example.com', 'crm_email' => 'tracking-test@example.com',
            'password' => bcrypt('test-password'), 'role' => 'customer',
        ])->save();
        $this->admin = new Admin();
        $this->admin->forceFill([
            'first_name' => 'Tracking Admin', 'email' => Str::uuid().'@example.com',
            'is_system_admin' => 1, 'is_active' => 1,
        ])->save();
        $this->record = ['customer' => ['name' => 'Tracking Customer'], 'invoices' => [[
            'id' => 'trackinginvoice', 'number' => 'TRACK-001', 'unit' => 'Test Car',
            'status' => 'approved', 'cnfPrice' => 1000, 'paid' => 250,
            'vehicles' => [
                ['id' => 'car-one', 'unit' => 'Car One', 'chassisNo' => 'CHASSIS-ONE'],
                ['id' => 'car-two', 'unit' => 'Car Two', 'chassisNo' => 'CHASSIS-TWO'],
            ],
        ]]];
        $this->mock(CrmClient::class, function ($mock) {
            $mock->shouldReceive('customerRecord')->with('tracking-test@example.com', true)->andReturn($this->record)->byDefault();
            $mock->shouldReceive('customerRecord')->with('tracking-test@example.com')->andReturn($this->record)->byDefault();
            $mock->shouldReceive('customerRecord')->with('tracking-test@example.com', false)->andReturn($this->record)->byDefault();
        });
    }

    private function payload(array $extra = []): array
    {
        return array_replace([
            'invoice_id' => 'trackinginvoice',
            'vehicle_key' => VehicleTracking::vehicles($this->record['invoices'][0])[0]['key'],
            'stage' => 'in_yard', 'occurred_on' => now()->toDateString(),
            'note' => 'Arrived safely <script>alert(1)</script>',
        ], $extra);
    }

    private function url(): string
    {
        return route('admin.users.edit.vehicle-tracking', $this->customer->id);
    }

    public function test_admin_can_update_correct_and_reset_a_car_with_history(): void
    {
        $this->actingAs($this->admin, 'admin');
        foreach (['in_yard', 'final_payment', 'reserved', 'not_started'] as $stage) {
            $this->post($this->url(), $this->payload(['stage' => $stage, 'occurred_on' => null]))
                ->assertRedirect(route('admin.users.show', $this->customer->id))->assertSessionHasNoErrors();
        }
        $cars = VehicleTracking::forUser($this->customer, $this->record)['trackinginvoice'];
        $this->assertCount(4, $cars[0]['events']);
        $this->assertSame('not_started', $cars[0]['events']->first()->stage);
        $this->assertSame($this->admin->id, $cars[0]['events']->first()->admin_id);
        $this->assertCount(0, $cars[1]['events']);
        $this->record['invoices'][0]['vehicles'] = array_reverse($this->record['invoices'][0]['vehicles']);
        $reordered = VehicleTracking::forUser($this->customer, $this->record)['trackinginvoice'];
        $this->assertCount(4, $reordered[1]['events']);
        $this->assertCount(0, $reordered[0]['events']);
    }

    public function test_customer_sees_progress_but_no_edit_controls_or_staff_identity(): void
    {
        $this->actingAs($this->admin, 'admin')->post($this->url(), $this->payload())->assertSessionHasNoErrors();
        foreach (['admin', 'customer'] as $audience) {
            $view = $this->view('partials.crm.record', ['crm' => [
                'record' => CrmRecord::forAudience($this->record, $audience === 'customer'),
                'audience' => $audience,
                'fileUrl' => fn () => '/file', 'pdfUrl' => fn () => '/pdf',
                'tracking' => VehicleTracking::forUser($this->customer, $this->record),
                'trackingUrl' => $this->url(),
            ]]);
            $view->assertSeeInOrder(['Inspection/Yard', 'In Yard', 'Final Payment'])->assertSee('aria-current="step"', false)
                ->assertSee('Arrived safely <script>alert(1)</script>')
                ->assertDontSee('<script>alert(1)</script>', false);
            if ($audience === 'admin') {
                $view->assertSee('Update Progress')->assertSee('Tracking Admin');
            } else {
                $view->assertDontSee('Update Progress')->assertDontSee('Tracking Admin')->assertDontSee('name="stage"', false);
            }
        }
    }

    public function test_other_customers_and_changed_crm_links_do_not_receive_history(): void
    {
        $this->actingAs($this->admin, 'admin')->post($this->url(), $this->payload())->assertSessionHasNoErrors();
        $other = clone $this->customer;
        $other->id = $this->customer->id + 100000;
        $this->assertCount(0, VehicleTracking::forUser($other, $this->record)['trackinginvoice'][0]['events']);
        $this->customer->crm_email = 'different@example.com';
        $this->assertCount(0, VehicleTracking::forUser($this->customer, $this->record)['trackinginvoice'][0]['events']);
    }

    public function test_full_admin_and_customer_pages_show_saved_progress(): void
    {
        $this->actingAs($this->admin, 'admin')->post($this->url(), $this->payload())->assertSessionHasNoErrors();
        $this->get(route('admin.users.show', $this->customer->id))
            ->assertOk()->assertSee('Vehicle progress')->assertSee('Update Progress')->assertSee('TRACK-001');
        auth('admin')->logout();
        $this->actingAs($this->customer)->get(route('profile.index'))
            ->assertOk()->assertSee('Vehicle progress')->assertSee('Arrived safely &lt;script&gt;', false)
            ->assertDontSee('Update Progress')->assertSee('TRACK-001');
    }

    public function test_agent_with_edit_permission_can_only_update_assigned_customers(): void
    {
        $role = \App\Models\AdminRole::create(['name' => 'Tracking test', 'slug' => 'tracking-'.Str::uuid()]);
        $permission = \App\Models\AdminPermission::where('name', 'users.edit')->firstOrFail();
        $role->permissions()->attach($permission);
        $this->admin->forceFill(['is_system_admin' => 0, 'admin_role_id' => $role->id])->save();
        $this->actingAs($this->admin, 'admin')->post($this->url(), $this->payload())->assertNotFound();
        \App\Models\CustomerAgent::assign($this->customer, $this->admin->email, 'panel');
        $this->post($this->url(), $this->payload())->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.users.show', $this->customer->id));
    }

    public function test_vehicle_identity_survives_single_to_multiple_car_changes(): void
    {
        $invoice = $this->record['invoices'][0];
        $single = $invoice;
        $single['vehicles'] = [$invoice['vehicles'][0]];
        $this->assertSame(VehicleTracking::vehicles($single)[0]['key'], VehicleTracking::vehicles($invoice)[0]['key']);
    }

    public function test_invalid_or_foreign_vehicles_cannot_be_updated(): void
    {
        $this->actingAs($this->admin, 'admin');
        $this->post($this->url(), $this->payload(['invoice_id' => 'someone-elses-invoice']))->assertNotFound();
        $this->post($this->url(), $this->payload(['vehicle_key' => str_repeat('a', 64)]))->assertNotFound();
        $this->post($this->url(), $this->payload(['stage' => 'invalid']))->assertSessionHasErrors('stage');
        $this->post($this->url(), $this->payload(['occurred_on' => now()->addDay()->toDateString()]))->assertSessionHasErrors('occurred_on');
        $this->assertSame(0, VehicleTrackingEvent::where('user_id', $this->customer->id)->count());
    }

    public function test_customer_and_unprivileged_admin_cannot_write_progress(): void
    {
        $this->actingAs($this->customer)->post($this->url(), $this->payload())->assertRedirect();
        $this->admin->is_system_admin = 0;
        $this->admin->save();
        $this->actingAs($this->admin, 'admin')->post($this->url(), $this->payload())->assertForbidden();
        $this->assertSame(0, VehicleTrackingEvent::where('user_id', $this->customer->id)->count());
    }

    public function test_crm_outage_does_not_write_progress(): void
    {
        $this->mock(CrmClient::class, function ($mock) {
            $mock->shouldReceive('customerRecord')->once()->andReturn(null);
        });
        $this->actingAs($this->admin, 'admin')->post($this->url(), $this->payload())->assertSessionHasErrors('tracking');
        $this->assertSame(0, VehicleTrackingEvent::where('user_id', $this->customer->id)->count());
    }

    public function test_ambiguous_multi_car_records_cannot_be_tracked(): void
    {
        $cars = VehicleTracking::vehicles(['vehicles' => [['unit' => 'Same'], ['unit' => 'Same']]]);
        $this->assertNull($cars[0]['key']);
        $this->assertNull($cars[1]['key']);
        $cars = VehicleTracking::vehicles(['vehicles' => [['id' => 'duplicate'], ['id' => 'duplicate']]]);
        $this->assertNull($cars[0]['key']);
        $this->assertNull($cars[1]['key']);
    }
}
