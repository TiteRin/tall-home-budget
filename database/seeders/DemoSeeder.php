<?php

namespace Database\Seeders;

use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Models\Member;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Création du foyer
        $household = Household::factory()->create([
            'name' => 'Duck Mansion',
        ]);

        // Création des membres (Guidelines: Huwey, Dewey, Louie)
        $huwey = Member::factory()->create(['first_name' => 'Huwey', 'last_name' => 'Duck', 'household_id' => $household->id]);
        $dewey = Member::factory()->create(['first_name' => 'Dewey', 'last_name' => 'Duck', 'household_id' => $household->id]);
        $louie = Member::factory()->create(['first_name' => 'Louie', 'last_name' => 'Duck', 'household_id' => $household->id]);

        // Création de l'utilisateur démo
        User::factory()->create([
            'email' => 'demo@titerin.ovh',
            'password' => Hash::make('demo'),
            'member_id' => $huwey->id,
        ]);

        $members = collect([$huwey, $dewey, $louie]);

        // Création des factures (bills)
        Bill::factory()->create([
            'name' => 'Loyer',
            'amount' => 120000, // 1200.00
            'distribution_method' => DistributionMethod::PRORATA,
            'household_id' => $household->id,
        ]);

        Bill::factory()->create([
            'name' => 'Électricité',
            'amount' => 8500, // 85.00
            'distribution_method' => DistributionMethod::PRORATA,
            'household_id' => $household->id,
        ]);

        Bill::factory()->create([
            'name' => 'Internet',
            'amount' => 3999, // 39.99
            'distribution_method' => DistributionMethod::EQUAL,
            'household_id' => $household->id,
        ]);

        Bill::factory()->create([
            'name' => 'Netflix',
            'amount' => 1799, // 17.99
            'distribution_method' => DistributionMethod::EQUAL,
            'household_id' => $household->id,
            'member_id' => $huwey->id,
        ]);

        // Création de l'onglet de dépenses "Test"
        $expenseTab = ExpenseTab::factory()->create([
            'name' => 'Test',
            'household_id' => $household->id,
            'from_day' => 1,
        ]);

        $now = CarbonImmutable::now();

        // Génération d'une quarantaine de dépenses
        // On va en générer sur 4 mois pour avoir une vue d'ensemble (M, M-1, M-2, M+1 pour les dépenses à venir)

        // Dépenses du mois en cours (environ 15)
        for ($i = 0; $i < 15; $i++) {
            Expense::factory()->create([
                'expense_tab_id' => $expenseTab->id,
                'member_id' => $members->random()->id,
                'spent_on' => $now->startOfMonth()->addDays(rand(0, $now->daysInMonth - 1)),
            ]);
        }

        // Dépenses du mois précédent (environ 10)
        for ($i = 0; $i < 10; $i++) {
            Expense::factory()->create([
                'expense_tab_id' => $expenseTab->id,
                'member_id' => $members->random()->id,
                'spent_on' => $now->subMonth()->startOfMonth()->addDays(rand(0, $now->subMonth()->daysInMonth - 1)),
            ]);
        }

        // Dépenses de deux mois avant (environ 10)
        for ($i = 0; $i < 10; $i++) {
            Expense::factory()->create([
                'expense_tab_id' => $expenseTab->id,
                'member_id' => $members->random()->id,
                'spent_on' => $now->subMonths(2)->startOfMonth()->addDays(rand(0, $now->subMonths(2)->daysInMonth - 1)),
            ]);
        }

        // Dépenses à venir (environ 5)
        for ($i = 0; $i < 5; $i++) {
            Expense::factory()->create([
                'expense_tab_id' => $expenseTab->id,
                'member_id' => $members->random()->id,
                'spent_on' => $now->addMonth()->startOfMonth()->addDays(rand(0, 10)), // Dépenses prévues début de mois prochain
            ]);
        }
    }
}
