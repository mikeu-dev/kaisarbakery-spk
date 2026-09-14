<?php

use App\Filament\Pages\PerhitunganTopsisPage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('perhitungan topsis page can render and has subnavigation items', function () {
    $this->seed(DatabaseSeeder::class);

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(PerhitunganTopsisPage::class)
        ->assertSuccessful();

    $page = app(PerhitunganTopsisPage::class);
    $subNavigation = $page->getSubNavigation();

    expect($subNavigation)->toHaveCount(6)
        ->and($subNavigation[0]->getLabel())->toContain('Perangkingan Akhir');
});
