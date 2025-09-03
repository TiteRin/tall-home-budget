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
            </tr>
            </thead>
            <tbody>
            @foreach($members as $member)
                <tr>
                    <td>{{ $member->full_name }}</td>
                    <td>
                        <input type="text" class="input" wire:model.blur="incomes.{{ $member->id }}"/>
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
