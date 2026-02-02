<section class="card bg-base-100 shadow-xl grow w-1/3">
    <div class="card-body">
        <h2 class="card-title mb-4">
            Charges et Dépenses
        </h2>
        <table class="table table-zebra">
            <thead>
            <tr>
                <th>Charge</th>
                <th>Montant</th>
            </tr>
            </thead>
            <tbody>
            @if (count($bills) > 0)
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
            @else
                <tr>
                    <td colspan="2">
                        <a href="{{ route('bills.settings') }}" class="btn btn-primary">
                            Paramétrer les charges
                        </a>
                    </td>
                </tr>
            @endif

            @if (count($this->expenseTabs) > 0)
                @foreach($this->expenseTabs as $tab)
                    <tr>
                        <td>
                            <a href="{{ route('expense-tabs.index', ['tab' => $tab->id]) }}">
                                {{ $tab->name }}
                            </a>
                        </td>
                        <td>

                        </td>
                    </tr>
                @endforeach
            @endif

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
