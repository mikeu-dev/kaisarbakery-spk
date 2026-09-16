<?php

use App\Filament\Pages\PerhitunganTopsisPage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('perhitungan topsis page can render and has tabs components', function () {
    $this->seed(DatabaseSeeder::class);

    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(PerhitunganTopsisPage::class)
        ->assertSuccessful()
        ->assertSee('Kalkulasi & Matriks TOPSIS')
        ->assertSee('1. Perangkingan Akhir (Vi)')
        ->assertSee('2. Matriks Keputusan (X)')
        ->assertSee('3. Normalisasi Matriks (R)')
        ->assertSee('4. Matriks Terbobot (Y)')
        ->assertSee('5. Solusi Ideal (A+ / A-)')
        ->assertSee('6. Jarak Euclidean (D+ / D-)');

    $page = app(PerhitunganTopsisPage::class);
    $schema = $page->content(Schema::make($page));
    $tabsComponent = $schema->getComponents()[0] ?? null;

    expect($tabsComponent)->toBeInstanceOf(Tabs::class)
        ->and($tabsComponent->getChildComponents())->toHaveCount(6)
        ->and($tabsComponent->getChildComponents()[0]->getLabel())->toContain('Perangkingan Akhir');
});
