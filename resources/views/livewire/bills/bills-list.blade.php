<section class="card bg-base-100 shadow-xl grow w-1/3">
    <div class="card-body">
        <h2 class="card-title mb-4">
            Dépenses
        </h2>
        <table class="table table-zebra">
            <thead>
            <tr>
                <th>Dépense</th>
                <th>Montant</th>
            </tr>
            </thead>
            <tbody>
            @foreach($bills as $bill)
                <tr>
                    <td>
                        {{ $bill->name }}
                    </td>
                    <td>
                        {{ $bill->amount->toCurrency() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td>Total</td>
                <td>{{ $this->totalAmount() }}</td>
            </tr>
            </tfoot>
        </table>
    </div>
</section>
