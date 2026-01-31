<section class="card bg-base-100 shadow-xl grow"
         x-data="{
            incomesInCents: @entangle('incomesInCents'),
            storageKey: 'accounts-list-incomes-{{ $household->id }}',
            init() {
                let savedIncomes = localStorage.getItem(this.storageKey);
                if (savedIncomes && (!this.incomesInCents || Object.keys(this.incomesInCents).length === 0)) {
                    try {
                        $wire.initIncomes(JSON.parse(savedIncomes), true);
                    } catch (e) {
                        console.error('Failed to parse saved incomes', e);
                    }
                }

                this.$watch('incomesInCents', value => {
                    if (value) {
                        localStorage.setItem(this.storageKey, JSON.stringify(value));
                    }
                });
            }
         }"
>
    <div class="card-body">
        <h2 class="card-title">
            Membres
        </h2>
        <table class="table table-zebra">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Revenu</th>
                <th>Ratio</th>
            </tr>
            </thead>
            <tbody>
            @foreach($members as $member)
                <tr wire:key="member-{{ $member->id }}">
                    <td>
                        <label class="cursor-pointer" for="income-{{ $member->id }}">
                            {{ $member->full_name }}
                        </label>
                    </td>
                    <td>
                        <input type="text" class="input w-30" wire:model.blur="incomes.{{ $member->id }}"
                               id="income-{{ $member->id }}"
                               aria-label="Montant du revenu de {{ $member->full_name }}"/>
                    </td>
                    <td>
                        {{ $this->ratioForMember($member->id)  }}
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td>Total</td>
                <td>
                    {{ $this->totalIncomes?->toCurrency() ?? "-" }}
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</section>
