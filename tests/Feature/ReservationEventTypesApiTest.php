<?php

namespace Tests\Feature;

use App\Models\ReservationEventType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationEventTypesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_event_types_endpoint_returns_only_active_types_in_correct_order(): void
    {
        ReservationEventType::create([
            'name_en' => 'Wedding',
            'name_hu' => 'Esküvő',
            'name_sr' => 'Venčanje',
            'name_sr_cyrl' => 'Венчање',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        ReservationEventType::create([
            'name_en' => 'Birthday',
            'name_hu' => 'Születésnap',
            'name_sr' => 'Rođendan',
            'name_sr_cyrl' => 'Рођендан',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        ReservationEventType::create([
            'name_en' => 'Inactive Event',
            'name_hu' => 'Inaktív esemény',
            'name_sr' => 'Neaktivan događaj',
            'name_sr_cyrl' => 'Неактиван догађај',
            'is_active' => false,
            'sort_order' => 0,
        ]);

        $response = $this->getJson('/api/reservation-event-types');

        $response
            ->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonPath('0.name_en', 'Birthday')
            ->assertJsonPath('0.name_hu', 'Születésnap')
            ->assertJsonPath('0.name_sr', 'Rođendan')
            ->assertJsonPath('0.name_sr_cyrl', 'Рођендан')
            ->assertJsonPath('1.name_en', 'Wedding')
            ->assertJsonPath('1.name_hu', 'Esküvő')
            ->assertJsonPath('1.name_sr', 'Venčanje')
            ->assertJsonPath('1.name_sr_cyrl', 'Венчање');

        $response->assertJsonMissing([
            'name_en' => 'Inactive Event',
        ]);
    }
}
