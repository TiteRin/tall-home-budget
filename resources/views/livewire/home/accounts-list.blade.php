<section class="card bg-base-100 shadow-xl grow">
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
                <fieldset>
                    <tr>
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
                </fieldset>
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
